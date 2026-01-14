<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommercialGoodResource\Pages;
use App\Models\Tenant\CommercialGood;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommercialGoodResource extends Resource
{
    protected static ?string $model = CommercialGood::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Commercial Goods';

    protected static ?string $pluralModelLabel = 'Commercial Goods';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->description('Basic information about the commercial good')
                    ->schema([
                        Forms\Components\TextInput::make('sku')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., DIME-OG-1G')
                            ->helperText('Unique identifier for the product in your ERP system'),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Dime OG Kush')
                            ->helperText('Display name for the product'),

                        Forms\Components\TextInput::make('points_awarded')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('e.g., 100')
                            ->helperText('Base points value awarded when this product is scanned'),

                        Forms\Components\TextInput::make('msrp_cents')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('e.g., 15000')
                            ->helperText('Manufacturer suggested retail price in cents for ROI calculations'),

                        Forms\Components\Select::make('strain_type')
                            ->options([
                                'indica' => 'Indica',
                                'sativa' => 'Sativa',
                                'hybrid' => 'Hybrid',
                                'cbd' => 'CBD',
                            ])
                            ->required()
                            ->helperText('Type of cannabis strain for affinity calculations'),

                        Forms\Components\TextInput::make('image_url')
                            ->maxLength(255)
                            ->placeholder('https://example.com/image.jpg')
                            ->helperText('URL to the product image stored on S3'),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Toggle to enable/disable product availability'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_awarded')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('msrp_cents')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('strain_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'indica' => 'warning',
                        'sativa' => 'success',
                        'hybrid' => 'info',
                        'cbd' => 'secondary',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->placeholder('All products')
                    ->trueLabel('Active products')
                    ->falseLabel('Inactive products'),

                Tables\Filters\SelectFilter::make('strain_type')
                    ->options([
                        'indica' => 'Indica',
                        'sativa' => 'Sativa',
                        'hybrid' => 'Hybrid',
                        'cbd' => 'CBD',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommercialGoods::route('/'),
            'create' => Pages\CreateCommercialGood::route('/create'),
            'edit' => Pages\EditCommercialGood::route('/{record}/edit'),
        ];
    }
}
