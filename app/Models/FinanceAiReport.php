<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceAiReport extends Model
{
    protected $fillable = ['user_id','month','title','snapshot'];

    protected $casts = ['snapshot'=>'encrypted:array'];
}
