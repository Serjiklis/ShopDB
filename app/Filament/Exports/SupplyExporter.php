<?php

namespace App\Filament\Exports;

use App\Models\Supply;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SupplyExporter extends Exporter
{
    protected static ?string $model = Supply::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('date')->label('Дата поставки'),
            ExportColumn::make('invoice_number')->label('Номер счета'),
            ExportColumn::make('article')->label('Артикул'),
            ExportColumn::make('quantity')->label('Количество'),
            ExportColumn::make('price')->label('Цена'),
            ExportColumn::make('product.name')->label('Наименование товара'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your supply export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
