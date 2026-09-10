<?php

namespace App\Livewire\Dashboard;

use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public $selectedMonth;

    public $startDate;

    public $endDate;

    protected array $monthNames = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    // Kode COA yang sama persis dengan OperatingExpenseDetail
    protected array $coaCodes = [
        '5311', '5313', '5314', '5315', '5317', '5320', '5322', '5399',
    ];

    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');
        $this->updatedSelectedMonth();
    }

    public function updatedSelectedMonth()
    {
        $date = Carbon::createFromFormat('Y-m', $this->selectedMonth);
        $this->startDate = $date->copy()->startOfMonth()->toDateString();
        $this->endDate = $date->copy()->endOfMonth()->toDateString();
    }

    protected function baseQuery()
    {
        return Transaction::query()
            ->whereNotIn('account_name', ['Bank BCA', 'Petty Cash'])
            ->where(function ($q) {
                $q->whereNull('description')
                    ->orWhere('description', 'not like', 'Saldo per%');
            })
            ->where('debit', '>', 0)
            ->whereRaw(
                'SUBSTR(account_code, 1, 4) IN ('.implode(',', array_fill(0, count($this->coaCodes), '?')).')',
                $this->coaCodes
            );
    }

    protected function monthlyExpenses(): array
    {
        $end = Carbon::createFromFormat('Y-m', $this->selectedMonth)->endOfMonth();
        $start = $end->copy()->subMonths(5)->startOfMonth();

        $totals = $this->baseQuery()
            ->whereBetween('transaction_date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->get(['transaction_date', 'debit'])
            ->groupBy(fn ($item) => $item->transaction_date->format('Y-m'))
            ->map(fn ($items) => (float) $items->sum('debit'));

        $result = [];

        for ($date = $start->copy(); $date <= $end; $date->addMonth()) {

            $key = $date->format('Y-m');

            $result[] = [
                'label' => $this->monthNames[$date->month].' '.$date->format('Y'),
                'total' => (float) ($totals->get($key) ?? 0),
            ];
        }

        return $result;
    }

    public function render()
    {

        $monthly = $this->monthlyExpenses();
        $current = $monthly[count($monthly) - 1]['total'];
        $previous = $monthly[count($monthly) - 2]['total'] ?? 0;
        $diff = $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);

        $stats = [
            'total_transactions' => (clone $this->baseQuery())
                ->whereDate('transaction_date', '>=', $this->startDate)
                ->whereDate('transaction_date', '<=', $this->endDate)
                ->count(),
        ];

        $this->dispatch(
            'monthly-expense-updated',
            labels: array_column($monthly, 'label'),
            totals: array_column($monthly, 'total')
        );

        return view('livewire.dashboard.index', [
            'stats' => $stats,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'monthly' => $monthly,
            'currentExpense' => $current,
            'previousExpense' => $previous,
            'expenseDiffPercent' => round($diff, 1),
        ])->layout('layouts.app');
    }
}
