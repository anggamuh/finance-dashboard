<?php

namespace App\Livewire\Revenues;

use App\Models\Revenue;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $monthFilter = '';

    public $editingId = null;

    public $showFormModal = false;

    public $dateFrom;

    public $dateTo;

    public $qty = 0;

    public $amount = 0;

    public $notes;

    protected $paginationTheme = 'tailwind';

    protected $rules = [

        'dateFrom' => 'required|date',
        'dateTo' => 'required|date|after_or_equal:dateFrom',

        'qty' => 'required|integer|min:1',

        'amount' => 'required|numeric|min:0',

        'notes' => 'nullable|string',

    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMonthFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'monthFilter']);
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();

        $this->dateFrom = now()->startOfWeek()->format('Y-m-d');
        $this->dateTo = now()->endOfWeek()->format('Y-m-d');

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

            'dateFrom',
            'dateTo',

            'qty',
            'amount',
            'notes',
        ]);

        $this->qty = 0;
        $this->amount = 0;

        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        Revenue::updateOrCreate(

            [
                'id' => $this->editingId,
            ],

            [

                'date_from' => $this->dateFrom,

                'date_to' => $this->dateTo,

                'qty' => $this->qty,

                'amount' => $this->amount,

                'notes' => $this->notes,

            ]

        );

        $this->dispatch('swal', [

            'icon' => 'success',

            'title' => 'Berhasil',

            'text' => $this->editingId
                ? 'Data omzet berhasil diupdate.'
                : 'Data omzet berhasil ditambahkan.',

        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $data = Revenue::findOrFail($id);

        $this->editingId = $data->id;

        $this->dateFrom = $data->date_from->format('Y-m-d');

        $this->dateTo = $data->date_to->format('Y-m-d');

        $this->qty = $data->qty;

        $this->amount = $data->amount;

        $this->notes = $data->notes;

        $this->showFormModal = true;
    }

    public function delete($id)
    {
        Revenue::findOrFail($id)->delete();

        $this->dispatch('swal', [

            'icon' => 'success',

            'title' => 'Berhasil',

            'text' => 'Data berhasil dihapus.',

        ]);
    }

    public function getTotalThisMonthProperty()
    {
        return Revenue::whereYear('date_from', now()->year)

            ->whereMonth('date_from', now()->month)

            ->sum('amount');
    }

    public function getTotalThisWeekProperty()
    {
        return Revenue::whereBetween('date_from', [

            now()->copy()->startOfWeek(),

            now()->copy()->endOfWeek(),

        ])->sum('amount');
    }

    public function getTotalQtyProperty()
    {
        return Revenue::sum('qty');
    }

    public function getTotalQtyMonthProperty()
    {
        return Revenue::whereYear('date_from', now()->year)
            ->whereMonth('date_from', now()->month)
            ->sum('qty');
    }

    public function getTotalAllProperty()
    {
        return Revenue::sum('amount');
    }

    public function render()
    {
        $datas = Revenue::query()

            ->when($this->search, function ($q) {

                $q->where('notes', 'like', "%{$this->search}%");

            })

            ->when($this->monthFilter, function ($q) {

                [$year, $month] = explode('-', $this->monthFilter);

                $q->whereYear('date_from', $year)

                    ->whereMonth('date_from', $month);

            })

            ->latest('date_from')

            ->paginate(10);

        return view('livewire.revenues.index', [
            'datas' => $datas,
            'totalThisMonth' => $this->totalThisMonth,
            'totalThisWeek' => $this->totalThisWeek,
            'totalAll' => $this->totalAll,
            'totalQty' => $this->totalQty,
            'totalQtyMonth' => $this->totalQtyMonth,
        ])->layout('layouts.app');
    }
}
