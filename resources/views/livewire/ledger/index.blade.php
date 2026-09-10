<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-tight flex items-center gap-2">
                Buku Besar
            </h1>
            <p class="text-gray-400 mt-1">
                Rincian mutasi per akun
            </p>
        </div>
        @if ($ledger)
            <button wire:click="kembaliKeRingkasan"
                class="inline-flex items-center justify-center sm:justify-start gap-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-5 py-3 font-medium transition">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
                Kembali ke Ringkasan
            </button>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <input type="month" wire:model.live="selectedMonth"
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                <input wire:model.live.debounce.400ms="search" placeholder="Cari akun / deskripsi..."
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                <select wire:model.live="perPage"
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                    <option value="25">25 Data</option>
                    <option value="50">50 Data</option>
                    <option value="100">100 Data</option>
                </select>
            </div>
        </div>

        @if ($ledger)
            {{-- DETAIL SATU AKUN --}}
            <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-bold text-gray-900 dark:text-white">
                    {{ $ledger['account']->account_code ?? '' }} - {{ $ledger['account']->account_name ?? '' }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300">Tanggal</th>
                            <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300 hidden sm:table-cell">No Transaksi</th>
                            <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300">Keterangan</th>
                            <th class="text-right p-4 font-semibold text-gray-700 dark:text-gray-300">Debit</th>
                            <th class="text-right p-4 font-semibold text-gray-700 dark:text-gray-300">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($ledger['rows'] as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="p-4 text-gray-900 dark:text-gray-200 text-sm">
                                    {{ $row->transaction_date->format('d/m/Y') }}
                                </td>
                                <td class="p-4 text-gray-900 dark:text-gray-200 text-sm hidden sm:table-cell">
                                    {{ $row->transaction_number }}
                                </td>
                                <td class="p-4 text-gray-900 dark:text-gray-200 text-sm">
                                    {{ $row->description }}
                                </td>
                                <td class="p-4 text-right text-sm text-green-600 dark:text-green-400">
                                    {{ $row->debit > 0 ? number_format($row->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="p-4 text-right text-sm text-orange-600 dark:text-orange-400">
                                    {{ $row->credit > 0 ? number_format($row->credit, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    Tidak ada transaksi di periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 sm:p-5 border-t border-gray-200 dark:border-gray-700">
                {{ $ledger['rows']->links() }}
            </div>
        @else
            {{-- RINGKASAN SEMUA AKUN --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300">Kode</th>
                            <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300">Nama Akun</th>
                            <th class="text-right p-4 font-semibold text-gray-700 dark:text-gray-300">Debit</th>
                            <th class="text-right p-4 font-semibold text-gray-700 dark:text-gray-300">Kredit</th>
                            <th class="text-center p-4 font-semibold text-gray-700 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($summary as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="p-4 font-mono text-gray-900 dark:text-white text-sm">
                                    {{ $row->account_code }}
                                </td>
                                <td class="p-4 text-gray-900 dark:text-gray-200 text-sm">
                                    {{ $row->account_name }}
                                </td>
                                <td class="p-4 text-right text-sm text-green-600 dark:text-green-400">
                                    {{ number_format($row->debit, 0, ',', '.') }}
                                </td>
                                <td class="p-4 text-right text-sm text-orange-600 dark:text-orange-400">
                                    {{ number_format($row->credit, 0, ',', '.') }}
                                </td>
                                <td class="p-4 text-center">
                                    <button wire:click="pilihAkun('{{ $row->account_code }}')"
                                        class="text-emerald-600 dark:text-emerald-400 hover:underline text-sm">
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    Belum ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
