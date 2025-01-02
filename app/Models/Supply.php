<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supply extends Model
{
    protected $fillable = ['date', 'invoice_number', 'article', 'quantity', 'price'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'article', 'article');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            // Получаем цену за единицу из товара, если не указана
            if (!$sale->price) {
                $product = Product::where('article', $sale->article)->first();
                $sale->price = $product->retail_price ?? 0;
            }

        });
    }
}
