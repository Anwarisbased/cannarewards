<?php

namespace App\Filament\Resources\CommercialGoodResource\Pages;

use App\Filament\Resources\CommercialGoodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommercialGoods extends ListRecords
{
    protected static string $resource = CommercialGoodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
