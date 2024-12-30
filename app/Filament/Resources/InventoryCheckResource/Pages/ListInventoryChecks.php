<?php

namespace App\Filament\Resources\InventoryCheckResource\Pages;

use App\Filament\Resources\InventoryCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventoryChecks extends ListRecords
{
    protected static string $resource = InventoryCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
