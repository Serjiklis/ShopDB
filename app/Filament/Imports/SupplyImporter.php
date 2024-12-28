<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\Supply;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SupplyImporter extends Importer
{
    protected static ?string $model = Supply::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('date')
                ->label('Дата')
                ->rules(['required', 'date']),
            ImportColumn::make('invoice_number')
                ->label('Номер счета')
                ->rules(['required', 'string', 'max:50']),
            ImportColumn::make('article')
                ->label('Артикул')
                ->rules(['required', 'string', 'max:50'])
                ->fillRecordUsing(function ($record, $state) {
                    // Проверяем, существует ли артикул в таблице products
                    if (!Product::where('article', $state)->exists()) {
                        throw new RowImportFailedException("Артикул '$state' не найден в таблице products.");
                    }

                    $record->article = $state; // Заполняем значение в записи
                }),
            ImportColumn::make('quantity')
                ->label('Количество')
                ->rules(['required', 'integer', 'min:0']),
            ImportColumn::make('price')
                ->label('Цена')
                ->numeric()
                ->rules(['required', 'numeric', 'min:0']),
        ];
    }

    public function resolveRecord(): ?Supply
    {
        return Supply::firstOrNew([
            'date' => $this->data['date'],
            'invoice_number' => $this->data['invoice_number'],
            'article' => $this->data['article'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your supply import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
