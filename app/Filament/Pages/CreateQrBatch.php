<?php

namespace App\Filament\Pages;

use App\Actions\Tenant\GenerateQrBatchAction;
use App\Models\Tenant;
use App\Models\Tenant\CommercialGood;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class CreateQrBatch extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static string $view = 'filament.pages.create-qr-batch';

    protected static ?string $navigationLabel = 'Generate QR Batch';

    protected static ?string $title = 'Generate QR Code Batch';

    public ?string $selectedTenantId = null;

    public ?int $productId = null;

    public ?int $quantity = null;

    public ?string $batchLabel = null;

    public function mount(): void
    {
        // Initialize with a default batch label
        $this->batchLabel = 'BATCH-'.strtoupper(Str::random(8));
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('selectedTenantId')
                ->label('Select Tenant')
                ->options(function () {
                    // Fetch all tenants from the central database
                    return Tenant::query()->pluck('brand_name', 'id')->toArray();
                })
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    // Reset product selection when tenant changes
                    $set('productId', null);
                })
                ->helperText('Choose the tenant for which to generate QR codes'),

            Select::make('productId')
                ->label('Product')
                ->options(function () {
                    if (! $this->selectedTenantId) {
                        return [];
                    }

                    // Switch to the selected tenant's context temporarily to fetch products
                    $tenant = Tenant::query()->find($this->selectedTenantId);
                    if (! $tenant) {
                        return [];
                    }

                    $products = null;
                    $tenantId = $tenant->getTenantKey();
                    $tenantIds = collect([$tenantId]);
                    \tenancy()->runForMultiple($tenantIds, function () use (&$products) {
                        $products = CommercialGood::query()->pluck('name', 'id')->toArray();
                    });

                    return $products ?? [];
                })
                ->required()
                ->disabled(! $this->selectedTenantId)
                ->helperText('Select the product for which to generate QR codes'),

            TextInput::make('quantity')
                ->label('Quantity')
                ->numeric()
                ->minValue(1)
                ->maxValue(100000)
                ->required()
                ->placeholder('Enter number of QR codes to generate')
                ->helperText('Number of unique QR codes to generate (max 100,000)'),

            TextInput::make('batchLabel')
                ->label('Batch Label')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g., OCT2025_RUN1')
                ->helperText('Label for identifying this batch of QR codes'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate QR Batch')
                ->submit('generate')
                ->color('primary'),
        ];
    }

    public function generate(): void
    {
        $data = $this->validate([
            'selectedTenantId' => ['required', 'string', 'exists:central.tenants,id'],
            'productId' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'batchLabel' => ['required', 'string', 'max:255'],
        ]);

        try {
            // Switch to the selected tenant's context to execute the action
            $tenant = Tenant::query()->find($data['selectedTenantId']);
            if (! $tenant) {
                throw new \Exception('Selected tenant not found');
            }

            // Execute the batch generation in the tenant's context
            $filePath = null;
            $error = null;

            $tenantId = $tenant->getTenantKey();
            $tenantIds = collect([$tenantId]);
            \tenancy()->runForMultiple($tenantIds, function () use ($data, &$filePath, &$error) {
                try {
                    $product = CommercialGood::query()->findOrFail($data['productId']);

                    $action = new GenerateQrBatchAction;
                    $filePath = $action->execute($product, $data['quantity'], $data['batchLabel']);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            });

            if ($error) {
                throw new \Exception($error);
            }

            Notification::make()
                ->title('QR Batch Generated Successfully!')
                ->body("File saved to: {$filePath}")
                ->success()
                ->send();

            // Reset form
            $this->batchLabel = 'BATCH-'.strtoupper(Str::random(8));
            $this->quantity = null;
            $this->selectedTenantId = null;
            $this->productId = null;

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Generating QR Batch')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
