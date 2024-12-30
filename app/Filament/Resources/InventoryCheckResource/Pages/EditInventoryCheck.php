<?php

namespace App\Filament\Resources\InventoryCheckResource\Pages;

use App\Filament\Resources\InventoryCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryCheck extends EditRecord
{
    protected static string $resource = InventoryCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
