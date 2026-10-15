<?php

namespace App\Livewire\InternalOperationalCosts;

use App\Models\InternalOperationalCost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $monthFilter = '';

    public $editingId = null;
    public $showFormModal = false;

    // Header bon
    public $transaction_date;
    public $invoice_number;
    public $supplier;
    public $notes;

    // Rincian item per bon
    public $items = [];

    protected function rules()
    {
        return [
            'transaction_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.key' => 'nullable|string',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'transaction_date' => 'tanggal bon',
            'items.*.item_name' => 'nama item',
            'items.*.qty' => 'qty',
            'items.*.price' => 'harga satuan',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMonthFilter()
    {
        $this->resetPage();
    }

    protected function blankItem(): array
    {
        return [
            'key' => (string) Str::uuid(),
            'item_name' => '',
            'qty' => 1,
            'price' => 0,
        ];
    }

    public function addItem()
    {
        $this->items[] = $this->blankItem();
    }

    public function removeItem($index)
    {
        if (count($this->items) <= 1 || !array_key_exists($index, $this->items)) {
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->resetValidation();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->transaction_date = now()->format('Y-m-d');
        $this->showFormModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $rows = collect($validated['items'])->map(function ($item) {
                $qty = (float) $item['qty'];
                $price = (float) $item['price'];

                return [
                    'item_name' => trim($item['item_name']),
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => round($qty * $price, 2),
                ];
            });

            $firstItem = $rows->first();
            $total = $rows->sum('subtotal');

            // KOMPATIBILITAS DATABASE LAMA:
            // kolom item_name, qty, price di tabel internal_operational_costs
            // masih NOT NULL. Isi dengan item pertama agar INSERT tidak gagal.
            // Semua item dan subtotal asli tetap disimpan di tabel rincian.
            $header = [
                'transaction_date' => $validated['transaction_date'],
                'invoice_number' => filled($validated['invoice_number'] ?? null)
                    ? trim($validated['invoice_number'])
                    : null,
                'supplier' => filled($validated['supplier'] ?? null)
                    ? trim($validated['supplier'])
                    : null,
                'item_name' => $firstItem['item_name'],
                'qty' => $firstItem['qty'],
                'price' => $firstItem['price'],
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ];

            if ($this->editingId !== null) {
                $cost = InternalOperationalCost::findOrFail($this->editingId);
                $cost->update($header);
            } else {
                $cost = InternalOperationalCost::create($header);
            }

            // Ganti rincian lama dalam transaksi database yang sama.
            // Jika gagal, update header dan penghapusan lama ikut rollback.
            $cost->items()->delete();
            $cost->items()->createMany($rows->all());
        });

        $this->resetForm();
        $this->showFormModal = false;
        $this->resetPage();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Bon dan seluruh rincian item berhasil disimpan.',
        ]);
    }

    public function edit($id)
    {
        $data = InternalOperationalCost::with('items')->findOrFail($id);
        $this->resetValidation();

        $this->editingId = $data->id;
        $this->transaction_date = $data->transaction_date?->format('Y-m-d');
        $this->invoice_number = $data->invoice_number;
        $this->supplier = $data->supplier;
        $this->notes = $data->notes;

        $this->items = $data->items->map(fn ($item) => [
            'key' => (string) Str::uuid(),
            'item_name' => $item->item_name,
            'qty' => (float) $item->qty,
            'price' => (float) $item->price,
        ])->values()->all();

        // Data yang dibuat sebelum fitur multi-item belum punya baris detail.
        // Angkat data lama dari tabel header supaya tetap bisa diedit.
        if (empty($this->items)) {
            $this->items = [[
                'key' => (string) Str::uuid(),
                'item_name' => $data->item_name ?? '',
                'qty' => (float) ($data->qty ?? 1),
                'price' => (float) ($data->price ?? 0),
            ]];
        }

        $this->showFormModal = true;
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $cost = InternalOperationalCost::findOrFail($id);
            $cost->items()->delete();
            $cost->delete();
        });

        $this->resetPage();
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data berhasil dihapus.',
        ]);
    }

    public function resetForm()
    {
        $this->reset([
            'editingId',
            'transaction_date',
            'invoice_number',
            'supplier',
            'notes',
        ]);

        $this->items = [$this->blankItem()];
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showFormModal = false;
    }

    public function render()
    {
        $totalThisMonth = InternalOperationalCost::query()
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('total');

        $totalThisWeek = InternalOperationalCost::query()
            ->whereBetween('transaction_date', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ])
            ->sum('total');

        $datas = InternalOperationalCost::query()
            ->with('items')
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('supplier', 'like', $search)
                        ->orWhere('invoice_number', 'like', $search)
                        ->orWhere('item_name', 'like', $search)
                        ->orWhereHas('items', fn ($itemQuery) => $itemQuery->where('item_name', 'like', $search));
                });
            })
            ->when($this->monthFilter, function ($query) {
                [$year, $month] = explode('-', $this->monthFilter);
                $query->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
            })
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(10);

        return view('livewire.internal-operational-costs.index', [
            'datas' => $datas,
            'totalThisMonth' => $totalThisMonth,
            'totalThisWeek' => $totalThisWeek,
        ])->layout('layouts.app');
    }
}
