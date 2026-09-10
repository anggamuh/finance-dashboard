<?php

namespace App\Livewire\InternalOperationalCosts;

use App\Models\InternalOperationalCost;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $monthFilter = '';

    public $date;
    public $description;
    public $amount;

    public $editingId = null;
    public $showFormModal = false;

    public $transaction_date;
    public $invoice_number;
    public $supplier;
    public $item_name;
    public $qty = 1;
    public $price = 0;
    public $total = 0;
    public $notes;

    protected $rules = [
        'transaction_date' => 'required|date',
        'item_name'       => 'required',
        'qty'             => 'required|numeric|min:1',
        'price'           => 'required|numeric|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMonthFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();

        $this->transaction_date = now()->format('Y-m-d');

        $this->showFormModal = true;
    }

    public function save()
    {
        $this->validate();

        $total = (float) $this->qty * (float) $this->price;

        InternalOperationalCost::updateOrCreate(
            ['id' => $this->editingId],
            [
                'transaction_date' => $this->transaction_date,
                'invoice_number'   => $this->invoice_number,
                'supplier'         => $this->supplier,
                'item_name'        => $this->item_name,
                'qty'              => $this->qty,
                'price'            => $this->price,
                'total'            => $total,
                'notes'            => $this->notes,
            ]
        );

        $this->resetForm();
        $this->showFormModal = false;

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => 'Data berhasil disimpan.',
        ]);
    }

    public function edit($id)
    {
        $data = InternalOperationalCost::findOrFail($id);

        $this->editingId = $data->id;

        $this->transaction_date = $data->transaction_date
            ? $data->transaction_date->format('Y-m-d')
            : null;

        $this->invoice_number = $data->invoice_number;
        $this->supplier = $data->supplier;
        $this->item_name = $data->item_name;
        $this->qty = $data->qty;
        $this->price = $data->price;
        $this->total = $data->total;
        $this->notes = $data->notes;

        $this->showFormModal = true;
    }

    public function delete($id)
    {
        InternalOperationalCost::findOrFail($id)->delete();

        $this->resetPage();

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => 'Data berhasil dihapus.',
        ]);
    }

    public function resetForm()
    {
        $this->reset([
            'editingId',
            'transaction_date',
            'invoice_number',
            'supplier',
            'item_name',
            'qty',
            'price',
            'total',
            'notes',
        ]);

        $this->qty = 1;
        $this->price = 0;
        $this->total = 0;

        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->resetForm();

        $this->showFormModal = false;
    }

    public function render()
    {
        /*
        |--------------------------------------------------------------------------
        | TOTAL BULAN INI
        |--------------------------------------------------------------------------
        | Selalu mengambil semua transaksi pada bulan berjalan.
        | Tidak terpengaruh oleh search atau monthFilter.
        */
        $totalThisMonth = InternalOperationalCost::query()
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('total');

        /*
        |--------------------------------------------------------------------------
        | TOTAL MINGGU INI
        |--------------------------------------------------------------------------
        | Mengambil semua transaksi dari awal sampai akhir minggu berjalan.
        */
        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();

        $totalThisWeek = InternalOperationalCost::query()
            ->whereBetween('transaction_date', [
                $startOfWeek,
                $endOfWeek,
            ])
            ->sum('total');

        /*
        |--------------------------------------------------------------------------
        | DATA TABEL
        |--------------------------------------------------------------------------
        */
        $datas = InternalOperationalCost::query()
            ->when($this->search, function ($q) {
                $search = '%' . $this->search . '%';

                $q->where(function ($query) use ($search) {
                    $query->where('item_name', 'like', $search)
                        ->orWhere('supplier', 'like', $search)
                        ->orWhere('invoice_number', 'like', $search);
                });
            })
            ->when($this->monthFilter, function ($q) {
                [$year, $month] = explode('-', $this->monthFilter);

                $q->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
            })
            ->latest('transaction_date')
            ->paginate(10);

        return view('livewire.internal-operational-costs.index', [
            'datas' => $datas,

            // Kirim langsung hasil perhitungan ke Blade
            'totalThisMonth' => $totalThisMonth,
            'totalThisWeek' => $totalThisWeek,
        ])->layout('layouts.app');
    }
}