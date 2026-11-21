<div class="space-y-6 text-slate-800 dark:text-slate-100">
    <div>
        <h1 class="text-2xl font-bold">Rekonsiliasi Bank</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Cocokkan mutasi bank dengan pembukuan secara aman, dengan persetujuan manual.</p>
    </div>

    @if(session('success')) <div class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300">{{ session('success') }}</div> @endif

    <div class="grid gap-5 lg:grid-cols-3">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
            <h2 class="font-semibold">Buat periode baru</h2>
            <form wire:submit="createBatch" class="mt-4 space-y-3">
                <label class="block text-sm">Nama periode
                    <input wire:model="name" placeholder="November 2026" class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900" required>
                </label>
                @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                <label class="block text-sm">Bank
                    <input wire:model="bankName" placeholder="BCA / Mandiri" class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900">
                </label>
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Buat periode</button>
            </form>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5 lg:col-span-2 dark:border-slate-700 dark:bg-slate-800">
            <h2 class="font-semibold">Pilih periode</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @forelse($batches as $item)
                    <button type="button" wire:click="selectBatch({{ $item->id }})"
                        class="rounded-lg border px-3 py-2 text-sm {{ $batch?->id === $item->id ? 'border-emerald-500 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'border-slate-200 dark:border-slate-600' }}">
                        {{ $item->name }} <span class="text-xs opacity-70">{{ $item->bank_name }}</span>
                    </button>
                @empty <p class="text-sm text-slate-500">Belum ada periode.</p> @endforelse
            </div>
        </section>
    </div>

    @if($batch)
        <div class="grid gap-3 sm:grid-cols-3">
            @foreach(['matched'=>'Cocok', 'suggested'=>'Perlu Konfirmasi', 'unmatched'=>'Belum Cocok'] as $status => $label)
                <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ number_format($counts[$status] ?? 0,0,',','.') }}</p>
                </div>
            @endforeach
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
            <h2 class="font-semibold">Import transaksi</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Upload CSV/XLS/XLSX langsung dari Excel. Pilih format sesuai kolom file, baris judul, dan sheet. Maksimum 10.000 transaksi/file. Upload ulang tidak diizinkan jika ada pasangan yang sudah disetujui.</p>
            <div class="mt-4 grid gap-3 rounded-xl bg-slate-50 p-4 dark:bg-slate-900/50 md:grid-cols-3">
                <label class="text-sm">Format kolom
                    <select wire:model="importMode" class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900">
                        <option value="separated">Masuk + Keluar (dua kolom)</option>
                        <option value="signed">Nominal positif / negatif</option>
                        <option value="typed">Nominal + jenis transaksi</option>
                    </select>
                </label>
                <label class="text-sm">Baris judul kolom
                    <input type="number" wire:model="headerRow" min="1" max="30" class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900">
                </label>
                <label class="text-sm">Nama sheet (opsional)
                    <input type="text" wire:model="sheetName" placeholder="Kosong = sheet aktif" class="mt-1 w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900">
                </label>
                @error('importMode') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                @error('headerRow') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                @error('sheetName') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">Header yang dikenali: Tanggal/Tgl/Date, Keterangan/Deskripsi/Uraian, Masuk/Penerimaan/Debit, Keluar/Pengeluaran/Kredit; atau Nominal/Amount dan Jenis/Type. Nomor bukti/referensi opsional. Cek bahwa Masuk dan Keluar sesuai arus uang pada file Anda.</p>
            <div class="mt-4 grid gap-5 md:grid-cols-2">
                <form wire:submit="import('bank')" class="space-y-2">
                    <label class="block text-sm font-semibold" for="bankFile">Mutasi bank</label>
                    <input id="bankFile" type="file" wire:model="bankFile" accept=".csv,.txt,.xls,.xlsx" class="block w-full text-sm">
                    @error('bankFile') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white dark:bg-slate-600" type="submit" wire:loading.attr="disabled">Upload Bank</button>
                </form>
                <form wire:submit="import('ledger')" class="space-y-2">
                    <label class="block text-sm font-semibold" for="ledgerFile">Data pembukuan</label>
                    <input id="ledgerFile" type="file" wire:model="ledgerFile" accept=".csv,.txt,.xls,.xlsx" class="block w-full text-sm">
                    @error('ledgerFile') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white dark:bg-slate-600" type="submit" wire:loading.attr="disabled">Upload Pembukuan</button>
                </form>
            </div>
            <div class="mt-5 rounded-xl border border-dashed border-emerald-300 bg-emerald-50/50 p-4 dark:border-emerald-800 dark:bg-emerald-950/20">
                <h3 class="font-semibold">Mutasi bank PDF (teks)</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Alternatif upload bank di atas. Format awal yang didukung: tanggal, keterangan, nominal, dan penanda DB/CR pada satu baris. PDF scan atau tabel bank dengan format lain perlu penyesuaian. Hasil wajib diperiksa sebelum disimpan.</p>
                <form wire:submit="previewBankPdf" class="mt-3 flex flex-wrap items-center gap-3">
                    <input type="file" wire:model="bankPdf" accept=".pdf,application/pdf" class="text-sm">
                    <button type="submit" wire:loading.attr="disabled" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Preview PDF</button>
                </form>
                @error('bankPdf') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                @if($pdfUnrecognized)
                    <div class="mt-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-900 dark:bg-amber-900/30 dark:text-amber-200">
                        <p class="font-semibold">Baris yang belum dikenali (contoh):</p>
                        @foreach($pdfUnrecognized as $line)
                            <p class="mt-1">Baris {{ $line['line'] }}: {{ $line['text'] }}</p>
                        @endforeach
                    </div>
                @endif
                @if($pdfPreview)
                    <div class="mt-4 overflow-x-auto">
                        <p class="mb-2 text-sm font-semibold">Preview {{ count($pdfPreview) }} transaksi — cek arah masuk/keluar dan nominalnya.</p>
                        <table class="w-full min-w-[600px] text-left text-xs">
                            <thead><tr><th class="p-2">Tanggal</th><th class="p-2">Keterangan</th><th class="p-2">Arah</th><th class="p-2 text-right">Nominal</th></tr></thead>
                            <tbody>
                                @foreach(array_slice($pdfPreview,0,20) as $row)
                                    <tr class="border-t border-slate-200 dark:border-slate-700">
                                        <td class="p-2">{{ $row['posted_at'] }}</td>
                                        <td class="p-2">{{ $row['description'] }}</td>
                                        <td class="p-2">{{ $row['direction'] === 'in' ? 'Masuk' : 'Keluar' }}</td>
                                        <td class="p-2 text-right">{{ number_format((float)$row['amount'],2,',','.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(count($pdfPreview)>20) <p class="mt-1 text-xs text-slate-500">Menampilkan 20 transaksi pertama.</p> @endif
                    </div>
                    <button type="button" wire:click="confirmBankPdf" wire:confirm="Sudah memeriksa transaksi dan nominal PDF? Simpan ke periode ini?" class="mt-3 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Konfirmasi & Simpan PDF</button>
                @endif
            </div>
            <button wire:click="runMatching" wire:loading.attr="disabled" class="mt-5 rounded-lg bg-emerald-600 px-5 py-2.5 font-semibold text-white">Jalankan Auto Matching</button>
        </div>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-200 p-5 dark:border-slate-700"><h2 class="font-semibold">Review transaksi bank</h2><p class="text-sm text-slate-500">Hanya pasangan yang unik dan nominal/arahnya sama yang disarankan otomatis.</p></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-900/50"><tr><th class="p-3">Tanggal</th><th class="p-3">Bank</th><th class="p-3">Nominal</th><th class="p-3">Pasangan pembukuan</th><th class="p-3">Status</th><th class="p-3">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($entries as $entry)
                        <tr wire:key="recon-{{ $entry->id }}">
                            <td class="p-3 whitespace-nowrap">{{ $entry->posted_at->format('d/m/Y') }}</td>
                            <td class="p-3"><div class="font-medium">{{ $entry->description }}</div><div class="text-xs text-slate-500">{{ $entry->reference }}</div></td>
                            <td class="p-3 whitespace-nowrap">{{ $entry->direction === 'in' ? '+' : '-' }} Rp {{ number_format($entry->amount,2,',','.') }}</td>
                            <td class="p-3">{{ $entry->match?->description ?? '—' }} @if($entry->match)<div class="text-xs text-slate-500">{{ $entry->match->posted_at->format('d/m/Y') }}</div>@endif</td>
                            <td class="p-3"><span class="rounded-lg bg-slate-100 px-2 py-1 text-xs dark:bg-slate-700">{{ match($entry->status) {'matched'=>'Cocok', 'suggested'=>'Saran '.$entry->confidence.'%', default=>'Belum cocok'} }}</span></td>
                            <td class="p-3 whitespace-nowrap">
                                @if($entry->status === 'suggested')
                                    <button wire:click="decide({{ $entry->id }}, true)" wire:confirm="Setujui pasangan transaksi ini?" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs text-white">Setujui</button>
                                    <button wire:click="decide({{ $entry->id }}, false)" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs dark:border-slate-600">Tolak</button>
                                @else — @endif
                            </td>
                        </tr>
                    @empty <tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada mutasi bank. Silakan upload file.</td></tr> @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $entries->links() }}</div>
        </section>
    @endif
</div>