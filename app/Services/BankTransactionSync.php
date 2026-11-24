<?php

namespace App\Services;

use App\Models\BankEntry;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class BankTransactionSync
{
    /**
     * Mirror one Kas/Bank entry into the existing transactions table.
     * transactions convention: debit = expense, credit = income;
     * Kas/Bank convention: debit = money in, credit = money out.
     */
    public function sync(BankEntry $entry): Transaction
    {
        $transaction = Transaction::withTrashed()->firstOrNew(['bank_entry_id' => $entry->id]);
        $transaction->fill([
            'bank_entry_id' => $entry->id,
            'transaction_date' => $entry->entry_date,
            'transaction_number' => $entry->document_number,
            // Keep the original nullable DBACC on bank_entries; the legacy
            // transactions table requires a non-null account code.
            'account_code' => filled($entry->account_code) ? trim((string) $entry->account_code) : 'BANK-UNMAPPED',
            'account_name' => filled($entry->account_name) ? $entry->account_name : 'Akun Kas-Bank Belum Dipetakan',
            'description' => $entry->description,
            'payment_method' => match ($entry->ledger) {
                'BCA' => 'Bank BCA',
                'MANDIRI' => 'Bank Mandiri',
                default => 'Petty Cash',
            },
            'transaction_type' => (float) $entry->debit > 0 ? 'income' : 'expense',
            'debit' => $entry->credit,
            'credit' => $entry->debit,
        ]);
        $transaction->deleted_at = null;
        $transaction->save();

        return $transaction;
    }

    public function delete(BankEntry $entry): void
    {
        DB::transaction(function () use ($entry) {
            Transaction::withTrashed()->where('bank_entry_id', $entry->id)->forceDelete();
            $entry->delete();
        });
    }

    public function deleteImportedPeriod(string $ledger, int $month, int $year): int
    {
        return DB::transaction(function () use ($ledger, $month, $year) {
            $query = BankEntry::query()
                ->where('source', 'excel')
                ->where('ledger', $ledger)
                ->whereYear('entry_date', $year)
                ->whereMonth('entry_date', $month);
            $count = 0;
            $query->orderBy('id')->chunkById(250, function ($entries) use (&$count) {
                foreach ($entries as $entry) {
                    $this->delete($entry);
                    $count++;
                }
            });

            return $count;
        });
    }
}
