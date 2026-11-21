<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReconciliationBatch extends Model
{
    protected $fillable = ['user_id', 'name', 'bank_name', 'period_start', 'period_end'];

    public function entries(): HasMany
    {
        return $this->hasMany(ReconciliationEntry::class, 'batch_id');
    }
}