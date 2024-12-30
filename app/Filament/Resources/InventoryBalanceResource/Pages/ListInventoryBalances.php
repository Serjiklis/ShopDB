<?php

namespace App\Filament\Resources\InventoryBalanceResource\Pages;

use App\Filament\Resources\InventoryBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventoryBalances extends ListRecords
{
    protected static string $resource = InventoryBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
