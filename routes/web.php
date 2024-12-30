<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplyPDFController;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/supplies/export-selected', [SupplyPDFController::class, 'exportSelectedInvoicesToPDF'])
    ->name('supplies.exportSelected');

Route::get('pdf/{supply}', SupplyPDFController::class)->name('supply.pdf');

// Routs for manual test Sales Calculate
Route::get('/inventory/calculate-sales', [InventoryController::class, 'calculateSales'])->name('inventory.calculate-sales');
Route::get('/inventory/rollback-sales', [InventoryController::class, 'rollbackSales'])->name('inventory.rollback-sales');



