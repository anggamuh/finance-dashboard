<div class="mx-auto max-w-6xl space-y-6 text-slate-800 dark:text-slate-100">
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
                            <div class="whitespace-pre-wrap break-words">{{ $message['text'] }}</div>
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
                    @foreach(['expense'=>'Beban bulan ini vs sebelumnya','accounts'=>'Akun beban terbesar','reconciliation'=>'Status rekonsiliasi bank','revenue'=>'Total omzet periode ini'] as $type => $label)
                        <button type="button" wire:click="quickQuestion('{{ $type }}')" class="w-full rounded-xl border border-slate-200 px-3 py-3 text-left text-sm transition hover:border-emerald-400 hover:bg-emerald-50 dark:border-slate-700 dark:hover:bg-slate-800">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-950 dark:border-emerald-900 dark:bg-emerald-950/30 dark:text-emerald-100">
                <div class="flex items-center gap-2 font-bold"><x-heroicon-o-shield-check class="h-5 w-5"/> Privasi & keamanan</div>
                <p class="mt-2 leading-relaxed">Hanya data agregat keuangan dan ringkasan status rekonsiliasi yang dikirim ke Gemini. Nomor rekening, referensi transfer, dan rincian transaksi tidak dikirim.</p>
                <p class="mt-3 text-xs opacity-80">Maksimal 15 pertanyaan per jam per akun. Riwayat hanya ada selama sesi halaman, tidak disimpan dalam database khusus.</p>
            </div>
        </aside>
    </div>
</div>
