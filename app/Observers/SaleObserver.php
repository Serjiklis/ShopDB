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
        // Смотрим, изменилось ли количество
        $originalQuantity = $sale->getOriginal('QuantitySold'); // до обновления
        $newQuantity = $sale->QuantitySold;                     // после обновления

        // Если количество не менялось, ничего не делаем
        if ($originalQuantity === $newQuantity) {
            return;
        }

        // Ищем остаток
        $inventory = InventoryBalance::where('Article', $sale->Article)->first();
        if (! $inventory) {
            return;
        }

        // Считаем разницу
        $difference = $newQuantity - $originalQuantity;
        // Если difference > 0, значит продали больше, нужно ещё списать
        // Если difference < 0, значит продали меньше, нужно вернуть на склад
        $inventory->StockCount -= $difference;
        // min(10 - (difference=2)) => 8
        // min(10 - (difference=-2)) => 12

        $inventory->save();

    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        // Возвращаем на склад всё проданное по этой продаже
        $inventory = InventoryBalance::where('Article', $sale->Article)->first();
        if (! $inventory) {
            return;
        }

        // Раз удаляем продажу, значит нужно "вернуть" товар на склад
        $inventory->StockCount += $sale->QuantitySold;
        $inventory->save();
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
