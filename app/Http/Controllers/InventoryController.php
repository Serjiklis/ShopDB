<?php

namespace App\Http\Controllers;

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
            $products = Product::all();

            foreach ($products as $product) {
                $totalSupplies = $product->supplies()->sum('quantity');
                $totalInventory = $product->inventoryChecks()->sum('CountedStock');

                $soldQuantity = $totalSupplies - $totalInventory;

                if ($soldQuantity > 0) {
                    Sale::updateOrCreate(
                        ['Article' => $product->article, 'SaleDate' => now()],
                        ['QuantitySold' => $soldQuantity, 'TotalPrice' => $soldQuantity * $product->retail_price]
                    );

                    InventoryBalance::updateOrCreate(
                        ['Article' => $product->article],
                        ['StockCount' => $totalInventory]
                    );
                }
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
            // Удаляем все записи продаж, добавленные в последний расчет
            Sale::whereDate('SaleDate', now()->toDateString())->delete();

            // Восстанавливаем остатки
            InventoryBalance::query()->update(['StockCount' => 0]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
