<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCheck extends Model
{
    protected $table = 'inventory_checks';

    protected $primaryKey = 'CheckID';

    protected $fillable = ['Article', 'Date', 'CountedStock'];

    /**
     * Relationship with Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'Article', 'article');
    }

}
