<?php

use App\Livewire\Transactions\Index as TransactionsIndex;
use App\Models\Transaction;
use Livewire\Livewire;

it('deletes a transaction when the delete event is dispatched', function () {
    $transaction = Transaction::create([
        'account_code' => '1001',
        'account_name' => 'Testing Account',
        'transaction_number' => 'TRX-001',
        'transaction_date' => now()->toDateString(),
        'transaction_type' => 'manual',
        'description' => 'Test transaction',
        'debit' => 100000,
        'credit' => 0,
    ]);

    Livewire::test(TransactionsIndex::class)
        ->dispatch('delete', ['id' => $transaction->id]);

    $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
});
