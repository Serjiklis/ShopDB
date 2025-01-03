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
        $originalQuantity = $supply->getOriginal('quantity');
        $newQuantity = $supply->quantity;

        if ($originalQuantity === $newQuantity) {
            return;
        }

        $inventory = InventoryBalance::where('Article', $supply->article)->first();
        if (! $inventory) {
            return;
        }

        // Разница: если > 0, значит увеличили количество поставки
        // если < 0, значит уменьшили
        $difference = $newQuantity - $originalQuantity;
        $inventory->StockCount += $difference;

        $inventory->save();
    }

    /**
     * Handle the Supply "deleted" event.
     */
    public function deleted(Supply $supply): void
    {
        $inventory = InventoryBalance::where('Article', $supply->article)->first();
        if (! $inventory) {
            return;
        }

        // Раз удаляем поставку, надо "вычесть" то, что ранее приходили
        $inventory->StockCount -= $supply->quantity;
        $inventory->save();
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
