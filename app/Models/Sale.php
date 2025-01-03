<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = ['Article', 'QuantitySold', 'SaleDate', 'TotalPrice'];
    protected $casts = [
        'SaleDate' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'Article', 'article');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            // Получаем цену за единицу из товара, если не указана
            if (!$sale->PricePerUnit) {
                $product = Product::where('article', $sale->Article)->first();
                $sale->PricePerUnit = $product->retail_price ?? 0;
            }

            // Рассчитываем общую сумму
            $sale->TotalPrice = $sale->QuantitySold * $sale->PricePerUnit;
        });
    }



}
