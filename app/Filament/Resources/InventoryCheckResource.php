<?php

namespace App\Filament\Resources;

use App\Filament\Imports\InventoryCheckImporter;
use App\Filament\Resources\InventoryCheckResource\Pages;
use App\Filament\Resources\InventoryCheckResource\RelationManagers;
use App\Models\InventoryCheck;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryCheckResource extends Resource
{
    protected static ?string $model = InventoryCheck::class;

    protected static ?string $navigationIcon = 'heroicon-c-clipboard-document';

    public static function getNavigationGroup(): ?string
    {
        return 'Операции';
    }

    public static function getModelLabel(): string
    {
        return 'Инвентаризация'; // Единичное название
    }

    public static function getPluralModelLabel(): string
    {
        return 'Инвентаризации'; // Множественное название
    }

    public static function getNavigationLabel(): string
    {
        return 'Инвентаризации'; // Название в навигации
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('Article')
                    ->label('Артикул')
                    ->required(),
                DatePicker::make('Date')
                    ->label('Дата')
                    ->required(),
                TextInput::make('CountedStock')
                    ->label('Количество')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('CheckID')
                    ->label('Check ID')
                    ->sortable(),
                TextColumn::make('Article')
                    ->label('Article')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Наименование товара') // Отображаем наименование через связь
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('CountedStock')
                    ->label('Counted Stock')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Импорт CSV')
                    ->importer(InventoryCheckImporter::class) // Класс импортера
                    ->icon('heroicon-o-arrow-down-on-square') // Иконка для импорта
                    ->color('primary') // Устанавливаем основной цвет кнопки,
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
            'index' => Pages\ListInventoryChecks::route('/'),
            'create' => Pages\CreateInventoryCheck::route('/create'),
            'edit' => Pages\EditInventoryCheck::route('/{record}/edit'),
        ];
    }
}
