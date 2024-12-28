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
}
