<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;




class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('article')
                ->label('Артикул') // Label for display
                ->requiredMapping() // Marks this column as required in the mapping
                ->rules(['required', 'max:255']), // Validation rules for the column,

    ImportColumn::make('name')
        ->label('Наименование') // Label for the column
        ->requiredMapping() // Ensures the column is mapped during import
        ->rules(['required', 'max:255']), // Validation rules

            ImportColumn::make('category')
                ->label('Категория')
                ->relationship(resolveUsing: 'name')
                ->rules(['nullable', 'exists:categories,name']),

    ImportColumn::make('purchase_price')
        ->label('Закупочная цена') // Purchase price
        ->numeric() // Numeric input
        ->rules(['required', 'numeric', 'min:0']), // Validation rules for purchase price

    ImportColumn::make('retail_price')
        ->label('Розничная цена') // Retail price
        ->numeric() // Numeric input
        ->rules(['required', 'numeric', 'min:0']), // Validation rules for retail price
        ];
    }

    public function resolveRecord(): ?Product
    {
        return Product::firstOrNew([
            'article' => $this->data['article'], // Match by article to update existing products
        ]);

    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function beforeSave(): void
    {
        // Transform category name to ID
        if (!empty($this->data['category'])) {
            $category = \App\Models\Category::where('name', $this->data['category'])->first();
            if ($category) {
                $this->record->category_id = $category->id;
            } else {
                throw new \Exception("Категория '{$this->data['category']}' не найдена.");
            }
        }
    }
}
