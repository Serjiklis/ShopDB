<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DiscrepancyTable;
use App\Filament\Widgets\ProfitabilityChart;
use App\Filament\Widgets\SalesVolumeChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Page;


class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Аналитика';

    protected static string $view = 'filament.pages.analytics';

    public static function getWidgets(): array
    {
        return [
            DiscrepancyTable::class,
            ProfitabilityChart::class,
            SalesVolumeChart::class,
            StatsOverview::class,
        ];
    }
}
