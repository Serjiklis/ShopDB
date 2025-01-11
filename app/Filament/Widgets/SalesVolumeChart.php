<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;

class SalesVolumeChart extends ChartWidget
{
    protected static ?string $heading = 'Объём продаж';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Группируем продажи по дате и рассчитываем общий объём
        $sales = Sale::selectRaw('DATE(SaleDate) as date, SUM(QuantitySold) as total_sales')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Формируем данные для графика
        return [
            'datasets' => [
                [
                    'label' => 'Объём продаж',
                    'data' => $sales->pluck('total_sales')->toArray(), // Значения объёма продаж
                ],
            ],
            'labels' => $sales->pluck('date')->toArray(), // Метки дат
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
