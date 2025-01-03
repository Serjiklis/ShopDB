<?php

namespace App\Observers;

use App\Models\InventoryBalance;
use App\Models\Sale;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        // Ищем запись об остатках по артикулу
        $inventory = InventoryBalance::where('Article', $sale->Article)->first();

        if ($inventory) {
            // Уменьшаем остаток
            $inventory->StockCount -= $sale->QuantitySold;

            // Проверяем, нужно ли обновлять last_calculated_at
            // Если она ещё не установлена (null) или SaleDate больше.
            if ($inventory->last_calculated_at === null
                || $sale->SaleDate->gt($inventory->last_calculated_at)
            ) {
                $inventory->last_calculated_at = $sale->SaleDate;
            }

            $inventory->save();
        }
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "restored" event.
     */
    public function restored(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "force deleted" event.
     */
    public function forceDeleted(Sale $sale): void
    {
        //
    }
}
