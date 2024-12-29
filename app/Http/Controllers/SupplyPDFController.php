<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplyPDFController extends Controller
{
    public function __invoke(Supply $supply)
    {
        $supplies = Supply::where('invoice_number', $supply->invoice_number)
            ->with('product') // Eager load the product
            ->get();

        if ($supplies->isEmpty()) {
            return back()->with('error', 'No supplies found for this invoice.');
        }

        return Pdf::loadView('pdf.supply', [
            'record' => $supply,
            'supplies' => $supplies,
        ])->download('invoice-' . str_replace(['/', '\\'], '-', $supply->invoice_number) . '.pdf');
    }

}
