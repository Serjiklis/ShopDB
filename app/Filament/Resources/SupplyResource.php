<?php

namespace App\Filament\Resources;

use App\Filament\Imports\SupplyImporter;
use App\Filament\Resources\SupplyResource\Pages;
use App\Filament\Resources\SupplyResource\RelationManagers;
use App\Models\Supply;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    public static function getModelLabel(): string
    {
        return 'Поставка'; // Единичное название
    }

    public static function getPluralModelLabel(): string
    {
        return 'Поставки'; // Множественное название
    }

    public static function getNavigationLabel(): string
    {
        return 'Поставки'; // Название в навигации
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->label('Дата')
                    ->required(),
                TextInput::make('invoice_number')
                    ->label('Номер счета')
                    ->required()
                    ->maxLength(50),
                TextInput::make('article')
                    ->label('Артикул')
                    ->required()
                    ->maxLength(50),
                TextInput::make('quantity')
                    ->label('Количество')
                    ->numeric()
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->inputMode('decimal'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Дата поставки')
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Номер счета')
                    ->searchable(),
                Tables\Columns\TextColumn::make('article')
                    ->label('Артикул')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Наименование товара') // Отображаем наименование через связь
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Количество')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_and_invoice')
                    ->label('Фильтр по дате и номеру счета')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('С даты')
                            ->reactive(), // Делаем поле реактивным
                        DatePicker::make('date_to')
                            ->label('До даты')
                            ->reactive(), // Делаем поле реактивным
                        Select::make('invoice_number')
                            ->label('Номер счета')
                            ->reactive() // Выпадающий список тоже реактивный
                            ->options(function (callable $get) {
                                $query = Supply::query();

                                // Учитываем диапазон дат
                                if ($get('date_from')) {
                                    $query->where('date', '>=', $get('date_from'));
                                }

                                if ($get('date_to')) {
                                    $query->where('date', '<=', $get('date_to'));
                                }

                                return $query->pluck('invoice_number', 'invoice_number')->toArray();
                            })
                            ->placeholder('Все счета'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['date_from'], fn ($query, $date) => $query->where('date', '>=', $date))
                            ->when($data['date_to'], fn ($query, $date) => $query->where('date', '<=', $date))
                            ->when($data['invoice_number'], fn ($query, $invoice) => $query->where('invoice_number', $invoice));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Изменить'),
                Tables\Actions\ViewAction::make()->label('Просмотр'),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Импорт CSV')
                    ->importer(SupplyImporter::class), // Класс импортера
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
            'index' => Pages\ListSupplies::route('/'),
            'create' => Pages\CreateSupply::route('/create'),
            'edit' => Pages\EditSupply::route('/{record}/edit'),
        ];
    }
}
