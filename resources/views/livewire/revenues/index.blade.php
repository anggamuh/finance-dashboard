<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                Omzet
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Kelola data omzet mingguan.
            </p>
        </div>

        <button wire:click="openCreateModal"
            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white inline-flex items-center justify-center gap-2 transition-colors shadow-sm text-sm">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 4a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V5a1 1 0 011-1z" />
            </svg>
            Tambah Omzet
        </button>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Omzet Bulan Ini</p>
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l4-4 3 3 5-6" />
                </svg>
            </div>
            <p class="text-lg sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">
                Rp {{ number_format($totalThisMonth, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Omzet Minggu Ini</p>
                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-lg sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                Rp {{ number_format($totalThisWeek, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Total Seluruh Omzet</p>
                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .672-3 1.5S10.343 11 12 11s3 .672 3 1.5-1.343 1.5-3 1.5m0-6V6m0 8v2m9-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-lg sm:text-2xl font-bold text-orange-600 dark:text-orange-400 mt-2">
                Rp {{ number_format($totalAll, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Qty Bulan Ini</p>
                <svg class="w-4 h-4 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <p class="text-lg sm:text-2xl font-bold text-purple-600 dark:text-purple-400 mt-2">
                {{ number_format($totalQtyMonth, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 sm:p-5 border border-gray-100 dark:border-gray-700 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Total Seluruh Qty</p>
                <svg class="w-4 h-4 text-pink-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </div>
            <p class="text-lg sm:text-2xl font-bold text-pink-600 dark:text-pink-400 mt-2">
                {{ number_format($totalQty, 0, ',', '.') }}
            </p>
        </div>

    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">

        <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row gap-3 md:items-center justify-between">

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <input wire:model.live.debounce.500ms="search" type="text" placeholder="Cari catatan..."
                        class="w-full sm:w-64 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">

                    <input type="month" wire:model.live="monthFilter"
                        class="w-full sm:w-auto rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                @if($search || $monthFilter)
                    <button type="button" wire:click="resetFilters"
                        class="text-sm text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 inline-flex items-center gap-1 self-start md:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reset Filter
                    </button>
                @endif

            </div>
        </div>

        <div class="relative">

            <div wire:loading.flex wire:target="search,monthFilter"
                class="absolute inset-0 bg-white/60 dark:bg-gray-800/60 items-center justify-center z-10 text-sm text-gray-500 dark:text-gray-300">
                Memuat...
            </div>

            {{-- DESKTOP: tabel --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Periode</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Omzet</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Catatan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datas as $item)
                            <tr wire:key="omzet-{{ $item->id }}" class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-200">
                                    @if(!$item->date_from || !$item->date_to)
                                        <span class="text-gray-400 italic">Belum ada tanggal</span>
                                    @elseif($item->date_from->isSameMonth($item->date_to))
                                        {{ $item->date_from->format('d') }} - {{ $item->date_to->format('d/m/Y') }}
                                    @else
                                        {{ $item->date_from->format('d/m/Y') }} - {{ $item->date_to->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ number_format($item->qty, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $item->notes ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-2">
                                        <button wire:click="edit({{ $item->id }})" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 rounded text-white text-sm transition-colors">Edit</button>
                                        <button type="button"
                                            x-on:click="
                                                Swal.fire({
                                                    title: 'Yakin hapus data omzet ini?',
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
                                            class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-white text-sm transition-colors">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        </svg>
                                        @if($search || $monthFilter)
                                            <span>Tidak ada data yang cocok dengan filter.</span>
                                        @else
                                            <span>Belum ada data omzet.</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE: kartu --}}
            <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($datas as $item)
                    <div wire:key="omzet-card-{{ $item->id }}" class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    @if(!$item->date_from || !$item->date_to)
                                        <span class="text-gray-400 italic">Belum ada tanggal</span>
                                    @elseif($item->date_from->isSameMonth($item->date_to))
                                        {{ $item->date_from->format('d') }} - {{ $item->date_to->format('d/m/Y') }}
                                    @else
                                        {{ $item->date_from->format('d/m/Y') }} - {{ $item->date_to->format('d/m/Y') }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $item->notes ?: '-' }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Rp {{ number_format($item->amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-400">Qty: {{ number_format($item->qty, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-3">
                            <button wire:click="edit({{ $item->id }})" class="flex-1 px-3 py-2 bg-yellow-500 hover:bg-yellow-600 rounded text-white text-sm transition-colors">Edit</button>
                            <button type="button"
                                x-on:click="
                                    Swal.fire({
                                        title: 'Yakin hapus data omzet ini?',
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
                                class="flex-1 px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-white text-sm transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            @if($search || $monthFilter)
                                <span class="text-sm">Tidak ada data yang cocok dengan filter.</span>
                            @else
                                <span class="text-sm">Belum ada data omzet.</span>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

        </div>

        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $datas->links() }}
        </div>

    </div>

    {{-- Modal --}}
    @if ($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:key="revenue-modal-{{ $editingId ?? 'new' }}">

            <div class="absolute inset-0 bg-black/50" wire:click="closeModal"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg p-6 border border-gray-100 dark:border-gray-700 max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Omzet' : 'Tambah Omzet' }}
                    </h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" aria-label="Tutup">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 001.06 1.06L10 11.06l3.72 3.72a.75.75 0 001.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-5">

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Periode (Minggu)</label>
                        <div class="grid grid-cols-2 gap-3 mt-1">
                            <div>
                                <label for="omzet-date-from" class="text-xs text-gray-500 dark:text-gray-400">Tanggal Mulai</label>
                                <input id="omzet-date-from" type="date" wire:model="dateFrom"
                                    class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                                @error('dateFrom') <small class="text-red-500">{{ $message }}</small> @enderror
                            </div>
                            <div>
                                <label for="omzet-date-to" class="text-xs text-gray-500 dark:text-gray-400">Tanggal Selesai</label>
                                <input id="omzet-date-to" type="date" wire:model="dateTo"
                                    class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                                @error('dateTo') <small class="text-red-500">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="omzet-qty" class="text-sm font-medium text-gray-700 dark:text-gray-300">Qty</label>
                        <input id="omzet-qty" type="number" min="1" wire:model="qty"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                        @error('qty') <small class="text-red-500">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label for="omzet-amount" class="text-sm font-medium text-gray-700 dark:text-gray-300">Omzet</label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">Rp</span>
                            <input id="omzet-amount" type="number" min="0" wire:model="amount"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white pl-9 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        @error('amount') <small class="text-red-500">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label for="omzet-notes" class="text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                        <textarea id="omzet-notes" rows="4" wire:model="notes"
                            placeholder="Contoh : Omzet minggu pertama Shopee + TikTok"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        @error('notes') <small class="text-red-500">{{ $message }}</small> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button wire:click="closeModal" class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white transition-colors">
                            Batal
                        </button>
                        <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                            class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed text-white inline-flex items-center gap-2 transition-colors">
                            <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" />
                                <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                            </svg>
                            {{ $editingId ? 'Update' : 'Simpan' }}
                        </button>
                    </div>

                </div>

            </div>

        </div>
    @endif

</div>
