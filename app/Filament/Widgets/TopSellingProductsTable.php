<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class TopSellingProductsTable extends BaseWidget
{
    protected static ?string $heading = 'Топ продаваемых товаров';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()
                    ->select(
                        'Article',
                        DB::raw('SUM(QuantitySold) as total_quantity, SUM(TotalPrice) as total_revenue')
                    )
                    ->groupBy('Article')
                    ->orderByDesc('total_quantity') // Сортировка по количеству продаж
                    ->limit(10) // Показываем только топ-10 товаров
            )
            ->columns([
                Tables\Columns\TextColumn::make('Article')
                    ->label('Артикул')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Название товара')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Продано, шт.')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Выручка, ₽')
                    ->money('rub') // Отображаем значение как валюту
                    ->sortable(),
            ])
            ->defaultSort('total_quantity', 'desc');
    }

    public function getTableRecordKey(Model|\Illuminate\Database\Eloquent\Model $record): string
    {
        // Указываем уникальный ключ записи для таблицы (в данном случае Article)
        return 'Article';
    }
}
