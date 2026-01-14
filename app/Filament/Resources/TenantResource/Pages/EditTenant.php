<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract the fields that should go into the main tenant record
        $brand_name = $data['brand_name'] ?? '';
        $plan = $data['plan'] ?? 'enterprise';
        $config = $data['config'] ?? [];

        // Prepare the data for the tenant record
        // For stancl/tenancy, we need to store custom fields in the 'data' JSON column
        $tenantData = [
            'brand_name' => $brand_name,
            'plan' => $plan,
            'config' => $config,
        ];

        // Return only the data field for the BaseTenant model
        return [
            'data' => $tenantData,
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Extract the fields that should go into the main tenant record
        $brand_name = $data['brand_name'] ?? '';
        $plan = $data['plan'] ?? 'enterprise';
        $config = $data['config'] ?? [];

        // Prepare the data for the tenant record
        // For stancl/tenancy, we need to store custom fields in the 'data' JSON column
        $tenantData = [
            'brand_name' => $brand_name,
            'plan' => $plan,
            'config' => $config,
        ];

        // Update the tenant record with the data
        $record->update([
            'data' => json_encode($tenantData), // Convert array to JSON string
        ]);

        // Update the individual columns directly in the database
        \DB::table('tenants')
            ->where('id', $record->getAttribute('id'))
            ->update([
                'brand_name' => $brand_name,
                'plan' => $plan,
                'config' => json_encode($config), // Convert array to JSON string
                'data' => json_encode($tenantData), // Also update the data column
            ]);

        return $record;
    }
}
