<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryBalance extends Model
{
    protected $fillable = ['Article', 'StockCount'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'Article', 'article');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Событие при сохранении записи
        static::saving(function ($inventoryBalance) {
            if ($inventoryBalance->Article) {
                // Получаем связанный продукт
                $product = Product::where('article', $inventoryBalance->Article)->first();

                // Если продукт найден, устанавливаем category_id
                if ($product && $product->category_id) {
                    $inventoryBalance->category_id = $product->category_id;
                }
            }
        });
    }

}
