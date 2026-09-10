<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportPdfController extends Controller
{
    public function operatingExpense(Request $request)
    {
        $dates = $this->validatePeriod($request);
        $dateFrom = $dates['from'];
        $dateTo = $dates['to'];

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

            ->whereNotIn('account_name', [
                'Bank BCA',
                'Petty Cash',
            ])

            ->where(function ($q) {
                $q->whereNull('description')
                    ->orWhere('description', 'not like', 'Saldo per%');
            })

            ->whereBetween('transaction_date', [
                $dateFrom,
                $dateTo,
            ])

            ->where('debit', '>', 0)

            ->selectRaw('
                SUBSTR(account_code, 1, 4) as account_code,
                SUM(debit) as total
            ')

            ->groupByRaw('SUBSTR(account_code, 1, 4)')

            ->get()

            ->map(function ($item) use ($coa) {

                return (object) [
                    'account_code' => $item->account_code,
                    'account_name' => $coa[$item->account_code] ?? '-',
                    'total' => $item->total,
                ];

            })

            ->filter(fn ($x) => isset($coa[$x->account_code]))
            ->values();

        $grandTotal = $expenses->sum('total');

        $pdf = Pdf::loadView(
            'pdf.operating-expense',
            compact(
                'expenses',
                'grandTotal',
                'dateFrom',
                'dateTo'
            )
        );

        return $pdf
            ->setPaper('a4', 'portrait')
            ->stream('beban-operasional.pdf');
    }

    public function operatingExpenseDetail(Request $request)
    {
        $dates = $this->validatePeriod($request);
        $dateFrom = $dates['from'];
        $dateTo = $dates['to'];

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

        // Build parent totals using DB-level grouping (match Livewire component)
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
                $dateFrom,
                $dateTo,
            ])
            ->where('debit', '>', 0)
            ->selectRaw('SUBSTR(account_code, 1, 4) as account_code, SUM(debit) as total')
            ->groupByRaw('SUBSTR(account_code, 1, 4)')
            ->orderBy('account_code')
            ->get()
            ->map(function ($item) use ($coa) {
                return (object) [
                    'account_code' => $item->account_code,
                    'account_name' => $coa[$item->account_code] ?? '-',
                    'total' => $item->total,
                ];
            })
            ->filter(fn ($x) => isset($coa[$x->account_code]))
            ->values();

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
                    $dateFrom,
                    $dateTo,
                ])
                ->where('debit', '>', 0)
                ->whereRaw('SUBSTR(account_code, 1, 4) = ?', [$parent->account_code])
                ->selectRaw('account_name, SUM(debit) as total, COUNT(*) as count')
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

        $pdf = Pdf::loadView(
            'pdf.operating-expense-detail',
            compact(
                'expenses',
                'grandTotal',
                'dateFrom',
                'dateTo'
            )
        );

        return $pdf
            ->setPaper('a4', 'portrait')
            ->stream('beban-operasional-detail.pdf');
    }

    private function validatePeriod(Request $request): array
    {
        return $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);
    }
}
