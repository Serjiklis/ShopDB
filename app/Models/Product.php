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

    public static function generateArticleForCategory($categoryId)
    {
        // Ищем максимальный артикул именно в рамках переданной категории
        $maxArticle = Product::where('category_id', $categoryId)
            ->selectRaw('CAST(MAX(CAST(article AS UNSIGNED)) AS UNSIGNED) as max_article')
            ->value('max_article');

        // Если что-то нашлось – увеличиваем на 1,
        // иначе начинаем с условного 25001 (или любого стартового числа)
        $nextArticle = $maxArticle ? $maxArticle + 1 : 25001;

        return $nextArticle;
    }

}
