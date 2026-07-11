<?php

namespace App\Livewire\Accounts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Account;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.accounts.index', [
            'accounts' => Account::query()
                ->when($this->search, function ($query) {
                    $query->where('code', 'like', '%' . $this->search . '%')
                          ->orWhere('name', 'like', '%' . $this->search . '%');
                })
                ->orderBy('code')
                ->paginate(20)
        ])->layout('layouts.app');
    }
}