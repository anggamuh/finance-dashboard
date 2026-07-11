<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-tight flex items-center gap-2">
                Transaksi
            </h1>
            <p class="text-gray-400 mt-1">
                Kelola transaksi manual
            </p>
        </div>
        <a href="{{ route('transactions.create') }}"
            class="inline-flex items-center justify-center sm:justify-start gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 font-medium transition">
            <x-heroicon-o-plus class="w-5 h-5" />
            Tambah Transaksi
        </a>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: @json(session('success')),
                        confirmButtonColor: '#10B981'
                    });
                } else {
                    // Fallback: simple alert
                    alert(@json(session('success')));
                }
            });
        </script>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <input wire:model.live="search" placeholder="Cari transaksi..." 
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                <input type="date" wire:model.live="dateFrom" 
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                <input type="date" wire:model.live="dateTo" 
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                <select wire:model.live="perPage" 
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                    <option value="10">10 Data</option>
                    <option value="25">25 Data</option>
                    <option value="50">50 Data</option>
                    <option value="100">100 Data</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300">Tanggal</th>
                        <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300 hidden sm:table-cell">Keterangan</th>
                        <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300">Akun</th>
                        <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300 hidden md:table-cell">Metode</th>
                        <th class="text-right p-4 font-semibold text-gray-700 dark:text-gray-300">Nominal</th>
                        <th class="text-center p-4 font-semibold text-gray-700 dark:text-gray-300 hidden lg:table-cell">Bukti</th>
                        <th class="text-center p-4 font-semibold text-gray-700 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="p-4 text-gray-900 dark:text-gray-200 text-sm">
                                {{ $trx->transaction_date->format('d/m/Y') }}
                            </td>
                            <td class="p-4 text-gray-900 dark:text-gray-200 text-sm hidden sm:table-cell">
                                {{ $trx->description ?: '-' }}
                            </td>
                            <td class="p-4">
                                <div class="font-semibold text-gray-900 dark:text-white text-sm">
                                    {{ $trx->account_code }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $trx->account_name }}
                                </div>
                            </td>
                            <td class="p-4 hidden md:table-cell">
                                @if ($trx->payment_method == 'Bank BCA')
                                    <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 text-xs font-medium gap-1">
                                        <x-heroicon-o-building-library class="w-3 h-3" /> BCA
                                    </span>
                                @elseif ($trx->payment_method == 'Petty Cash')
                                    <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-2 py-1 text-xs font-medium gap-1">
                                        <x-heroicon-o-banknotes class="w-3 h-3" /> Petty Cash
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 text-xs font-medium">
                                        {{ $trx->payment_method ?: '-' }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right font-semibold text-gray-900 dark:text-white text-sm">
                                Rp{{ number_format($trx->debit > 0 ? $trx->debit : $trx->credit, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center hidden lg:table-cell">
                                @if ($trx->proof_file)
                                    <a href="{{ asset('storage/' . $trx->proof_file) }}" target="_blank"
                                        class="text-emerald-600 dark:text-emerald-400 hover:underline text-sm">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('transactions.edit', $trx->id) }}" 
                                        class="inline-flex items-center justify-center p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </a>
                                    <button type="button"
                                        onclick="confirmDelete({{ $trx->id }})"
                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition">
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-gray-500 dark:text-gray-400 text-sm">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

        <div class="p-4 sm:p-5 border-t border-gray-200 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete(id) {
                if (typeof Swal === 'undefined') {
                    if (confirm('Yakin ingin menghapus transaksi ini?')) {
                        Livewire.dispatch('delete', { id: id });
                    }
                    return;
                }

                Swal.fire({
                    title: 'Yakin?',
                    text: 'Transaksi akan dihapus dan tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('delete', { id: id });
                    }
                });
            }
        </script>
    @endpush

</div>