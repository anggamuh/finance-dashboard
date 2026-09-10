<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalOperationalCost extends Model
{
    protected $fillable = [
        'transaction_date',
        'invoice_number',
        'supplier',
        'item_name',
        'qty',
        'price',
        'total',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];
}
