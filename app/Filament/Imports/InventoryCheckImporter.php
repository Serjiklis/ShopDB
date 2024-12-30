<?php

namespace App\Filament\Imports;

use App\Models\InventoryCheck;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class InventoryCheckImporter extends Importer
{
    protected static ?string $model = InventoryCheck::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('Article')->label('Article')->rules(['required', 'string', 'max:50']),
            ImportColumn::make('Date')->label('Date')->rules(['required', 'date']),
            ImportColumn::make('CountedStock')->label('Counted Stock')->rules(['required', 'integer']),
        ];
    }

    public function handle(array $data): void
    {
        foreach ($data as $row) {
            InventoryCheck::updateOrCreate(
                [
                    'Article' => $row['Article'],
                    'Date' => $row['Date'],
                ],
                [
                    'CountedStock' => $row['CountedStock'],
                ]
            );
        }
    }

    public function resolveRecord(): ?InventoryCheck
    {
        // return InventoryCheck::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new InventoryCheck();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your inventory check import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
