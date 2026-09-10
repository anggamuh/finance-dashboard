<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
   protected $fillable = [
    'date_from',
    'date_to',
    'qty',
    'amount',
    'notes',
];

    protected $casts = [
    'date_from' => 'date',
    'date_to' => 'date',
];
}