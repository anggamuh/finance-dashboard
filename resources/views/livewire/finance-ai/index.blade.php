<div class="mx-auto max-w-7xl space-y-6 text-slate-800 dark:text-slate-100">
    <header class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400">
                <x-heroicon-o-sparkles class="h-4 w-4"/> Finance Intelligence
            </div>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">Finance AI Assistant</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tanya data keuangan yang sudah dihitung Laravel. AI hanya membaca ringkasan, tidak mengubah transaksi.</p>
        </div>
        <button type="button" wire:click="clearChat" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Percakapan baru</button>
    </header>

    <div class="grid gap-4 sm:grid-cols-3">
        @foreach([['label'=>'Beban operasional','value'=>$dashboard['total']],['label'=>'Bulan sebelumnya','value'=>$dashboard['previous_total']],['label'=>'Selisih beban','value'=>$dashboard['delta']]] as $metric)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
                <p class="text-sm text-slate-500">{{ $metric['label'] }}</p>
                <p class="mt-2 text-2xl font-bold">Rp {{ number_format($metric['value'],0,',','.') }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
            <h2 class="font-semibold">Tren beban operasional (6 bulan)</h2>
            <p class="text-xs text-slate-500">Dihitung langsung dari transaksi Accurate</p>
            @php($chartMax = max(1, collect($dashboard['chart'])->max('total')))
            <div class="mt-5 flex h-44 items-end gap-3">
                @foreach($dashboard['chart'] as $point)
                    <div class="flex min-w-0 flex-1 flex-col items-center gap-2">
                        <span class="text-center text-[10px] text-slate-500">{{ number_format($point['total']/1000000,1,',','.') }} jt</span>
                        <div class="flex h-28 w-full items-end overflow-hidden rounded bg-slate-100 dark:bg-slate-800" title="{{ $point['month'] }}: Rp {{ number_format($point['total'],0,',','.') }}">
                            <div class="w-full rounded-t bg-emerald-500" style="height: {{ max(0,min(100,($point['total']/$chartMax)*100)) }}%"></div>
                        </div>
                        <span class="truncate text-[10px] text-slate-500">{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
            <h2 class="font-semibold">Financial Insights</h2>
            <p class="text-xs text-slate-500">Ringkasan deterministik, tanpa biaya API tambahan</p>
            @foreach($dashboard['insights'] as $insight)
                <div class="mt-3 rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                    <p class="text-sm font-semibold">{{ $insight['title'] }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $insight['detail'] }}</p>
                </div>
            @endforeach
        </section>
    </div>
    <div class="grid gap-5 lg:grid-cols-[1fr_280px]">
        <section class="flex min-h-[620px] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400"><x-heroicon-o-sparkles class="h-5 w-5"/></span>
                    <div>
                        <p class="text-sm font-bold">Finance Copilot <span class="ml-1 text-xs font-normal text-slate-400">Gemini</span></p>
                        <p class="text-xs text-slate-500">Read-only · ringkasan agregat · tanpa data rekening</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-500">
                    Periode
                    <input type="month" wire:model="month" class="rounded-lg border-slate-200 bg-white text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white"/>
                </label>
            </div>

            <div class="flex-1 space-y-5 px-5 py-7 sm:px-7">
                @if(count($messages) === 0)
                    <div class="mx-auto flex max-w-md flex-col items-center py-14 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950"><x-heroicon-o-sparkles class="h-8 w-8"/></div>
                        <h2 class="mt-5 text-xl font-bold">Ada yang mau dicek hari ini?</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">Tanya soal beban operasional, omzet, atau status rekonsiliasi bank. Angka diambil dari database dashboard yang tersedia.</p>
                    </div>
                @endif

                @foreach($messages as $i => $message)
                    <div wire:key="finance-ai-message-{{ $i }}" class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[88%] rounded-2xl px-4 py-3 text-sm leading-relaxed sm:max-w-[78%] {{ $message['role'] === 'user' ? 'bg-emerald-600 text-white' : 'border border-slate-200 bg-slate-50 text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100' }}">
                            <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide opacity-70">{{ $message['role'] === 'user' ? 'Kamu' : 'Finance AI' }} · {{ $message['month'] }}</p>
                            @if($message['role'] === 'assistant')
                                <div class="finance-ai-markdown break-words">{!! \Illuminate\Support\Str::markdown($message['text'], ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
                            @else
                                <div class="whitespace-pre-wrap break-words">{{ $message['text'] }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-slate-100 p-4 sm:p-5 dark:border-slate-800">
                @error('question')<p class="mb-3 rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $message }}</p>@enderror
                @error('month')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror
                <form wire:submit="ask" class="flex items-end gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-2 dark:border-slate-700 dark:bg-slate-800">
                    <textarea wire:model="question" rows="2" maxlength="500" placeholder="Contoh: Kenapa pengeluaran Oktober naik dibanding September?" class="min-h-[58px] flex-1 resize-none border-0 bg-transparent text-sm text-slate-800 focus:ring-0 dark:text-slate-100" required></textarea>
                    <button type="submit" wire:loading.attr="disabled" wire:target="ask" class="mb-1 rounded-xl bg-emerald-600 p-3 text-white hover:bg-emerald-700 disabled:opacity-50" aria-label="Kirim pertanyaan">
                        <x-heroicon-o-paper-airplane class="h-5 w-5"/>
                    </button>
                </form>
                <div class="mt-2 flex justify-between gap-2 text-xs text-slate-400">
                    <span>AI dapat keliru. Verifikasi angka sebelum dipakai untuk laporan.</span>
                    <span wire:loading wire:target="ask">Sedang menganalisis...</span>
                </div>
            </div>
        </section>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
                <h2 class="font-bold">Pertanyaan cepat</h2>
                <p class="mt-1 text-xs text-slate-500">Klik untuk mengisi kolom chat.</p>
                <div class="mt-4 space-y-2">
                    @foreach(['expense'=>'Beban bulan ini vs sebelumnya','accounts'=>'Akun beban terbesar','reconciliation'=>'Status rekonsiliasi bank','revenue'=>'Total omzet periode ini','why'=>'Apa penyebab perubahan biaya?'] as $type => $label)
                        <button type="button" wire:click="quickQuestion('{{ $type }}')" class="w-full rounded-xl border border-slate-200 px-3 py-3 text-left text-sm transition hover:border-emerald-400 hover:bg-emerald-50 dark:border-slate-700 dark:hover:bg-slate-800">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
                <h2 class="font-semibold">Akun beban terbesar</h2>
                <p class="mt-1 text-xs text-slate-500">Klik untuk lihat transaksi.</p>
                @forelse($dashboard['accounts'] as $row)
                    <button type="button" wire:click="showAccount(@js($row['code']))" class="mt-2 block w-full rounded-lg p-2 text-left hover:bg-emerald-50 dark:hover:bg-slate-800">
                        <span class="block truncate text-xs font-semibold">{{ $row['name'] }}</span>
                        <span class="text-xs text-slate-500">Rp {{ number_format($row['amount'],0,',','.') }} · {{ $row['count'] }} transaksi</span>
                    </button>
                @empty
                    <p class="mt-3 text-xs text-slate-500">Belum ada akun beban.</p>
                @endforelse
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-950 dark:border-emerald-900 dark:bg-emerald-950/30 dark:text-emerald-100">
                <div class="flex items-center gap-2 font-bold"><x-heroicon-o-shield-check class="h-5 w-5"/> Privasi & keamanan</div>
                <p class="mt-2 leading-relaxed">Hanya data agregat keuangan dan ringkasan status rekonsiliasi yang dikirim ke Gemini. Nomor rekening, referensi transfer, dan rincian transaksi tidak dikirim.</p>
                <p class="mt-3 text-xs opacity-80">Maksimal 15 pertanyaan per jam per akun. Riwayat hanya ada selama sesi halaman, tidak disimpan dalam database khusus.</p>
            </div>
        </aside>
    </div>
    @if($accountTransactions)
        <section class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
            <div class="mb-4 flex items-center justify-between gap-2">
                <div>
                    <h2 class="font-semibold">Rincian: {{ $accountName }}</h2>
                    <p class="text-xs text-slate-500">Periode {{ $month }} · Hanya ditampilkan di dashboard, tidak dikirim ke Gemini.</p>
                </div>
                <button type="button" wire:click="closeAccount" class="rounded-lg border border-slate-200 px-3 py-2 text-xs dark:border-slate-700">Tutup</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800"><tr><th class="p-3">Tanggal</th><th class="p-3">Keterangan</th><th class="p-3 text-right">Nominal</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($accountTransactions as $transaction)
                            <tr wire:key="finance-drill-{{ $transaction->id }}">
                                <td class="whitespace-nowrap p-3">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td class="p-3">{{ $transaction->description ?: '—' }}</td>
                                <td class="whitespace-nowrap p-3 text-right">Rp {{ number_format((float)$transaction->debit,0,',','.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex items-center justify-between gap-3">
                <button type="button" wire:click="previousAccountPage" @disabled($accountPage <= 1) class="rounded-lg border border-slate-200 px-3 py-2 text-xs disabled:opacity-40 dark:border-slate-700">Sebelumnya</button>
                <span class="text-xs text-slate-500">Halaman {{ $accountPage }} dari {{ max(1,$accountTransactions->lastPage()) }} · {{ $accountTransactions->total() }} transaksi</span>
                <button type="button" wire:click="nextAccountPage" @disabled(!$accountTransactions->hasMorePages()) class="rounded-lg border border-slate-200 px-3 py-2 text-xs disabled:opacity-40 dark:border-slate-700">Selanjutnya</button>
            </div>
        </section>
    @endif
    <style>
        .finance-ai-markdown p {margin:.5rem 0}
        .finance-ai-markdown strong {font-weight:700}
        .finance-ai-markdown ul {list-style:disc;padding-left:1.3rem;margin:.5rem 0}
        .finance-ai-markdown ol {list-style:decimal;padding-left:1.3rem;margin:.5rem 0}
        .finance-ai-markdown li {margin:.25rem 0}
        .finance-ai-markdown table {display:block;max-width:100%;overflow-x:auto;border-collapse:collapse;margin:.6rem 0}
        .finance-ai-markdown th,.finance-ai-markdown td {border:1px solid #94a3b855;padding:.4rem .55rem;text-align:left}
        .finance-ai-markdown th {font-weight:700}
        .finance-ai-markdown code {white-space:break-spaces;background:#94a3b822;border-radius:4px;padding:1px 3px}
        .finance-ai-markdown a {color:#059669;text-decoration:underline}
    </style>
</div>
