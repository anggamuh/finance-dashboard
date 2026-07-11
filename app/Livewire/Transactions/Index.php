<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $dateFrom = '';

    public $dateTo = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = now()->endOfMonth()->format('Y-m-d');
    }

    public function updating($property)
    {
        if (in_array($property, [
            'search',
            'perPage',
            'dateFrom',
            'dateTo',
        ])) {
            $this->resetPage();
        }
    }

    #[On('delete')]
    public function delete($id = null)
    {
        if (! $id) {
            return;
        }

        $transaction = Transaction::find($id);

        if (! $transaction) {
            session()->flash('error', 'Transaksi tidak ditemukan.');
            return;
        }

        $transaction->delete();

        session()->flash('success', 'Transaksi berhasil dihapus.');
    }

    public function render()
    {
        $transactions = Transaction::query()
            ->whereNotIn('account_name', [
                'Bank BCA',
                'Petty Cash',
            ])
            ->where(function ($q) {
                $q->whereNull('description')
                  ->orWhere('description', 'not like', 'Saldo per%');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('account_name', 'like', "%{$this->search}%")
                      ->orWhere('transaction_number', 'like', "%{$this->search}%")
                      ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('transaction_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('transaction_date', '<=', $this->dateTo);
            })
            ->latest('transaction_date')
            ->paginate($this->perPage);

        return view('livewire.transactions.index', compact('transactions'))
            ->layout('layouts.app');
    }
}