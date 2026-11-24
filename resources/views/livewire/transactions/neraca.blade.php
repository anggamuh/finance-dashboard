<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Neraca — Posisi Kas &amp; Bank</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Saldo kas dan rekening pada tanggal tertentu, berdasarkan saldo awal dan mutasi yang tercatat.</p>
        </div>
        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
            Posisi per tanggal
            <input type="date" wire:model.live="asOf" class="ml-2 rounded-lg border-slate-300 text-slate-900 dark:border-slate-600">
        </label>
    </div>
    <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-950 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-100">
        <strong>Belum neraca akuntansi lengkap.</strong> Data Excel Kas-Bank merekam mutasi kas, bukan jurnal berpasangan seluruh aset, utang, dan modal. Saldo di bawah adalah posisi kas/bank; bagian akun lain hanya menunjukkan mutasi akun yang tersedia, bukan saldo akhir buku besar. Tidak digunakan untuk menyatakan Aset = Kewajiban + Ekuitas.
    </div>
    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        @foreach($ledgerRows as $row)
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ $row->ledger }}</p>
                <p class="mt-2 text-xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($row->ending, 2, ',', '.') }}</p>
                <div class="mt-3 space-y-1 text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex justify-between"><span>Saldo awal bulan</span><span>{{ number_format($row->opening, 2, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Uang masuk</span><span class="text-emerald-600">+{{ number_format($row->money_in, 2, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Uang keluar</span><span class="text-rose-600">-{{ number_format($row->money_out, 2, ',', '.') }}</span></div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="font-semibold text-slate-900 dark:text-white">Total saldo Kas &amp; Bank</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tidak termasuk piutang, persediaan, aset tetap, utang atau modal.</p>
            </div>
            <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totalBank, 2, ',', '.') }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        @foreach($accountGroups as $group)
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h3 class="mb-1 font-semibold text-slate-900 dark:text-white">{{ $group['label'] }}</h3>
                <p class="mb-3 text-xs text-slate-500">Mutasi dari transaksi non-Kas-Bank sampai tanggal terpilih, bukan saldo neraca.</p>
                <div class="max-h-72 overflow-y-auto">
                    <table class="w-full text-sm"><thead><tr class="border-b text-xs text-slate-500 dark:border-slate-700"><th class="py-2 text-left">Akun</th><th class="py-2 text-right">Mutasi</th></tr></thead>
                        <tbody>
                            @forelse($group['rows'] as $row)
                                <tr class="border-b border-slate-100 dark:border-slate-700">
                                    <td class="py-2 pr-2"><span class="font-medium">{{ $row['code'] }}</span> — {{ $row['name'] }}</td>
                                    <td class="py-2 text-right">{{ number_format($row['movement'], 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-4 text-sm text-slate-500">Belum ada akun {{ strtolower($group['label']) }}.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>