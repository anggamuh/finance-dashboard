<div class="space-y-6 text-slate-800 dark:text-slate-100">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-2xl font-bold">Kas & Bank</h1><p class="text-sm text-slate-500 dark:text-slate-400">Input dan import buku Kas, BCA, Mandiri tanpa mengubah data Accurate.</p></div>
        <button wire:click="exportCsv" class="rounded-xl bg-emerald-600 px-4 py-2 font-semibold text-white">Export CSV</button>
    </div>
    @if(session('success')) <div class="rounded-xl border border-emerald-300 bg-emerald-50 p-3 text-emerald-800">{{ session('success') }}</div> @endif
    <div class="grid gap-4 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow dark:bg-slate-800"><p class="text-sm text-slate-500">Saldo awal</p><p class="text-xl font-bold">Rp {{ number_format((float)$opening,2,',','.') }}</p></div>
        <div class="rounded-xl bg-white p-4 shadow dark:bg-slate-800"><p class="text-sm text-slate-500">Debit / Masuk</p><p class="text-xl font-bold text-emerald-600">Rp {{ number_format($totalDebit,2,',','.') }}</p></div>
        <div class="rounded-xl bg-white p-4 shadow dark:bg-slate-800"><p class="text-sm text-slate-500">Kredit / Keluar</p><p class="text-xl font-bold text-rose-600">Rp {{ number_format($totalCredit,2,',','.') }}</p></div>
        <div class="rounded-xl bg-white p-4 shadow dark:bg-slate-800"><p class="text-sm text-slate-500">Saldo akhir</p><p class="text-xl font-bold">Rp {{ number_format($ending,2,',','.') }}</p></div>
    </div>
    <div class="rounded-xl bg-white p-5 shadow dark:bg-slate-800">
        <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
            <label class="text-sm">Rekening<select wire:model.live="ledger" class="mt-1 w-full rounded-lg text-slate-900"><option>BCA</option><option>MANDIRI</option><option>KAS</option></select></label>
            <label class="text-sm">Bulan<select wire:model.live="month" class="mt-1 w-full rounded-lg text-slate-900">@for($i=1;$i<=12;$i++)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></label>
            <label class="text-sm">Tahun<input wire:model.live="year" type="number" class="mt-1 w-full rounded-lg text-slate-900"></label>
            <label class="text-sm">Saldo awal<input wire:model="opening" type="number" step="0.01" class="mt-1 w-full rounded-lg text-slate-900"></label>
            <div class="flex items-end"><button wire:click="saveOpening" class="rounded-lg border border-emerald-500 px-3 py-2 text-sm">Simpan saldo awal</button></div>
        </div>
        <p class="mt-2 text-xs text-slate-500">Saldo awal diatur per rekening, bulan, dan tahun. Debit menambah saldo, kredit mengurangi saldo.</p>
    </div>
    <div class="grid gap-4 xl:grid-cols-2">
        <div class="rounded-xl bg-white p-5 shadow dark:bg-slate-800">
            <h2 class="mb-3 font-semibold">Import Excel lama</h2>
            <p class="mb-3 text-sm text-slate-500">Baca sheet BCA, MANDIRI, KAS. GABUNGAN hanya rekap, tidak diimport. Maksimal 10 MB.</p>
            <input type="file" wire:model="workbook" accept=".xlsx,.xls" class="w-full text-sm">
            @error('workbook') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            <div class="mt-3 flex gap-2"><button wire:click="previewImport" wire:loading.attr="disabled" class="rounded-lg border px-4 py-2 text-sm">Preview</button>
                @if($preview)<button wire:click="import" wire:loading.attr="disabled" wire:confirm="Import transaksi dari ketiga sheet?" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white">Konfirmasi import</button>@endif
            </div>
            <div wire:loading wire:target="previewImport,import" class="mt-2 text-sm">Memproses Excel...</div>
            @if($preview)<div class="mt-3 rounded-lg bg-slate-100 p-3 text-sm dark:bg-slate-700">Dibaca: {{ $preview['total'] }} transaksi. Dilewati: {{ $preview['skipped'] }} baris.<br>
                @foreach($preview['by_ledger'] as $name=>$count) {{ $name }}: {{ $count }} &nbsp; @endforeach
                @foreach($preview['warnings'] as $warning)<p class="text-amber-600">{{ $warning }}</p>@endforeach
            </div>@endif
        </div>
        <form wire:submit="save" class="rounded-xl bg-white p-5 shadow dark:bg-slate-800">
            <h2 class="mb-3 font-semibold">{{ $editingId ? 'Edit transaksi' : 'Input transaksi harian' }}</h2>
            <div class="grid grid-cols-2 gap-3">
                <label class="text-sm">Tanggal<input type="date" wire:model="entry_date" class="mt-1 w-full rounded-lg text-slate-900"></label>
                <label class="text-sm">Nomor transaksi<input wire:model="document_number" class="mt-1 w-full rounded-lg text-slate-900"></label>
                <label class="text-sm">DBACC<input wire:model="account_code" class="mt-1 w-full rounded-lg text-slate-900"></label>
                <label class="text-sm">Nama akun<input wire:model="account_name" class="mt-1 w-full rounded-lg text-slate-900"></label>
                <label class="text-sm">Invoice<input wire:model="invoice" class="mt-1 w-full rounded-lg text-slate-900"></label>
                <label class="text-sm">Bank/Metode<input wire:model="payment_channel" class="mt-1 w-full rounded-lg text-slate-900"></label>
                <label class="text-sm">Debit / Masuk<input type="number" step="0.01" min="0" wire:model="debit" class="mt-1 w-full rounded-lg text-slate-900"></label>
                <label class="text-sm">Kredit / Keluar<input type="number" step="0.01" min="0" wire:model="credit" class="mt-1 w-full rounded-lg text-slate-900"></label>
                <label class="col-span-2 text-sm">Uraian<input wire:model="description" class="mt-1 w-full rounded-lg text-slate-900"></label>
            </div>
            @if($errors->any())<div class="mt-2 text-sm text-red-500">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <div class="mt-4 flex gap-2"><button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white">Simpan</button><button type="button" wire:click="resetForm" class="rounded-lg border px-4 py-2">Bersihkan</button></div>
        </form>
    </div>
    <div class="overflow-hidden rounded-xl bg-white shadow dark:bg-slate-800">
        <div class="p-4"><input wire:model.live.debounce.350ms="search" placeholder="Cari transaksi, invoice, akun..." class="w-full max-w-lg rounded-lg text-slate-900"></div>
        <div class="overflow-x-auto"><table class="w-full min-w-[1050px] text-left text-sm">
            <thead class="bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200"><tr>
                @foreach(['Tanggal','No. Bukti','DBACC','Akun','Invoice','Uraian','Debit','Kredit','Saldo','Aksi'] as $col)<th class="px-3 py-3">{{ $col }}</th>@endforeach
            </tr></thead>
            <tbody>@forelse($rows as $row)<tr wire:key="bank-entry-{{ $row->id }}" class="border-t border-slate-200 dark:border-slate-700">
                <td class="whitespace-nowrap px-3 py-2">{{ $row->entry_date->format('d/m/Y') }}</td>
                <td class="px-3 py-2">{{ $row->document_number }}</td><td class="px-3 py-2">{{ $row->account_code }}</td>
                <td class="px-3 py-2">{{ $row->account_name }}</td><td class="px-3 py-2">{{ $row->invoice }}</td>
                <td class="px-3 py-2">{{ $row->description }}</td>
                <td class="px-3 py-2 text-right">{{ number_format((float)$row->debit,2,',','.') }}</td>
                <td class="px-3 py-2 text-right">{{ number_format((float)$row->credit,2,',','.') }}</td>
                <td class="px-3 py-2 text-right font-semibold">{{ number_format($row->running_balance,2,',','.') }}</td>
                <td class="whitespace-nowrap px-3 py-2"><button wire:click="edit({{ $row->id }})" class="text-emerald-600">Edit</button> <button wire:click="remove({{ $row->id }})" wire:confirm="Hapus transaksi ini?" class="ml-2 text-red-500">Hapus</button></td>
            </tr>@empty<tr><td colspan="10" class="p-8 text-center text-slate-500">Belum ada transaksi pada periode ini.</td></tr>@endforelse</tbody>
        </table></div>
    </div>
</div>