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
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Imports\ProductImporter;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\RestoreAction;
use Filament\Forms\Components\Tabs;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-americas';

    public static function getNavigationGroup(): ?string
    {
        return 'Товары';
    }
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
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Основные')
                            ->schema([
                TextInput::make('article')
                    ->label('Артикул'),
                TextInput::make('name')
                    ->label('Наименование'),
                Select::make('category_id')
                    ->label('Категория')
                    ->options(\App\Models\Category::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            logger('afterStateUpdated вызван для категории:', ['category_id' => $state]);

                            // Генерируем артикул для выбранной категории
                            $nextArticle = Product::generateArticleForCategory($state);
                            logger('Сгенерированный артикул:', ['article' => $nextArticle]);

                            $set('article', $nextArticle);
                        } else {
                            logger('afterStateUpdated: категория не выбрана.');
                        }
                    }),
                TextInput::make('purchase_price')
                    ->label('Закупочная цен')
                    ->numeric()
                    ->inputMode('decimal'),
                TextInput::make('retail_price')
                    ->label('Розничная цена')
                    ->numeric()
                    ->inputMode('decimal'),
                Toggle::make('is_active')
                ->label('Вкл/Выкл')
                    ->onColor('success')
                    ->offColor('danger'),
                            ])->columns(2),
                        Tabs\Tab::make('Данные')
                            ->schema([
                                // ...
                            ]),
                        ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('article')->label('Артикул')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('name')->label('Наименование')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('category.name') // Используем связь с категорией
                ->label('Категория')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('purchase_price')->label('Закупочная цена')
                    ->toggleable(),
                TextColumn::make('retail_price')->label('Розничная цена')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Статус')
                    ->boolean() // для автоматической логики "true/false"
                    ->trueIcon('heroicon-o-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-x-circle')
                    ->falseColor('danger')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Товары активны')
                    ->default()

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
