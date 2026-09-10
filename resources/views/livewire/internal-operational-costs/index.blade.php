<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                Biaya Operasional Internal
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Kelola biaya operasional internal (kebutuhan terkait stok) — per bon.
            </p>
        </div>

        <button
            wire:click="openCreateModal"
            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white inline-flex items-center justify-center gap-2 transition-colors text-sm">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 4a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V5a1 1 0 011-1z"/>
            </svg>
            Tambah Data
        </button>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Total Bulan Ini</p>
            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">
                Rp {{ number_format($totalThisMonth ?? 0, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Total Minggu Ini</p>
            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">
                Rp {{ number_format($totalThisWeek ?? 0, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Jumlah Transaksi (filter aktif)</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">
                {{ $datas->total() }}
            </p>
        </div>

    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">

        <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700 space-y-3">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">

                <h2 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">
                    Data Biaya
                </h2>

                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="month" wire:model.live="monthFilter"
                        class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">

                    <input type="text" wire:model.live.debounce.500ms="search"
                        placeholder="Cari item, supplier, atau no. invoice..."
                        class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

            </div>
        </div>

        <div class="relative">

            <div wire:loading.flex wire:target="search,monthFilter" class="absolute inset-0 bg-white/60 dark:bg-gray-800/60 items-center justify-center z-10">
                <span class="text-sm text-gray-500 dark:text-gray-300">Memuat...</span>
            </div>

            {{-- DESKTOP: tabel --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">No. Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Supplier</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Harga Satuan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datas as $item)
                            <tr wire:key="biaya-{{ $item->id }}" class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $item->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $item->invoice_number ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $item->supplier ?: '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                                    {{ $item->item_name }}
                                    @if($item->notes)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $item->notes }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-700 dark:text-gray-200">{{ $item->qty }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap text-gray-700 dark:text-gray-200">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-red-600 dark:text-red-400 whitespace-nowrap">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-2">
                                        <button wire:click="edit({{ $item->id }})" class="px-3 py-1 rounded bg-yellow-500 hover:bg-yellow-600 text-white text-sm transition-colors">Edit</button>
                                        <button type="button"
                                            x-on:click="
                                                Swal.fire({
                                                    title: 'Yakin hapus data ini?',
                                                    text: 'Data tidak bisa dikembalikan.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'Ya, hapus',
                                                    cancelButtonText: 'Batal',
                                                    confirmButtonColor: '#dc2626',
                                                    cancelButtonColor: '#6b7280',
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.delete({{ $item->id }})
                                                    }
                                                })
                                            "
                                            class="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-sm transition-colors">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-gray-500 dark:text-gray-400">
                                    @if($search || $monthFilter)
                                        Tidak ada data yang cocok dengan filter.
                                    @else
                                        Belum ada data.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE / TABLET: kartu --}}
            <div class="lg:hidden divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($datas as $item)
                    <div wire:key="biaya-card-{{ $item->id }}" class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $item->item_name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $item->transaction_date->format('d/m/Y') }}
                                    @if($item->invoice_number)
                                        &bull; {{ $item->invoice_number }}
                                    @endif
                                </p>
                            </div>
                            <p class="shrink-0 font-semibold text-red-600 dark:text-red-400 whitespace-nowrap">
                                Rp {{ number_format($item->total, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm mt-2 text-gray-600 dark:text-gray-300">
                            <span>Supplier: {{ $item->supplier ?: '-' }}</span>
                            <span class="text-right">Qty: {{ $item->qty }}</span>
                            <span class="col-span-2">Harga satuan: Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>

                        @if($item->notes)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $item->notes }}</p>
                        @endif

                        <div class="flex gap-2 mt-3">
                            <button wire:click="edit({{ $item->id }})" class="flex-1 px-3 py-2 rounded bg-yellow-500 hover:bg-yellow-600 text-white text-sm transition-colors">Edit</button>
                            <button type="button"
                                x-on:click="
                                    Swal.fire({
                                        title: 'Yakin hapus data ini?',
                                        text: 'Data tidak bisa dikembalikan.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, hapus',
                                        cancelButtonText: 'Batal',
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.delete({{ $item->id }})
                                        }
                                    })
                                "
                                class="flex-1 px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                        @if($search || $monthFilter)
                            Tidak ada data yang cocok dengan filter.
                        @else
                            Belum ada data.
                        @endif
                    </div>
                @endforelse

                @if($datas->count())
                    <div class="p-4 flex justify-between items-center bg-gray-50 dark:bg-gray-700/40 font-semibold text-sm">
                        <span class="text-gray-700 dark:text-gray-200">Total (halaman ini)</span>
                        <span class="text-red-600 dark:text-red-400">Rp {{ number_format($datas->sum('total'), 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

        </div>

        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $datas->links() }}
        </div>

    </div>

    {{-- Modal: Tambah / Edit Data --}}
    @if($showFormModal)
        <div class="fixed inset-0 z-40 flex items-center justify-center p-4" wire:key="modal-{{ $editingId ?? 'new' }}">

            <div class="absolute inset-0 bg-black/50" wire:click="closeModal"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto border border-gray-100 dark:border-gray-700">

                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Data' : 'Tambah Data' }}
                    </h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" aria-label="Tutup">
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="bio-transaction-date" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Bon</label>
                            <input id="bio-transaction-date" type="date" wire:model="transaction_date"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                            @error('transaction_date') <small class="text-red-500">{{ $message }}</small> @enderror
                        </div>

                        <div>
                            <label for="bio-invoice-number" class="text-sm font-medium text-gray-700 dark:text-gray-300">No. Invoice</label>
                            <input id="bio-invoice-number" type="text" wire:model="invoice_number" placeholder="Opsional"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                            @error('invoice_number') <small class="text-red-500">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="bio-supplier" class="text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                        <input id="bio-supplier" type="text" wire:model="supplier" placeholder="Opsional"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                        @error('supplier') <small class="text-red-500">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label for="bio-item-name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Item / Keterangan</label>
                        <input id="bio-item-name" type="text" wire:model="item_name" placeholder="Contoh: kardus packing, lakban, ongkir supplier"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                        @error('item_name') <small class="text-red-500">{{ $message }}</small> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="bio-qty" class="text-sm font-medium text-gray-700 dark:text-gray-300">Qty</label>
                            <input id="bio-qty" type="number" min="1" step="1" wire:model.live="qty"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                            @error('qty') <small class="text-red-500">{{ $message }}</small> @enderror
                        </div>

                        <div>
                            <label for="bio-price" class="text-sm font-medium text-gray-700 dark:text-gray-300">Harga Satuan</label>
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">Rp</span>
                                <input id="bio-price" type="number" min="0" step="1" wire:model.live="price" placeholder="0"
                                    class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white pl-9 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            @error('price') <small class="text-red-500">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 px-4 py-3 flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">
                            Rp {{ number_format(($qty ?: 0) * ($price ?: 0), 0, ',', '.') }}
                        </span>
                    </div>

                    <div>
                        <label for="bio-notes" class="text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                        <textarea id="bio-notes" wire:model="notes" rows="2" placeholder="Opsional"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        @error('notes') <small class="text-red-500">{{ $message }}</small> @enderror
                    </div>

                    <div class="flex gap-2 justify-end pt-2">
                        <button wire:click="closeModal" class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white transition-colors">
                            Batal
                        </button>
                        <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                            class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed text-white inline-flex items-center gap-2 transition-colors">
                            <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            {{ $editingId ? 'Update' : 'Simpan' }}
                        </button>
                    </div>

                </div>

            </div>

        </div>
    @endif

</div>
