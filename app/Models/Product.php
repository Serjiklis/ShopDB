<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = ['article', 'name', 'purchase_price', 'retail_price', 'category_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplies(): HasMany
    {
        return $this->hasMany(Supply::class, 'article', 'article');
    }

    public function inventoryChecks(): HasMany
    {
        return $this->hasMany(InventoryCheck::class, 'Article', 'article');
    }

}
