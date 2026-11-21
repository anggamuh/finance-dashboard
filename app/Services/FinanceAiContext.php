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
    public const EXPENSE_CODES = ['5311','5313','5314','5315','5317','5320','5322','5399'];

    public function expenseQuery(string $start, string $end): \Illuminate\Database\Eloquent\Builder
    {
        return Transaction::query()
            ->whereBetween('transaction_date', [$start,$end])
            ->whereNotIn('account_name', ['Bank BCA','Petty Cash'])
            ->where(fn ($q) => $q->whereNull('description')->orWhere('description', 'not like', 'Saldo per%'))
            ->where('debit', '>', 0)
            ->whereIn(DB::raw('SUBSTR(account_code, 1, 4)'), self::EXPENSE_CODES);
    }

    public function dashboard(string $month): array
    {
        $period = CarbonImmutable::createFromFormat('!Y-m', $month);
        if (!$period || $period->format('Y-m') !== $month) {
            throw new \InvalidArgumentException('Periode tidak valid.');
        }
        $start = $period->startOfMonth()->toDateString();
        $end = $period->endOfMonth()->toDateString();
        $previous = $period->subMonth();
        $prevStart = $previous->startOfMonth()->toDateString();
        $prevEnd = $previous->endOfMonth()->toDateString();

        $current = $this->expenseQuery($start, $end);
        $prev = $this->expenseQuery($prevStart, $prevEnd);
        $total = (float)(clone $current)->sum('debit');
        $prevTotal = (float)(clone $prev)->sum('debit');
        $byAccount = (clone $current)
            ->selectRaw('account_code, account_name, COUNT(*) as transaction_count, SUM(debit) as total')
            ->groupBy('account_code', 'account_name')
            ->orderByDesc('total')->limit(8)->get()
            ->map(fn ($r) => [
                'code'=>(string)$r->account_code,
                'name'=>(string)$r->account_name,
                'amount'=>round((float)$r->total, 2),
                'count'=>(int)$r->transaction_count,
            ])->all();

        $chart = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = $period->subMonths($i);
            $chart[] = [
                'month'=>$d->format('Y-m'),
                'label'=>$d->translatedFormat('M Y'),
                'total'=>round((float)$this->expenseQuery($d->startOfMonth()->toDateString(), $d->endOfMonth()->toDateString())->sum('debit'),2),
            ];
        }

        $insights = [];
        $delta = $total - $prevTotal;
        if ($prevTotal > 0) {
            $insights[] = [
                'title'=>$delta > 0 ? 'Beban naik' : ($delta < 0 ? 'Beban turun' : 'Beban stabil'),
                'detail'=>'Perubahan '.number_format(abs($delta),0,',','.').' rupiah ('.number_format(abs($delta / $prevTotal * 100),1,',','.').'%) dari bulan sebelumnya.',
                'type'=>$delta > 0 ? 'warning' : 'info',
            ];
        } else {
            $insights[] = ['title'=>'Perbandingan terbatas', 'detail'=>'Tidak ada beban bulan sebelumnya untuk menghitung persentase perubahan.', 'type'=>'info'];
        }
        if ($byAccount !== []) {
            $top = $byAccount[0];
            $insights[] = [
                'title'=>'Beban terbesar: '.$top['name'],
                'detail'=>'Rp '.number_format($top['amount'],0,',','.').' dari '.number_format($total,0,',','.').' total beban operasional. Klik akun untuk melihat rinciannya.',
                'type'=>'info',
            ];
        }
        if ($total <= 0) {
            $insights[] = ['title'=>'Belum ada beban', 'detail'=>'Tidak ditemukan transaksi beban operasional untuk periode yang dipilih.', 'type'=>'info'];
        }

        return [
            'month'=>$month,
            'total'=>$total,
            'previous_total'=>$prevTotal,
            'delta'=>$delta,
            'chart'=>$chart,
            'accounts'=>$byAccount,
            'insights'=>$insights,
        ];
    }

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

        $expenses = fn (string $from, string $to) => $this->expenseQuery($from, $to);

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
