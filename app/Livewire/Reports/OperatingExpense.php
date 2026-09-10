<?php

namespace App\Livewire\Reports;

use App\Models\Transaction;
use Livewire\Component;

class OperatingExpense extends Component
{
    public $dateFrom;

    public $dateTo;

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        // Mapping COA Laporan
        $coa = [
             '5311' => 'Gaji & Tunjangan',
            '5313' => 'Beban Transportasi',
            '5314' => 'Beban Penyusutan & Amortisasi',
            '5315' => 'Beban Sewa',
            '5317' => 'Beban Jasa',
            '5320' => 'Beban Pemasaran',
            '5322' => 'Beban Umum & Administrasi',
            '5399' => 'Beban Usaha Lainnya',
        ];

        $expenses = Transaction::query()

            // Hilangkan akun kas
            ->whereNotIn('account_name', [
                'Bank BCA',
                'Petty Cash',
            ])

            // Hilangkan saldo awal
            ->where(function ($q) {
                $q->whereNull('description')
                    ->orWhere('description', 'not like', 'Saldo per%');
            })

            // Filter tanggal
            ->whereBetween('transaction_date', [
                $this->dateFrom,
                $this->dateTo,
            ])

            // Hanya transaksi debit
            ->where('debit', '>', 0)

            // Kelompokkan berdasarkan 4 digit kode akun
            ->selectRaw('
                LEFT(account_code,4) as account_code,
                SUM(debit) as total
            ')

            ->groupByRaw('LEFT(account_code,4)')

            ->orderBy('account_code')

            ->get()

            // Ganti nama akun sesuai mapping
            ->map(function ($item) use ($coa) {

                return (object) [
                    'account_code' => $item->account_code,
                    'account_name' => $coa[$item->account_code] ?? 'Lainnya',
                    'total' => $item->total,
                ];

            })

            // Tampilkan hanya akun yang ada di mapping
            ->filter(function ($item) use ($coa) {
                return isset($coa[$item->account_code]);
            })

            ->values();

        $grandTotal = $expenses->sum('total');

        return view(
            'livewire.reports.operating-expense',
            compact(
                'expenses',
                'grandTotal'
            )
        )->layout('layouts.app');
    }
}
