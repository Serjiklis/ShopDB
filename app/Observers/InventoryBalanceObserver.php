<?php

namespace App\Observers;

use App\Models\InventoryBalance;

class InventoryBalanceObserver
{
    /**
     * Handle the InventoryBalance "created" event.
     */
    public function created(InventoryBalance $inventoryBalance): void
    {
        //
    }

    /**
     * Handle the InventoryBalance "updated" event.
     */
    public function updating(InventoryBalance $inventoryBalance): void
    {
        // Если новый StockCount < 0 => выставляем discrepancy = true
        if ($inventoryBalance->StockCount < 0) {
            $inventoryBalance->is_discrepancy = true;
        } else {
            // Если снова >= 0, можно сбросить discrepancy.
            $inventoryBalance->is_discrepancy = false;
        }
    }

    /**
     * Handle the InventoryBalance "deleted" event.
     */
    public function deleted(InventoryBalance $inventoryBalance): void
    {
        //
    }

    /**
     * Handle the InventoryBalance "restored" event.
     */
    public function restored(InventoryBalance $inventoryBalance): void
    {
        //
    }

    /**
     * Handle the InventoryBalance "force deleted" event.
     */
    public function forceDeleted(InventoryBalance $inventoryBalance): void
    {
        //
    }
}
