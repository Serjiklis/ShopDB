<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriesResource\Pages;
use App\Filament\Resources\CategoriesResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriesResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function getNavigationGroup(): ?string
    {
        return 'Товары';
    }
    public static function getModelLabel(): string
    {
        return 'Категория';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Категории';
    }

    public static function getNavigationLabel(): string
    {
        return 'Категории';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Наименование'),
                TextInput::make('description')
                    ->label('Описание'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('каждый товар должен иметь категорию')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Наименование'),
                TextColumn::make('description')
                    ->label('Описание')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Изменить'),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategories::route('/create'),
            'edit' => Pages\EditCategories::route('/{record}/edit'),
        ];
    }
}
