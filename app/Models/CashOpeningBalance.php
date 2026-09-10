<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashOpeningBalance extends Model
{
    protected $fillable = [
        'year',
        'month',
        'amount',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'amount' => 'decimal:2',
    ];
}