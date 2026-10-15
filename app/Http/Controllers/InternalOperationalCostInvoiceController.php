<?php

namespace App\Http\Controllers;

use App\Models\InternalOperationalCost;

class InternalOperationalCostInvoiceController extends Controller
{
    public function show(InternalOperationalCost $cost)
    {
        // Muat semua baris item, bukan hanya kolom item lama di tabel utama.
        $cost->loadMissing('items');

        $date = $cost->transaction_date;

        $code = filled($cost->invoice_number)
            ? (string) $cost->invoice_number
            : 'BON-'
                . $date->format('Ymd')
                . '-'
                . str_pad((string) $cost->id, 4, '0', STR_PAD_LEFT);

        return view('invoices.internal-operational-cost', [
            'cost' => $cost,
            'code' => $code,
        ]);
    }
}
