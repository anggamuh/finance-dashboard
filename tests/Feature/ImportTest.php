<?php

use App\Models\Import;
use App\Models\Transaction;

test('import can persist file path for deletion', function () {
    $import = Import::create([
        'file_name' => 'contoh.csv',
        'file_path' => 'imports/contoh.csv',
        'total_rows' => 3,
        'status' => 'processing',
    ]);

    expect($import->fresh()->file_path)->toBe('imports/contoh.csv');
});

test('deleting import also deletes related transactions', function () {
    $import = Import::create([
        'file_name' => 'transaksi.csv',
        'file_path' => 'imports/transaksi.csv',
        'total_rows' => 2,
        'status' => 'completed',
    ]);

    Transaction::create([
        'account_code' => '1001',
        'account_name' => 'Kas',
        'transaction_number' => 'TRX001',
        'transaction_date' => now(),
        'transaction_type' => 'Debit',
        'description' => 'Setoran',
        'debit' => 100000,
        'credit' => 0,
        'import_id' => $import->id,
    ]);

    Transaction::create([
        'account_code' => '1002',
        'account_name' => 'Bank',
        'transaction_number' => 'TRX002',
        'transaction_date' => now(),
        'transaction_type' => 'Credit',
        'description' => 'Penarikan',
        'debit' => 0,
        'credit' => 50000,
        'import_id' => $import->id,
    ]);

    expect(Transaction::where('import_id', $import->id)->count())->toBe(2);

    $import->transactions()->delete();
    $import->delete();

    expect(Transaction::where('import_id', $import->id)->count())->toBe(0);
    expect(Import::find($import->id))->toBeNull();
});
