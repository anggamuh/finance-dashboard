<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReconciliationEntry extends Model
{
    protected $fillable = ['batch_id','source','posted_at','reference','description','direction','amount','fingerprint','matched_entry_id','status','confidence'];
    protected $casts = ['posted_at'=>'date','amount'=>'decimal:2'];

    public function batch(): BelongsTo { return $this->belongsTo(ReconciliationBatch::class, 'batch_id'); }
    public function match(): BelongsTo { return $this->belongsTo(self::class, 'matched_entry_id'); }
}