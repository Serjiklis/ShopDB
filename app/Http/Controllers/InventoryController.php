<?php

namespace App\Http\Controllers;

use App\Models\InventoryCheck;
use App\Models\Supply;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\InventoryBalance;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function calculateSales()
    {
        DB::beginTransaction();

        try {
            // Получаем все инвентаризации, которые ещё не рассчитаны
            $checks = InventoryCheck::where('is_calculated', false)->get();

            foreach ($checks as $check) {
                $product = $check->product;
                if (! $product) {
                    // Пропускаем, если нет связанного товара
                    continue;
                }

                // Текущий учётный остаток
                $inventoryRecord = InventoryBalance::where('Article', $check->Article)->first();
                $currentStock = $inventoryRecord ? $inventoryRecord->StockCount : 0;

                // Фактический остаток по инвентаризации
                $actualStock = $check->CountedStock;

                // Расчёт разницы
                $difference = $currentStock - $actualStock;

                if ($difference > 0) {
                    // Если разница положительная, создаём продажу
                    Sale::create([
                        'Article'      => $check->Article,
                        'SaleDate'     => $check->Date,
                        'QuantitySold' => $difference,
                        'TotalPrice'   => $difference * ($product->retail_price ?? 0),
                    ]);
                }

                // Здесь больше не создаём Supply, даже если разница отрицательная.
                // Просто уходим в минус.

                // Обновляем учётный остаток и флаг discrepancy
                $isDiscrepancy = $actualStock < 0;

                InventoryBalance::updateOrCreate(
                    ['Article' => $check->Article],
                    [
                        'StockCount'     => $actualStock, // Обновляем остаток по инвентаризации
                        'is_discrepancy' => $isDiscrepancy, // Устанавливаем флаг расхождения
                    ]
                );

                // Помечаем инвентаризацию как рассчитанную
                $check->is_calculated = true;
                $check->calculated_at = now();
                $check->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    public function rollbackSales()
    {
        DB::beginTransaction();

        try {
            // Предположим, хотим откатить все инвентаризации, которые были помечены is_calculated
            // за последние X минут/часов.
            // Или же храним конкретный список CheckID — зависит от требований.

            // Для примера: Откатим все, у которых calculated_at = today (или за последние N минут):
            $checksToRollback = InventoryCheck::whereDate('calculated_at', now()->toDateString())->get();

            foreach ($checksToRollback as $check) {
                // Удаляем продажи, созданные для этой даты/товара
                Sale::where('Article', $check->Article)
                    ->whereDate('SaleDate', $check->Date)
                    ->delete();

                // Возвращаем is_calculated = false
                $check->is_calculated = false;
                $check->calculated_at = null;
                $check->save();
            }

            // При желании можете восстановить InventoryBalance,
            // но тогда надо знать, что было до расчёта.
            // К примеру, обнулить (или как-то иначе).
            // InventoryBalance::query()->update(['StockCount' => 0]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


}
