<?php

namespace App\Filament\Resources\InventoryBalanceResource\Pages;

use App\Filament\Resources\InventoryBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryBalance extends EditRecord
{
    protected static string $resource = InventoryBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
