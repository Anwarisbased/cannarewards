<?php

namespace App\Filament\Resources\CommercialGoodResource\Pages;

use App\Filament\Resources\CommercialGoodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommercialGood extends EditRecord
{
    protected static string $resource = CommercialGoodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
