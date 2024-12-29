<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplyPDFController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/supplies/export-selected', [SupplyPDFController::class, 'exportSelectedInvoicesToPDF'])
    ->name('supplies.exportSelected');

Route::get('pdf/{supply}', SupplyPDFController::class)->name('supply.pdf');





