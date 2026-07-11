<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'account_code',
        'account_name',
        'transaction_number',
        'transaction_date',
        'transaction_type',
        'description',
        'debit',
        'credit',
        'import_id',
        'proof_file',
        'proof_original_name',
        'payment_method',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_code', 'code');
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }

    // Helper to get balance (debit - credit)
    public function getBalanceAttribute()
    {
        return $this->debit - $this->credit;
    }
}
