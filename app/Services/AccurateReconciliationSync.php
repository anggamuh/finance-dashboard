<?php

namespace App\Services;

use App\Models\ReconciliationBatch;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates an isolated, reviewable snapshot of Accurate cash/bank journal lines.
 * Does not write to the original Accurate transactions.
 */
class AccurateReconciliationSync
{
    public function preview(string $accountCode, string $start, string $end): array
    {
        $query = $this->sourceQuery($accountCode, $start, $end);

        return [
            'count' => (clone $query)->count(),
            'debit_total' => (clone $query)->sum('debit'),
            'credit_total' => (clone $query)->sum('credit'),
        ];
    }

    public function sync(ReconciliationBatch $batch, string $accountCode, string $start, string $end): int
    {
        $startDate = CarbonImmutable::parse($start)->toDateString();
        $endDate = CarbonImmutable::parse($end)->toDateString();

        if ($startDate > $endDate) {
            throw ValidationException::withMessages(['accurateEnd' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.']);
        }

        $query = $this->sourceQuery($accountCode, $startDate, $endDate);
        $count = (clone $query)->count();

        if ($count === 0) {
            throw ValidationException::withMessages(['accurateAccount' => 'Tidak ada transaksi kas/bank Accurate untuk filter ini.']);
        }

        if ($count > 10000) {
            throw ValidationException::withMessages(['accurateAccount' => 'Maksimal 10.000 jurnal per sinkronisasi. Persempit tanggal.']);
        }

        return DB::transaction(function () use ($batch, $query, $accountCode, $startDate, $endDate, $count) {
            // Refresh the batch under lock, preventing concurrent replacements.
            $batch = ReconciliationBatch::whereKey($batch->id)->lockForUpdate()->firstOrFail();

            if ($batch->entries()->where('source', 'ledger')->where('status', 'matched')->exists()) {
                throw ValidationException::withMessages([
                    'accurateAccount' => 'Ada pasangan pembukuan yang sudah disetujui. Buat periode rekonsiliasi baru untuk sinkronisasi ulang.',
                ]);
            }

            $batch->entries()->where('status', 'suggested')
                ->update(['status' => 'unmatched', 'matched_entry_id' => null, 'confidence' => null]);
            $batch->entries()->where('source', 'ledger')->delete();

            $now = now();
            $buffer = [];

            // Using chunkById avoids loading the entire Accurate import into memory.
            (clone $query)->chunkById(500, function ($transactions) use ($batch, $now, &$buffer) {
                foreach ($transactions as $transaction) {
                    $debit = (float) $transaction->debit;
                    $credit = (float) $transaction->credit;
                    // On cash/bank asset accounts: debit means money in, credit means money out.
                    $direction = $debit > 0 ? 'in' : 'out';
                    $amount = number_format($debit > 0 ? $debit : $credit, 2, '.', '');
                    $reference = trim((string) ($transaction->transaction_number ?? ''));
                    $description = trim((string) ($transaction->description ?? ''));
                    $date = $transaction->transaction_date->toDateString();

                    $buffer[] = [
                        'batch_id' => $batch->id,
                        'source' => 'ledger',
                        'posted_at' => $date,
                        'reference' => mb_substr($reference, 0, 255),
                        'description' => $description ?: 'Jurnal Accurate #'.$transaction->id,
                        'direction' => $direction,
                        'amount' => $amount,
                        'fingerprint' => hash('sha256', 'accurate|'.$transaction->id.'|'.$date.'|'.$direction.'|'.$amount),
                        'status' => 'unmatched',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($buffer) {
                    DB::table('reconciliation_entries')->insert($buffer);
                    $buffer = [];
                }
            });

            $batch->update([
                'bank_name' => $batch->bank_name ?: $accountCode,
                'period_start' => $startDate,
                'period_end' => $endDate,
            ]);

            return $count;
        });
    }

    private function sourceQuery(string $accountCode, string $start, string $end)
    {
        return Transaction::query()
            ->where('account_code', $accountCode)
            ->whereBetween('transaction_date', [$start, $end])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('debit', '>', 0)->where('credit', '=', 0);
                })->orWhere(function ($q) {
                    $q->where('credit', '>', 0)->where('debit', '=', 0);
                });
            })
            ->where(function ($query) {
                $query->whereNull('description')
                    ->orWhere('description', 'not like', 'Saldo per%');
            })
            ->orderBy('id');
    }
}