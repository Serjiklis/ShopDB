<?php

namespace App\Providers;

use App\Models\Sale;
use App\Models\Supply;
use App\Observers\SaleObserver;
use App\Observers\SupplyObserver;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sale::observe(SaleObserver::class);
        Supply::observe(SupplyObserver::class);
    }
}
