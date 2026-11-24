<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\BankEntry;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Neraca extends Component
{
    public string $asOf;

    public function mount(): void
    {
        $this->asOf = now()->toDateString();
    }

    public function render()
    {
        $asOf = $this->asOf;
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $asOf)) {
            $asOf = now()->toDateString();
        }
        try {
            $date = Carbon::parse($asOf);
        } catch (\Throwable) {
            $date = now();
        }

        // Cash/bank movements have their own convention: debit is money in.
        // Opening balance is anchored to the selected calendar month.
        $ledgerRows = collect(['KAS', 'BCA', 'MANDIRI'])->map(function ($ledger) use ($date) {
            $opening = (float) DB::table('bank_ledger_openings')
                ->where('ledger', $ledger)
                ->where('year', $date->year)
                ->where('month', $date->month)
                ->value('amount');
            $movements = BankEntry::query()
                ->where('ledger', $ledger)
                ->whereBetween('entry_date', [$date->copy()->startOfMonth()->toDateString(), $date->toDateString()])
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(debit), 0) as money_in, COALESCE(SUM(credit), 0) as money_out')
                ->first();

            return (object) [
                'ledger' => $ledger,
                'opening' => $opening,
                'money_in' => (float) $movements->money_in,
                'money_out' => (float) $movements->money_out,
                'count' => (int) $movements->count,
                'ending' => $opening + (float) $movements->money_in - (float) $movements->money_out,
            ];
        });

        // Existing Accurate transactions are single-side expense/income records,
        // not a balanced general journal. Show their COA breakdown separately.
        // Do not add these balances to the bank balances: that can double count.
        $coa = Account::query()
            ->whereIn('type', ['asset', 'liability', 'equity'])
            ->orderBy('type')
            ->orderBy('code')
            ->get()
            ->groupBy('type');

        $legacyAmounts = Transaction::query()
            ->whereNull('bank_entry_id')
            ->whereDate('transaction_date', '<=', $date->toDateString())
            ->selectRaw('account_code, COALESCE(SUM(debit),0) AS debit_total, COALESCE(SUM(credit),0) AS credit_total')
            ->groupBy('account_code')
            ->get()
            ->keyBy('account_code');

        $accountGroups = collect(['asset' => 'Aset', 'liability' => 'Kewajiban', 'equity' => 'Ekuitas'])
            ->map(function ($label, $type) use ($coa, $legacyAmounts) {
                return [
                    'label' => $label,
                    'rows' => ($coa->get($type) ?? collect())->map(function ($account) use ($legacyAmounts, $type) {
                        $totals = $legacyAmounts->get($account->code);
                        $debit = (float) ($totals?->debit_total ?? 0);
                        $credit = (float) ($totals?->credit_total ?? 0);
                        return [
                            'code' => $account->code,
                            'name' => $account->name,
                            // Only net movements; not account closing balances.
                            'movement' => $type === 'asset' ? $debit - $credit : $credit - $debit,
                        ];
                    })->values(),
                ];
            });

        return view('livewire.transactions.neraca', [
            'ledgerRows' => $ledgerRows,
            'totalBank' => $ledgerRows->sum('ending'),
            'accountGroups' => $accountGroups,
        ]);
    }
}
