<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Imports\ProductImporter;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\RestoreAction;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getModelLabel(): string
    {
        return 'Товар';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Товары';
    }

    public static function getNavigationLabel(): string
    {
        return 'Справочник';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('article'),
                TextInput::make('name'),
                Select::make('category_id')
                    ->label('Category')
                    ->options(\App\Models\Category::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
        TextInput::make('purchase_price')
                    ->numeric()
                    ->inputMode('decimal'),
                TextInput::make('retail_price')
                    ->numeric()
                    ->inputMode('decimal'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('article')->label('Артикул')
                    ->searchable(),
                TextColumn::make('name')->label('Наименование')
                    ->searchable(),
                TextColumn::make('category.name') // Используем связь с категорией
                ->label('Категория')
                    ->searchable(),
                TextColumn::make('purchase_price')->label('Закупочная цена'),
                TextColumn::make('retail_price')->label('Розничная цена'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Изменить'),

            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ProductImporter::class)
                    ->label('Импортировать продукты'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

}
