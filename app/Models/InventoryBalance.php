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
}
