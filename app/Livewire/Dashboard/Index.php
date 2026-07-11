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
            });
    }

    protected function monthlyExpenses(): array
    {
        $end = Carbon::createFromFormat('Y-m', $this->selectedMonth)->endOfMonth();
        $start = $end->copy()->subMonths(5)->startOfMonth();

        $rows = $this->baseQuery()
            ->selectRaw('YEAR(transaction_date) as year')
            ->selectRaw('MONTH(transaction_date) as month')
            ->selectRaw('SUM(debit) as total')
            ->whereBetween('transaction_date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->groupByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->get()
            ->keyBy(fn ($item) => sprintf('%04d-%02d', $item->year, $item->month));

        $result = [];

        for ($date = $start->copy(); $date <= $end; $date->addMonth()) {

            $key = $date->format('Y-m');

            $result[] = [
                'label' => $date->translatedFormat('M Y'),
                'total' => (float) optional($rows->get($key))->total,
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
