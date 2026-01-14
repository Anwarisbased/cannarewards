<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('e.g., dime')
                    ->helperText('This will be used as the subdomain (dime.rewards.io)'),

                Forms\Components\TextInput::make('brand_name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Dime Industries')
                    ->dehydrated(true), // Ensure it's included in the form data

                Forms\Components\Select::make('plan')
                    ->options([
                        'starter' => 'Starter',
                        'growth' => 'Growth',
                        'enterprise' => 'Enterprise',
                    ])
                    ->default('enterprise')
                    ->required()
                    ->dehydrated(true), // Ensure it's included in the form data

                Forms\Components\Section::make('Brand Configuration')
                    ->description('Customize the look and feel for this tenant')
                    ->schema([
                        Forms\Components\Fieldset::make('Theme Settings')
                            ->schema([
                                Forms\Components\ColorPicker::make('config.theme.primary_color')
                                    ->label('Primary Color')
                                    ->rgb(),

                                Forms\Components\Select::make('config.theme.font_family')
                                    ->label('Font Family')
                                    ->options([
                                        'Inter' => 'Inter',
                                        'Poppins' => 'Poppins',
                                        'Roboto' => 'Roboto',
                                        'Open Sans' => 'Open Sans',
                                    ])
                                    ->default('Inter'),

                                Forms\Components\TextInput::make('config.theme.radius')
                                    ->label('Radius')
                                    ->default('0.5rem')
                                    ->helperText('Border radius value (e.g., 0.5rem)'),
                            ]),

                        Forms\Components\Fieldset::make('Copy Settings')
                            ->schema([
                                Forms\Components\TextInput::make('config.copy.points_label')
                                    ->label('Points Label')
                                    ->default('Points')
                                    ->helperText('What to call the points in the UI'),

                                Forms\Components\TextInput::make('config.copy.scan_cta')
                                    ->label('Scan CTA')
                                    ->default('Claim Points')
                                    ->helperText('Call-to-action text for scanning'),
                            ]),

                        Forms\Components\Fieldset::make('Feature Flags')
                            ->schema([
                                Forms\Components\Toggle::make('config.features.referrals_enabled')
                                    ->label('Referrals Enabled')
                                    ->default(true),

                                Forms\Components\Toggle::make('config.features.age_gate_strict')
                                    ->label('Strict Age Gate')
                                    ->default(true),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan')
                    ->options([
                        'starter' => 'Starter',
                        'growth' => 'Growth',
                        'enterprise' => 'Enterprise',
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
