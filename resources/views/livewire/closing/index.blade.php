<div class="space-y-6">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Closing Tahunan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Rekap otomatis berdasarkan data Omzet, Pembelian, Biaya Operasional, dan Stok.
            </p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="flex-1 sm:flex-none">
                <label for="closing-year" class="sr-only">Tahun</label>
                <input id="closing-year" type="number" min="2020" max="2100" wire:model.live="year"
                    class="w-full sm:w-28 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <button wire:click="exportPdf" wire:loading.attr="disabled" wire:target="exportPdf"
                class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 disabled:opacity-60 disabled:cursor-not-allowed text-white inline-flex items-center gap-2 transition-colors text-sm whitespace-nowrap">

                <svg wire:loading wire:target="exportPdf" class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>

                <svg wire:loading.remove wire:target="exportPdf" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>

                <span class="hidden xs:inline sm:inline">Export PDF</span>
            </button>
        </div>
    </div>

    {{-- TABEL BULANAN --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden relative">

        <div wire:loading.flex wire:target="year" class="absolute inset-0 bg-white/60 dark:bg-gray-800/60 items-center justify-center z-10">
            <span class="text-sm text-gray-500 dark:text-gray-300">Memuat...</span>
        </div>

        {{-- DESKTOP: tabel --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/60">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400">Bulan</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400">Omzet</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400">Pembelian</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400">Biaya Operasional</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400">Stok Awal</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400">Stok Masuk</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400">Stok Keluar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($rows as $row)
                        <tr wire:key="closing-row-{{ $loop->index }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $row['month'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200 whitespace-nowrap">Rp {{ number_format($row['omzet'],0,',','.') }}</td>
                            <td class="px-4 py-3 text-right text-red-600 dark:text-red-400 whitespace-nowrap">Rp {{ number_format($row['internal'],0,',','.') }}</td>
                            <td class="px-4 py-3 text-right text-red-600 dark:text-red-400 whitespace-nowrap">Rp {{ number_format($row['operasional'],0,',','.') }}</td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-200">{{ number_format($row['stock_start'],0,',','.') }}</td>
                            <td class="px-4 py-3 text-center text-emerald-600 dark:text-emerald-400">{{ number_format($row['stock_in'],0,',','.') }}</td>
                            <td class="px-4 py-3 text-center text-red-600 dark:text-red-400">{{ number_format($row['stock_out'],0,',','.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-500 dark:text-gray-400">Belum ada data untuk tahun ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE / TABLET: kartu per bulan --}}
        <div class="lg:hidden divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($rows as $row)
                <div wire:key="closing-card-{{ $loop->index }}" class="p-4 space-y-2">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $row['month'] }}</p>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Omzet</span>
                        <span class="text-right text-gray-700 dark:text-gray-200">Rp {{ number_format($row['omzet'],0,',','.') }}</span>

                        <span class="text-gray-500 dark:text-gray-400">Pembelian</span>
                        <span class="text-right text-red-600 dark:text-red-400">Rp {{ number_format($row['internal'],0,',','.') }}</span>

                        <span class="text-gray-500 dark:text-gray-400">Biaya Operasional</span>
                        <span class="text-right text-red-600 dark:text-red-400">Rp {{ number_format($row['operasional'],0,',','.') }}</span>

                        <span class="text-gray-500 dark:text-gray-400 pt-1 border-t border-dashed border-gray-200 dark:border-gray-700 mt-1">Stok Awal</span>
                        <span class="text-right text-gray-700 dark:text-gray-200 pt-1 border-t border-dashed border-gray-200 dark:border-gray-700 mt-1">{{ number_format($row['stock_start'],0,',','.') }}</span>

                        <span class="text-gray-500 dark:text-gray-400">Stok Masuk</span>
                        <span class="text-right text-emerald-600 dark:text-emerald-400">{{ number_format($row['stock_in'],0,',','.') }}</span>

                        <span class="text-gray-500 dark:text-gray-400">Stok Keluar</span>
                        <span class="text-right text-red-600 dark:text-red-400">{{ number_format($row['stock_out'],0,',','.') }}</span>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Belum ada data untuk tahun ini.
                </div>
            @endforelse
        </div>

    </div>

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- RINGKASAN KEUANGAN: dipecah jadi 3 tahap perhitungan, gaya laporan laba rugi --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 sm:p-6 border border-gray-100 dark:border-gray-700">
            <h2 class="font-bold text-base sm:text-lg mb-5 text-gray-900 dark:text-white">Ringkasan Keuangan</h2>

            <div class="space-y-5">

                {{-- TAHAP 1: HPP --}}
                <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">
                        1. Harga Pokok Penjualan (HPP)
                    </p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">
                                Stok Awal ({{ number_format($stockAwal,0,',','.') }} &times; Rp 19.500)
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($stockAwalRupiah,0,',','.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">+ Pembelian</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($totalInternal,0,',','.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">
                                &minus; Stok Akhir ({{ number_format($stockEnd,0,',','.') }} &times; Rp 19.500)
                            </span>
                            <span class="font-medium text-red-600 dark:text-red-400">
                                &minus; Rp {{ number_format($stockEndRupiah,0,',','.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-2 mt-1 border-t border-gray-200 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-200">= HPP (Hasil 1)</span>
                            <span class="font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($hasil1,0,',','.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- TAHAP 2: LABA KOTOR --}}
                <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">
                        2. Laba Kotor
                    </p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Omzet</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($totalOmzet,0,',','.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">&minus; HPP (Hasil 1)</span>
                            <span class="font-medium text-red-600 dark:text-red-400">
                                &minus; Rp {{ number_format($hasil1,0,',','.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-2 mt-1 border-t border-gray-200 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-200">= Laba Kotor (Hasil 2)</span>
                            <span class="font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($hasil2,0,',','.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- TAHAP 3: LABA BERSIH --}}
                <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-400 mb-3">
                        3. Laba Bersih
                    </p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Laba Kotor (Hasil 2)</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($hasil2,0,',','.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">&minus; Biaya Operasional</span>
                            <span class="font-medium text-red-600 dark:text-red-400">
                                &minus; Rp {{ number_format($totalOperational,0,',','.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-2 mt-1 border-t border-emerald-200 dark:border-emerald-800">
                            <span class="font-bold text-gray-900 dark:text-white text-base">LABA BERSIH</span>
                            <span class="font-bold text-lg {{ $profit >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                                Rp {{ number_format($profit,0,',','.') }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- RINGKASAN STOK --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 sm:p-6 border border-gray-100 dark:border-gray-700 h-fit">
            <h2 class="font-bold text-base sm:text-lg mb-5 text-gray-900 dark:text-white">Ringkasan Stok</h2>

            <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 p-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Stok Akhir Tahun</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 text-xl">
                        {{ number_format($stockEnd,0,',','.') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>