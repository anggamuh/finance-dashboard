<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClosingStock extends Model
{
    protected $fillable = [

        'year',

        'opening_stock',

        'incoming_stock',

        'outgoing_stock',

    ];
}
