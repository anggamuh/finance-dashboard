<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Neraca Kas &amp; Bank — CV Second Star</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Format mengikuti workbook KAS-BANK-GABUNGAN 2026: GABUNGAN, MANDIRI, BCA, KAS.
            </p>
        </div>
        <div class="flex flex-wrap items-end gap-3">
            <label class="text-sm font-medium text-gray-600 dark:text-gray-300">
                Sampai tanggal
                <input type="date" wire:model.live="asOf"
                    class="mt-1 block rounded-lg border-gray-300 bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            </label>
            <label class="text-sm font-medium text-gray-600 dark:text-gray-300">
                Sheet / rekening
                <select wire:model.live="ledger"
                    class="mt-1 block rounded-lg border-gray-300 bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="GABUNGAN">GABUNGAN</option>
                    <option value="KAS">KAS (111)</option>
                    <option value="BCA">BCA (121)</option>
                    <option value="MANDIRI">MANDIRI (122)</option>
                </select>
            </label>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach(['KAS' => 'Kas (111)', 'BCA' => 'Bank BCA (121)', 'MANDIRI' => 'Bank Mandiri (122)'] as $key => $label)
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $label }}</p>
                <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                    Rp {{ number_format($summary[$key]['ending'], 2, ',', '.') }}
                </p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Debit {{ number_format($summary[$key]['debit'], 0, ',', '.') }} ·
                    Kredit {{ number_format($summary[$key]['credit'], 0, ',', '.') }}
                </p>
            </div>
        @endforeach
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/30">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Total Kas + Bank</p>
            <p class="mt-2 text-xl font-bold text-emerald-700 dark:text-emerald-300">
                Rp {{ number_format($totalBalance, 2, ',', '.') }}
            </p>
            <p class="mt-2 text-xs text-emerald-700 dark:text-emerald-400">{{ $periodLabel }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-100">
        <strong>Catatan saldo:</strong> sama seperti sheet Excel, Debit = uang masuk dan Kredit = uang keluar.
        SALDO berjalan dihitung terpisah untuk setiap rekening, dari saldo awal bulan yang disimpan di menu Kas &amp; Bank.
        Bila baris Excel juga mencantumkan saldo pembuka sebagai Debit, jangan isi saldo awal yang sama dua kali.
        Ini rekap Kas-Bank, belum neraca akuntansi lengkap (Aset = Kewajiban + Ekuitas).
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-4 py-4 dark:border-gray-700">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Sheet {{ $selectedLedger }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $rowCount }} transaksi sesuai filter · posisi sampai {{ $asOf }} · maksimal 500 baris ditampilkan.
                </p>
            </div>
            <input wire:model.live.debounce.350ms="search"
                placeholder="Cari DBACC, bukti, invoice, uraian..."
                class="w-full max-w-sm rounded-lg border-gray-300 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1500px] text-xs sm:text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-700/60 dark:text-gray-200">
                    <tr>
                        <th class="px-3 py-3 text-left">IDBANKKAS</th>
                        <th class="px-3 py-3 text-left">KODE JURNAL / DBACC</th>
                        <th class="px-3 py-3 text-left">NAMA AKUN</th>
                        <th class="px-3 py-3 text-left">TANGGAL</th>
                        <th class="px-3 py-3 text-left">INVOICE</th>
                        <th class="px-3 py-3 text-left">No. Bank</th>
                        <th class="px-3 py-3 text-left">BANK</th>
                        <th class="px-3 py-3 text-left">NO. CK/BG</th>
                        <th class="px-3 py-3 text-left">URAIAN</th>
                        <th class="px-3 py-3 text-right">DEBIT</th>
                        <th class="px-3 py-3 text-right">KREDIT</th>
                        <th class="px-3 py-3 text-right">SALDO</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($rows as $row)
                        <tr wire:key="excel-neraca-{{ $row['id'] }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="whitespace-nowrap px-3 py-2 font-semibold">{{ $row['bank_code'] }}</td>
                            <td class="whitespace-nowrap px-3 py-2">{{ $row['account_code'] ?: '—' }}</td>
                            <td class="px-3 py-2">{{ $row['account_name'] ?: '—' }}</td>
                            <td class="whitespace-nowrap px-3 py-2">{{ $row['date'] }}</td>
                            <td class="px-3 py-2">{{ $row['invoice'] ?: '—' }}</td>
                            <td class="whitespace-nowrap px-3 py-2">{{ $row['document_number'] ?: '—' }}</td>
                            <td class="px-3 py-2">{{ $row['channel'] ?: '—' }}</td>
                            <td class="px-3 py-2">{{ $row['cheque_number'] ?: '—' }}</td>
                            <td class="min-w-[230px] px-3 py-2">{{ $row['description'] ?: '—' }}</td>
                            <td class="whitespace-nowrap px-3 py-2 text-right font-medium text-emerald-600 dark:text-emerald-400">
                                {{ $row['debit'] > 0 ? number_format($row['debit'], 2, ',', '.') : '—' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-right font-medium text-rose-600 dark:text-rose-400">
                                {{ $row['credit'] > 0 ? number_format($row['credit'], 2, ',', '.') : '—' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-2 text-right font-semibold text-gray-900 dark:text-white">
                                {{ number_format($row['balance'], 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="p-8 text-center text-gray-500">
                            Tidak ada transaksi pada periode ini.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-200 px-4 py-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
            <span>Sheet GABUNGAN hanya menggabungkan tiga rekening; tidak menghitung transaksi dua kali.</span>
            <a href="{{ route('reports.bank-ledger') }}" class="font-semibold text-emerald-600 hover:underline">Buka Laporan Kas &amp; Bank untuk export CSV</a>
        </div>
    </div>
</div>