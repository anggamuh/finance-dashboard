<?php

namespace App\Livewire\Transactions;

use App\Models\BankEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Neraca extends Component
{
    public string $asOf;
    public string $ledger = 'GABUNGAN';
    public string $search = '';

    private const BANK_CODES = ['KAS' => '111', 'BCA' => '121', 'MANDIRI' => '122'];

    public function mount(): void
    {
        $this->asOf = now()->toDateString();
    }

    public function render()
    {
        try {
            $date = Carbon::createFromFormat('!Y-m-d', $this->asOf);
            if (! $date || $date->format('Y-m-d') !== $this->asOf) {
                throw new \InvalidArgumentException('Invalid date');
            }
        } catch (\Throwable) {
            $date = today();
        }

        $start = $date->copy()->startOfMonth()->toDateString();
        $end = $date->toDateString();
        $selected = in_array($this->ledger, ['GABUNGAN', 'KAS', 'BCA', 'MANDIRI'], true)
            ? $this->ledger : 'GABUNGAN';

        // Match the original workbook: GABUNGAN is a view across 111, 121 and 122,
        // not a fourth ledger. Always calculate SALDO independently for each ledger.
        $all = BankEntry::query()
            ->whereBetween('entry_date', [$start, $end])
            ->orderBy('entry_date')
            ->orderBy('id')
            ->get();

        $balances = [];
        $summary = collect(['KAS', 'BCA', 'MANDIRI'])->mapWithKeys(function ($name) use ($date, &$balances) {
            $opening = (float) DB::table('bank_ledger_openings')
                ->where('ledger', $name)->where('year', $date->year)->where('month', $date->month)
                ->value('amount');
            $balances[$name] = $opening;
            return [$name => ['code' => self::BANK_CODES[$name], 'opening' => $opening,
                'debit' => 0.0, 'credit' => 0.0, 'ending' => $opening, 'count' => 0]];
        })->all();

        $rows = $all->map(function ($row) use (&$balances, &$summary) {
            if (! array_key_exists($row->ledger, self::BANK_CODES)) {
                return null;
            }
            $ledger = $row->ledger;
            $debit = (float) $row->debit;
            $credit = (float) $row->credit;
            $balances[$ledger] += $debit - $credit;
            $summary[$ledger]['debit'] += $debit;
            $summary[$ledger]['credit'] += $credit;
            $summary[$ledger]['ending'] = $balances[$ledger];
            $summary[$ledger]['count']++;
            return [
                'id' => $row->id,
                'bank_code' => self::BANK_CODES[$ledger],
                'ledger' => $ledger,
                'account_code' => $row->account_code,
                'account_name' => $row->account_name,
                'date' => $row->entry_date->format('d/m/Y'),
                'invoice' => $row->invoice,
                'document_number' => $row->document_number,
                'channel' => $row->payment_channel,
                'cheque_number' => $row->cheque_number,
                'description' => $row->description,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balances[$ledger],
            ];
        })->filter();

        if ($selected !== 'GABUNGAN') {
            $rows = $rows->where('ledger', $selected);
        }
        $term = mb_strtolower(trim($this->search));
        if ($term !== '') {
            $rows = $rows->filter(fn ($r) => str_contains(mb_strtolower(implode(' ', [
                $r['bank_code'], $r['account_code'], $r['account_name'],
                $r['invoice'], $r['document_number'], $r['channel'], $r['description'],
            ])), $term));
        }

        return view('livewire.transactions.neraca', [
            'rows' => $rows->take(500)->values(),
            'rowCount' => $rows->count(),
            'summary' => $summary,
            'totalBalance' => array_sum(array_column($summary, 'ending')),
            'selectedLedger' => $selected,
            'periodLabel' => $date->translatedFormat('F Y'),
        ]);
    }
}
