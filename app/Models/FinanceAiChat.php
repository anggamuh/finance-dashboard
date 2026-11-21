<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceAiChat extends Model
{
    protected $fillable = ['user_id','month','title','messages'];
    protected $casts = ['messages'=>'encrypted:array'];
}
