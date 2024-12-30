<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryBalanceResource\Pages;
use App\Filament\Resources\InventoryBalanceResource\RelationManagers;
use App\Models\InventoryBalance;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryBalanceResource extends Resource
{
    protected static ?string $model = InventoryBalance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                TextColumn::make('Article')->label('Артикул'),
                TextColumn::make('product.name')->label('Наименование товара'),
                TextColumn::make('StockCount')->label('Остаток')->sortable(),
            ])
            ->filters([
                //
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
