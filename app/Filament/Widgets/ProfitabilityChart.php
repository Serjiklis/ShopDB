<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ProfitabilityChart extends ChartWidget
{
    protected static ?string $heading = 'Маржинальность по категориям';
    protected static ?int $sort = 3;
    protected function getData(): array
    {
        // Рассчитываем маржинальность по категориям
        $profitData = Product::join('sales', 'products.article', '=', 'sales.Article')
            ->selectRaw('products.category_id, SUM(sales.TotalPrice - (sales.QuantitySold * products.purchase_price)) as profit')
            ->groupBy('products.category_id')
            ->orderBy('profit', 'desc')
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $profitData->pluck('profit')->toArray(), // Значения маржинальности
                ],
            ],
            'labels' => $profitData->pluck('category_id')->map(function ($id) {
                return \App\Models\Category::find($id)?->name ?? 'Неизвестная категория'; // Название категории
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
