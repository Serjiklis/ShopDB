<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        return [
            Stat::make('Общий объём продаж', $this->getTotalSales())
                ->description('За текущий период')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Общая сумма поставок', $this->getTotalSupplies())
                ->description('За текущий период')
                ->descriptionIcon('heroicon-o-truck'),

            Stat::make('Валовый доход', $this->getGrossProfit())
                ->description('Доход за период')
                ->color('primary'),

            Stat::make('Товары с расхождениями', $this->getDiscrepancyCount())
                ->description('Нужно проверить')
                ->color('danger'),
        ];
    }

    private function getTotalSales(): string
    {
        // Пример логики: извлекаем общую сумму продаж из таблицы sales
        return \App\Models\Sale::sum('TotalPrice') . ' ₽';
    }
    private function getTotalSupplies(): string
    {
        // Пример логики: общая сумма поставок из таблицы supplies
        return \App\Models\Supply::sum('price') . ' ₽';
    }

    private function getGrossProfit(): string
    {
        // Пример расчёта валового дохода: продажи минус поставки
        $totalSales = \App\Models\Sale::sum('TotalPrice');
        $totalSupplies = \App\Models\Supply::sum('price');
        return ($totalSales - $totalSupplies) . ' ₽';
    }

    private function getDiscrepancyCount(): int
    {
        // Считаем товары с расхождениями
        return \App\Models\InventoryBalance::where('is_discrepancy', true)->count();
    }
}
