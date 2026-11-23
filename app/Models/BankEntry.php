<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankEntry extends Model
{
    protected $fillable = [
        'ledger', 'entry_date', 'document_number', 'account_code', 'account_name',
        'invoice', 'payment_channel', 'cheque_number', 'description', 'debit',
        'credit', 'source_balance', 'source', 'fingerprint',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'source_balance' => 'decimal:2',
    ];

    public static function fingerprintFor(array $data): string
    {
        // Identical transaction content on a different date/ledger remains distinct.
        // An exactly identical repeated row is treated as the same record on re-import.
        return hash('sha256', json_encode([
            $data['ledger'],
            $data['entry_date'],
            trim((string) ($data['document_number'] ?? '')),
            trim((string) ($data['account_code'] ?? '')),
            trim((string) ($data['invoice'] ?? '')),
            trim((string) ($data['description'] ?? '')),
            number_format((float) ($data['debit'] ?? 0), 2, '.', ''),
            number_format((float) ($data['credit'] ?? 0), 2, '.', ''),
        ], JSON_UNESCAPED_UNICODE));
    }
}
