<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryBalanceResource\Pages;
use App\Filament\Resources\InventoryBalanceResource\RelationManagers;
use App\Models\InventoryBalance;
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
