<?php

namespace App\Observers;

use App\Models\InventoryBalance;
use App\Models\Supply;

class SupplyObserver
{
    /**
     * Handle the Supply "created" event.
     */
    public function created(Supply $supply): void
    {
        // Ищем или создаём запись об остатках по тому же артикулу
        // (если запись не найдена, создаём с начальным StockCount=0)
        $inventory = InventoryBalance::firstOrCreate(
            ['Article' => $supply->article],
            ['StockCount' => 0]
        );

        // Увеличиваем остаток на quantity, полученное в поставке
        $inventory->StockCount += $supply->quantity;

        // Сохраняем обновлённую запись
        $inventory->save();
    }

    /**
     * Handle the Supply "updated" event.
     */
    public function updated(Supply $supply): void
    {
        //
    }

    /**
     * Handle the Supply "deleted" event.
     */
    public function deleted(Supply $supply): void
    {
        //
    }

    /**
     * Handle the Supply "restored" event.
     */
    public function restored(Supply $supply): void
    {
        //
    }

    /**
     * Handle the Supply "force deleted" event.
     */
    public function forceDeleted(Supply $supply): void
    {
        //
    }
}
