<?php

namespace App\Livewire\Reports;

use App\Models\Transaction;
use Livewire\Component;

class OperatingExpenseDetail extends Component
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

        // Get parent totals (same way as working OperatingExpense)
        $parentTotals = Transaction::query()
            ->whereNotIn('account_name', [
                'Bank BCA',
                'Petty Cash',
            ])
            ->where(function ($q) {
                $q->whereNull('description')
                    ->orWhere('description', 'not like', 'Saldo per%');
            })
            ->whereBetween('transaction_date', [
                $this->dateFrom,
                $this->dateTo,
            ])
            ->where('debit', '>', 0)
            ->selectRaw('
                SUBSTR(account_code, 1, 4) as account_code,
                SUM(debit) as total
            ')
            ->groupByRaw('SUBSTR(account_code, 1, 4)')
            ->orderBy('account_code')
            ->get()
            ->map(function ($item) use ($coa) {
                return (object) [
                    'account_code' => $item->account_code,
                    'account_name' => $coa[$item->account_code] ?? 'Lainnya',
                    'total' => $item->total,
                ];
            })
            ->filter(function ($item) use ($coa) {
                return isset($coa[$item->account_code]);
            });

        // Get all child details grouped by account_name per parent code
        $expenses = [];
        $grandTotal = 0;

        foreach ($parentTotals as $parent) {
            $children = Transaction::query()
                ->whereNotIn('account_name', [
                    'Bank BCA',
                    'Petty Cash',
                ])
                ->where(function ($q) {
                    $q->whereNull('description')
                        ->orWhere('description', 'not like', 'Saldo per%');
                })
                ->whereBetween('transaction_date', [
                    $this->dateFrom,
                    $this->dateTo,
                ])
                ->where('debit', '>', 0)
                ->whereRaw('SUBSTR(account_code, 1, 4) = ?', [$parent->account_code])
                ->selectRaw('
                    account_name,
                    SUM(debit) as total,
                    COUNT(*) as count
                ')
                ->groupBy('account_name')
                ->orderByRaw('SUM(debit) DESC')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'name' => $item->account_name,
                        'total' => $item->total,
                        'count' => $item->count,
                    ];
                });

            $expenses[] = (object) [
                'code' => $parent->account_code,
                'name' => $parent->account_name,
                'total' => $parent->total,
                'children' => $children,
            ];

            $grandTotal += $parent->total;
        }

        return view(
            'livewire.reports.operating-expense-detail',
            compact(
                'expenses',
                'grandTotal'
            )
        )->layout('layouts.app');
    }
}
