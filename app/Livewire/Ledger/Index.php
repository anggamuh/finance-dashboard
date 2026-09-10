<?php

namespace App\Livewire\Ledger;

use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $selectedMonth;

    public $dateFrom;

    public $dateTo;

    public $selectedAccount = '';

    public $search = '';

    public $perPage = 25;

    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');
        $this->updatedSelectedMonth();
    }

    public function updatedSelectedMonth()
    {
        $date = Carbon::createFromFormat('Y-m', $this->selectedMonth);
        $this->dateFrom = $date->copy()->startOfMonth()->toDateString();
        $this->dateTo = $date->copy()->endOfMonth()->toDateString();
        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'perPage', 'dateFrom', 'dateTo', 'selectedAccount'])) {
            $this->resetPage();
        }
    }

    public function pilihAkun($accountCode)
    {
        $this->selectedAccount = $accountCode;
        $this->resetPage();
    }

    public function kembaliKeRingkasan()
    {
        $this->selectedAccount = '';
    }

    /** Daftar semua akun yang pernah bertransaksi. */
    public function getAccountsProperty()
    {
        return Transaction::select('account_code', 'account_name')
            ->whereNotNull('account_code')
            ->distinct()
            ->orderBy('account_code')
            ->get();
    }

    /** Ringkasan debit/kredit semua akun dalam periode (tampil saat belum pilih akun). */
    public function getSummaryProperty()
    {
        return Transaction::query()
            ->whereDate('transaction_date', '>=', $this->dateFrom)
            ->whereDate('transaction_date', '<=', $this->dateTo)
            ->when($this->search, function ($query) {
                $search = '%'.$this->search.'%';

                $query->where(function ($query) use ($search) {
                    $query->where('account_code', 'like', $search)
                        ->orWhere('account_name', 'like', $search);
                });
            })
            ->selectRaw('account_code, account_name, COALESCE(SUM(debit), 0) as debit, COALESCE(SUM(credit), 0) as credit')
            ->groupBy('account_code', 'account_name')
            ->orderBy('account_code')
            ->get();
    }

    /** Detail transaksi untuk akun yang dipilih (tanpa saldo berjalan). */
    public function getLedgerProperty()
    {
        if (! $this->selectedAccount) {
            return null;
        }

        $query = Transaction::where('account_code', $this->selectedAccount)
            ->whereDate('transaction_date', '>=', $this->dateFrom)
            ->whereDate('transaction_date', '<=', $this->dateTo)
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->orderBy('transaction_date')
            ->orderBy('id');

        $rows = $query->paginate($this->perPage);

        $account = $this->accounts->firstWhere('account_code', $this->selectedAccount);

        return [
            'account' => $account,
            'rows' => $rows,
        ];
    }

    public function render()
    {
        return view('livewire.ledger.index', [
            'summary' => $this->selectedAccount ? null : $this->summary,
            'ledger' => $this->selectedAccount ? $this->ledger : null,
        ])->layout('layouts.app');
    }
}
