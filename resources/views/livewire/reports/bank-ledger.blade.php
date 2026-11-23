<div class="space-y-6 text-slate-900 dark:text-slate-100">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-2xl font-bold">Laporan Kas & Bank</h1><p class="text-sm text-slate-500">Rekap transaksi hasil import dan input manual. Mutasi antar rekening bukan omzet operasional.</p></div>
        <button wire:click="exportCsv" class="rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white">Export CSV</button>
    </div>
    <div class="grid grid-cols-1 gap-3 rounded-xl bg-white p-4 shadow dark:bg-slate-800 sm:grid-cols-4">
        <label class="text-sm">Dari tanggal<input type="date" wire:model.live="dateFrom" class="mt-1 w-full rounded-lg text-slate-900"></label>
        <label class="text-sm">Sampai tanggal<input type="date" wire:model.live="dateTo" class="mt-1 w-full rounded-lg text-slate-900"></label>
        <label class="text-sm">Rekening<select wire:model.live="ledger" class="mt-1 w-full rounded-lg text-slate-900"><option value="ALL">Semua rekening</option><option>BCA</option><option>MANDIRI</option><option>KAS</option></select></label>
        <label class="text-sm">Cari<input wire:model.live.debounce.350ms="search" placeholder="Akun, uraian, invoice..." class="mt-1 w-full rounded-lg text-slate-900"></label>
    </div>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl bg-white p-4 shadow dark:bg-slate-800"><p class="text-sm text-slate-500">Total masuk / Debit</p><p class="text-xl font-bold text-emerald-600">Rp {{ number_format((float)$totalDebit,2,',','.') }}</p></div>
        <div class="rounded-xl bg-white p-4 shadow dark:bg-slate-800"><p class="text-sm text-slate-500">Total keluar / Kredit</p><p class="text-xl font-bold text-rose-600">Rp {{ number_format((float)$totalCredit,2,',','.') }}</p></div>
        <div class="rounded-xl bg-white p-4 shadow dark:bg-slate-800"><p class="text-sm text-slate-500">Mutasi bersih</p><p class="text-xl font-bold">Rp {{ number_format((float)$totalDebit-(float)$totalCredit,2,',','.') }}</p></div>
    </div>
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="overflow-x-auto rounded-xl bg-white p-4 shadow dark:bg-slate-800"><h2 class="mb-3 font-semibold">Ringkasan per rekening</h2>
            <table class="w-full text-sm"><thead><tr class="border-b"><th class="py-2 text-left">Rekening</th><th class="text-right">Transaksi</th><th class="text-right">Masuk</th><th class="text-right">Keluar</th></tr></thead><tbody>
            @foreach($byLedger as $item)<tr class="border-b border-slate-100 dark:border-slate-700"><td class="py-2">{{ $item->ledger }}</td><td class="text-right">{{ $item->total }}</td><td class="text-right">{{ number_format($item->masuk,0,',','.') }}</td><td class="text-right">{{ number_format($item->keluar,0,',','.') }}</td></tr>@endforeach
            </tbody></table>
        </div>
        <div class="overflow-x-auto rounded-xl bg-white p-4 shadow dark:bg-slate-800"><h2 class="mb-3 font-semibold">Ringkasan per DBACC</h2>
            <div class="max-h-72 overflow-y-auto"><table class="w-full text-sm"><thead><tr class="border-b"><th class="py-2 text-left">Akun</th><th class="text-right">Masuk</th><th class="text-right">Keluar</th></tr></thead><tbody>
            @foreach($byAccount as $item)<tr class="border-b border-slate-100 dark:border-slate-700"><td class="py-2">{{ $item->account_code }} — {{ $item->account_name }}</td><td class="text-right">{{ number_format($item->masuk,0,',','.') }}</td><td class="text-right">{{ number_format($item->keluar,0,',','.') }}</td></tr>@endforeach
            </tbody></table></div>
        </div>
    </div>
    <div class="overflow-x-auto rounded-xl bg-white shadow dark:bg-slate-800">
        <div class="p-4"><h2 class="font-semibold">Rincian transaksi</h2><p class="text-xs text-slate-500">Maksimal 300 baris ditampilkan; export CSV mengambil seluruh hasil filter.</p></div>
        <table class="min-w-[900px] w-full text-left text-sm"><thead class="bg-slate-100 dark:bg-slate-700"><tr>
            @foreach(['Tanggal','Rekening','No. Transaksi','DBACC','Akun','Uraian','Debit','Kredit'] as $heading)<th class="px-3 py-3">{{ $heading }}</th>@endforeach
        </tr></thead><tbody>@forelse($rows as $row)<tr class="border-t border-slate-100 dark:border-slate-700">
            <td class="px-3 py-2">{{ $row->entry_date->format('d/m/Y') }}</td><td class="px-3 py-2">{{ $row->ledger }}</td>
            <td class="px-3 py-2">{{ $row->document_number }}</td><td class="px-3 py-2">{{ $row->account_code }}</td>
            <td class="px-3 py-2">{{ $row->account_name }}</td><td class="px-3 py-2">{{ $row->description }}</td>
            <td class="px-3 py-2 text-right">{{ number_format((float)$row->debit,2,',','.') }}</td><td class="px-3 py-2 text-right">{{ number_format((float)$row->credit,2,',','.') }}</td>
        </tr>@empty<tr><td colspan="8" class="p-8 text-center text-slate-500">Belum ada transaksi untuk filter ini.</td></tr>@endforelse</tbody></table>
    </div>
</div>