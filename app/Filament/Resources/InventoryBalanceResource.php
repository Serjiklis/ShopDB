<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryBalanceResource\Pages;
use App\Filament\Resources\InventoryBalanceResource\RelationManagers;
use App\Http\Controllers\InventoryController;
use App\Models\InventoryBalance;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryBalanceResource extends Resource
{
    protected static ?string $model = InventoryBalance::class;

    protected static ?string $navigationIcon = 'heroicon-m-clipboard-document-list';

    public static function getNavigationGroup(): ?string
    {
        return 'Операции';
    }

    public static function getModelLabel(): string
    {
        return 'Остатки товара'; // Единичное название
    }

    public static function getPluralModelLabel(): string
    {
        return 'Остатки товаров'; // Множественное название
    }

    public static function getNavigationLabel(): string
    {
        return 'Остатки товаров'; // Название в навигации
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('Article')
                    ->label('Артикул / Наименование')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('StockCount')
                    ->label('Остаток')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Article')
                    ->label('Артикул')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Наименование товара')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category.name') // Используем связь с категорией
                ->label('Категория')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('StockCount')
                    ->label('Остаток')
                    ->sortable(),
                IconColumn::make('is_discrepancy')
                    ->label('Расхождение')
                    ->boolean() // для автоматической логики "true/false"
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->color(function ($state) {
                        return $state ? 'danger' : 'success';
                    })
                    ->sortable(),
        TextColumn::make('created_at')
                    ->label('Создано')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Категория')
                    ->relationship('category', 'name') // связь с категорией
                    ->preload()
                    ->multiple()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Action::make('calculateSales')
                    ->label('Рассчитать продажи')
                    ->color('success')
                    ->icon('heroicon-o-calculator')
                    ->action(function () {
                        // Вызов метода расчета из контроллера
                        app(\App\Http\Controllers\InventoryController::class)->calculateSales();

                        // Уведомление
                        Notification::make()
                            ->title('Расчет успешно выполнен!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation('Вы уверены, что хотите выполнить расчет?')
                    ->button(),
                Action::make('rollbackSales')
                    ->label('Откатить изменения')
                    ->color('danger')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->action(function () {
                        // Вызов метода отката из контроллера
                        app(\App\Http\Controllers\InventoryController::class)->rollbackSales();

                        // Уведомление
                        Notification::make()
                            ->title('Откат успешно выполнен!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation('Вы уверены, что хотите откатить изменения?')
                    ->button(),

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
            'index' => Pages\ListInventoryBalances::route('/'),
            'create' => Pages\CreateInventoryBalance::route('/create'),
            'edit' => Pages\EditInventoryBalance::route('/{record}/edit'),
        ];
    }
}
