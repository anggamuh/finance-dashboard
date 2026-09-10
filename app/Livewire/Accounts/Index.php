<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $editingId = null;

    public $showFormModal = false;

    public $code;

    public $name;

    public $parent_id;

    public $type;

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'code' => 'required|string|max:20',
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:accounts,id',
        'type' => 'required|in:asset,liability,equity,revenue,expense',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();

        $this->showFormModal = true;
    }

    public function closeModal()
    {
        $this->resetForm();

        $this->showFormModal = false;
    }

    public function resetForm()
    {
        $this->reset([
            'editingId',
            'code',
            'name',
            'parent_id',
            'type',
        ]);

        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        Account::updateOrCreate(
            [
                'id' => $this->editingId,
            ],
            [
                'code' => $this->code,
                'name' => $this->name,
                'parent_id' => $this->parent_id ?: null,
                'type' => $this->type,
            ]
        );

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => $this->editingId
                ? 'Akun berhasil diupdate.'
                : 'Akun berhasil ditambahkan.',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $account = Account::findOrFail($id);

        $this->editingId = $account->id;
        $this->code = $account->code;
        $this->name = $account->name;
        $this->parent_id = $account->parent_id;
        $this->type = $account->type;

        $this->showFormModal = true;
    }

    public function delete($id)
    {
        Account::findOrFail($id)->delete();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Akun berhasil dihapus.',
        ]);
    }

    public function getParentOptionsProperty()
    {
        return Account::query()
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->orderBy('code')
            ->get(['id', 'code', 'name']);
    }

    public function render()
    {
        return view('livewire.accounts.index', [
            'accounts' => Account::query()
                ->when($this->search, function ($query) {
                    $query->where('code', 'like', '%'.$this->search.'%')
                        ->orWhere('name', 'like', '%'.$this->search.'%');
                })
                ->orderBy('code')
                ->paginate(20),
            'parentOptions' => $this->parentOptions,
        ])->layout('layouts.app');
    }
}