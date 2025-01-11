<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\Supply;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Query\Builder;
use Carbon\CarbonImmutable;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

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
    private function applyDateFilters($query, $dateColumn): void
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query->when($startDate, fn ($query) => $query->whereDate($dateColumn, '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate($dateColumn, '<=', $endDate));
    }

    private function getTotalSales(): string
    {

        $query = Sale::query();
        $this->applyDateFilters($query, 'SaleDate');

        return $query->sum('TotalPrice') . ' ₽';

    }
    private function getTotalSupplies(): string
    {
        $query = Supply::query();
        $this->applyDateFilters($query, 'date');

        return $query->sum('price') . ' ₽';

    }

    private function getGrossProfit(): string
    {
        $salesQuery = Sale::query();
        $this->applyDateFilters($salesQuery, 'SaleDate');
        $totalSales = $salesQuery->sum('TotalPrice');

        $suppliesQuery = Supply::query();
        $this->applyDateFilters($suppliesQuery, 'date');
        $totalSupplies = $suppliesQuery->sum('price');


        return  $grossProfit = $totalSales - $totalSupplies;
    }

    private function getDiscrepancyCount(): int
    {
        // Считаем товары с расхождениями
        return \App\Models\InventoryBalance::where('is_discrepancy', true)->count();
    }
}
