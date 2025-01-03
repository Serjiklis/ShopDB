<?php

namespace App\Filament\Resources;

use App\Filament\Imports\SupplyImporter;
use App\Filament\Resources\SupplyResource\Pages;
use App\Filament\Resources\SupplyResource\RelationManagers;
use App\Models\Product;
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
use Filament\Tables\Actions\Action;
use Illuminate\Support\Collection;
use App\Filament\Exports\SupplyExporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\BulkAction;
use Carbon\Carbon;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function getNavigationGroup(): ?string
    {
        return 'Операции';
    }

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
                    ->default(Carbon::now())
                    ->required(),
                TextInput::make('invoice_number')
                    ->label('Номер накладной')
                    ->required()
                    ->maxLength(50),
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
                        $set('price', $product->retail_price ?? 0); // Устанавливаем цену
                    })
                    ->dehydrateStateUsing(fn ($state) => $state) // Явно сохраняем в 'Article'
                    ->required(),
                TextInput::make('quantity')
                    ->label('Количество')
                    ->numeric()
                    ->required(),

                TextInput::make('price')
                    ->label('Цена за единицу')
                    ->numeric()
                    ->inputMode('decimal')
                    ->reactive()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Дата поставки')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Номер накладной')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('article')
                    ->label('Артикул')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Наименование товара') // Отображаем наименование через связь
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Количество')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->sortable()
                    ->toggleable(),
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
                //test
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->url(fn (Supply $record) => route('supply.pdf', $record))
                    ->openUrlInNewTab(),


            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Импорт CSV')
                    ->importer(SupplyImporter::class) // Класс импортера
                    ->icon('heroicon-o-arrow-down-on-square') // Иконка для импорта
                    ->color('primary'), // Устанавливаем основной цвет кнопки,
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
//                ExportBulkAction::make()
//                    ->exporter(SupplyExporter::class)
//                    ->label('Экспортировать выбранные')
//                    ->icon('heroicon-o-arrow-up-on-square'),

                BulkAction::make('exportSelected')
                    ->label('Экспортировать выбранные накладные')
                    ->action(function (Collection $records) {
                        $selectedIds = $records->pluck('id')->toArray();

                        // Сохранение ID выбранных записей в сессии
                        session(['selected_ids' => $selectedIds]);
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-arrow-up-on-square'),

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
