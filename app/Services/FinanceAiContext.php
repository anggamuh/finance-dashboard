<?php

namespace App\Services;

use App\Models\InternalOperationalCost;
use App\Models\ReconciliationBatch;
use App\Models\Revenue;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Only aggregates, never executes AI-authored SQL or exposes bank account
 * numbers, reference numbers, names or free-form transaction descriptions.
 */
class FinanceAiContext
{
    private const EXPENSE_CODES = ['5311','5313','5314','5315','5317','5320','5322','5399'];

    public function forMonth(int $userId, string $month): array
    {
        $date = CarbonImmutable::createFromFormat('!Y-m', $month);
        if (!$date || $date->format('Y-m') !== $month) {
            throw new \InvalidArgumentException('Periode tidak valid.');
        }
        $start = $date->startOfMonth()->toDateString();
        $end = $date->endOfMonth()->toDateString();
        $priorStart = $date->subMonth()->startOfMonth()->toDateString();
        $priorEnd = $date->subMonth()->endOfMonth()->toDateString();

        $expenses = function (string $from, string $to) {
            return Transaction::query()
                ->whereBetween('transaction_date', [$from,$to])
                ->whereNotIn('account_name',['Bank BCA','Petty Cash'])
                ->where(function ($query) {
                    $query->whereNull('description')
                          ->orWhere('description','not like','Saldo per%');
                })
                ->where('debit','>',0)
                ->whereIn(DB::raw('SUBSTR(account_code, 1, 4)'), self::EXPENSE_CODES);
        };

        $totals = fn ($query) => [
            'jumlah_transaksi'=>(int)(clone $query)->count(),
            'total_rp'=>round((float)(clone $query)->sum('debit'),2),
        ];

        $expenseThisMonth = $expenses($start,$end);
        $byAccount = (clone $expenseThisMonth)
            ->selectRaw('account_code, account_name, COUNT(*) as jumlah, SUM(debit) as total')
            ->groupBy('account_code','account_name')
            ->orderByDesc('total')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'kode'=>$row->account_code,
                'akun'=>mb_substr((string)$row->account_name,0,70),
                'jumlah'=>(int)$row->jumlah,
                'total_rp'=>round((float)$row->total,2),
            ])->all();

        $reconciliation = ReconciliationBatch::query()
            ->where('user_id',$userId)
            ->whereHas('entries',fn ($q) => $q->whereBetween('posted_at',[$start,$end]))
            ->withCount([
                'entries as bank_matched'=>fn ($q) => $q->whereBetween('posted_at',[$start,$end])->where('source','bank')->where('status','matched'),
                'entries as bank_suggested'=>fn ($q) => $q->whereBetween('posted_at',[$start,$end])->where('source','bank')->where('status','suggested'),
                'entries as bank_unmatched'=>fn ($q) => $q->whereBetween('posted_at',[$start,$end])->where('source','bank')->where('status','unmatched'),
            ])
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn ($batch) => [
                'periode'=>$batch->name,
                'bank'=>$batch->bank_name,
                'cocok_disetujui'=>(int)$batch->bank_matched,
                'perlu_konfirmasi'=>(int)$batch->bank_suggested,
                'belum_cocok'=>(int)$batch->bank_unmatched,
            ])->all();

        return [
            'periode'=>$month,
            'mata_uang'=>'IDR',
            'definisi_beban'=>'Sama dengan filter beban operasional pada Dashboard; debit akun 5311/5313/5314/5315/5317/5320/5322/5399, bukan semua pergerakan uang bank.',
            'beban_operasional'=>$totals($expenseThisMonth),
            'beban_bulan_sebelumnya'=>$totals($expenses($priorStart,$priorEnd)),
            'beban_per_akun'=>$byAccount,
            'omzet_entri'=>[
                'jumlah'=>(int)Revenue::query()->whereDate('date_from','<=',$end)->whereDate('date_to','>=',$start)->count(),
                'total_rp'=>round((float)Revenue::query()->whereDate('date_from','<=',$end)->whereDate('date_to','>=',$start)->sum('amount'),2),
                'catatan'=>'Total entri omzet yang rentang tanggalnya beririsan dengan periode, bukan prorata; bisa mengandung periode lintas bulan.',
            ],
            'biaya_internal'=>[
                'jumlah'=>(int)InternalOperationalCost::query()->whereBetween('transaction_date',[$start,$end])->count(),
                'total_rp'=>round((float)InternalOperationalCost::query()->whereBetween('transaction_date',[$start,$end])->sum('total'),2),
                'catatan'=>'Dataset biaya internal terpisah dari transaksi Accurate; jangan dijumlahkan begitu saja karena mungkin tumpang tindih.',
            ],
            'rekonsiliasi_milik_pengguna'=>$reconciliation,
            'batasan'=>'Hanya ringkasan agregat, bukan daftar transaksi individual atau bukti transfer. Jawaban tidak boleh menganggap matched suggested sudah disetujui.',
        ];
    }
}
