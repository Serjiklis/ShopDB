<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Product;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('SaleDate')
                    ->label('Дата продажи')
                    ->required(),
                Select::make('article')
                    ->label('Артикул / Наименование')
                    ->options(function () {
                         return Product::query()
                    ->select(['article', 'name'])
                    ->get()
                    ->mapWithKeys(function ($product) {
                        return [$product->article => "{$product->article} - {$product->name}"];
                    })
                    ->toArray();
            })
            ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $product = Product::where('article', $state)->first();
                        $set('PricePerUnit', $product->retail_price ?? 0); // Устанавливаем цену
                    })
                    ->required(),

                TextInput::make('PricePerUnit')
                    ->label('Цена за единицу')
                    ->numeric()
                    ->reactive()
                    ->required(),

                TextInput::make('QuantitySold')
                    ->label('Количество')
                    ->numeric()
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $set('TotalPrice', $state * $get('PricePerUnit')); // Рассчитываем сумму
                    }),

                TextInput::make('TotalPrice')
                    ->label('Итоговая сумма')
                    ->numeric()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Article')->label('Артикул'),
                TextColumn::make('product.name')->label('Наименование'),
                TextColumn::make('QuantitySold')->label('Количество'),
                TextColumn::make('PricePerUnit')->label('Цена за единицу'),
                TextColumn::make('TotalPrice')->label('Итоговая сумма'),
            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('to'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($query, $date) => $query->where('SaleDate', '>=', $date))
                        ->when($data['to'], fn ($query, $date) => $query->where('SaleDate', '<=', $date))),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
