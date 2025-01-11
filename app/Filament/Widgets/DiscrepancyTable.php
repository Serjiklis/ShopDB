<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\InventoryBalance;

class DiscrepancyTable extends BaseWidget
{
    protected static ?string $heading = 'Discrepancy Table';

    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InventoryBalance::query()->where('is_discrepancy', true)
            )
            ->columns([
                Tables\Columns\TextColumn::make('Article')
                    ->label('Article')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('StockCount')
                    ->label('Stock Count')
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_discrepancy')
                    ->label('Discrepancy')
                    ->badge() // Используем TextColumn с методом badge()
                    ->color(fn ($state) => $state ? 'danger' : 'success') // Цвет в зависимости от состояния
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'), // Форматируем текст

            ])
            ->defaultSort('StockCount', 'desc');
    }
}
