<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccurateImport extends Model
{
    protected $fillable = [
        'original_name',
        'file_name',
        'file_path',
        'file_size',
        'period',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_at'=>'datetime',
    ];
}
