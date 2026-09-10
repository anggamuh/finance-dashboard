<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h1 class="text-3xl font-bold text-white">
                Edit Transaksi
            </h1>
            <p class="text-gray-400 mt-1">
                Input transaksi pengeluaran maupun pemasukan.
            </p>
        </div>

        <a href="{{ route('transactions') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3 font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
            Kembali
        </a>

    </div>

    <form wire:submit="save">

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

            {{-- LEFT --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- INFORMASI TRANSAKSI --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">

                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h2 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-document-text class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            Informasi Transaksi
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">

                        <div class="grid md:grid-cols-2 gap-5">

                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tanggal
                                </label>
                                <input type="date" wire:model="transaction_date"
                                    class="mt-1.5 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @error('transaction_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nomor Transaksi
                                </label>
                                <input type="text" wire:model="transaction_number" readonly
                                    class="mt-1.5 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-sm cursor-not-allowed">
                            </div>

                        </div>

                        <div class="grid md:grid-cols-2 gap-5">

                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Jenis
                                </label>
                                <select wire:model="type"
                                    class="mt-1.5 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="expense">Pengeluaran</option>
                                    <option value="income">Pemasukan</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Metode Pembayaran
                                </label>
                                <select wire:model="payment_method"
                                    class="mt-1.5 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option>Bank BCA</option>
                                    <option>Petty Cash</option>
                                    <option>Transfer</option>
                                    <option>QRIS</option>
                                    <option>Tunai</option>
                                </select>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- DETAIL TRANSAKSI --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">

                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h2 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-wallet class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            Detail Transaksi
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">

                        {{-- AKUN BEBAN: searchable combobox, bukan native select --}}
                        <div
                            x-data="{
                                open: false,
                                search: '',
                                accounts: @js($accounts->map(fn ($a) => ['code' => $a->code, 'name' => $a->name])),
                                selected: $wire.entangle('account_code'),
                                get filtered() {
                                    if (!this.search) return this.accounts;
                                    const q = this.search.toLowerCase();
                                    return this.accounts.filter(a =>
                                        a.code.toLowerCase().includes(q) || a.name.toLowerCase().includes(q)
                                    );
                                },
                                get selectedLabel() {
                                    const acc = this.accounts.find(a => a.code === this.selected);
                                    return acc ? acc.code + ' - ' + acc.name : '';
                                },
                                choose(code) {
                                    this.selected = code;
                                    this.open = false;
                                    this.search = '';
                                }
                            }"
                            class="relative"
                        >
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Akun Beban
                            </label>

                            <button type="button" @click="open = !open"
                                class="mt-1.5 w-full flex items-center justify-between rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm text-left focus:border-emerald-500 focus:ring-emerald-500">
                                <span :class="selected ? 'text-gray-900 dark:text-white' : 'text-gray-400'"
                                    x-text="selectedLabel || 'Pilih Akun...'"></span>
                                <x-heroicon-o-chevron-up-down class="w-4 h-4 text-gray-400 shrink-0" />
                            </button>

                            <div x-show="open" @click.outside="open = false" x-cloak
                                class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg overflow-hidden">

                                <div class="p-2 border-b border-gray-100 dark:border-gray-600">
                                    <input type="text" x-model="search" placeholder="Cari kode atau nama akun..."
                                        class="w-full rounded-md border-gray-300 dark:border-gray-500 dark:bg-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                </div>

                                <div class="max-h-56 overflow-y-auto">
                                    <template x-for="acc in filtered" :key="acc.code">
                                        <div @click="choose(acc.code)"
                                            class="px-4 py-2 text-sm cursor-pointer hover:bg-emerald-50 dark:hover:bg-gray-600"
                                            :class="selected === acc.code ? 'bg-emerald-50 dark:bg-gray-600 font-medium text-emerald-700 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-200'">
                                            <span x-text="acc.code"></span> - <span x-text="acc.name"></span>
                                        </div>
                                    </template>
                                    <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-400">
                                        Akun tidak ditemukan.
                                    </div>
                                </div>

                            </div>

                            @error('account_code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nominal
                            </label>
                            <div class="relative mt-1.5">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 dark:text-gray-400">Rp</span>
                                <input type="number" wire:model="amount"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm pl-9 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Deskripsi
                            </label>
                            <textarea rows="4" wire:model="description"
                                class="mt-1.5 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>

                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="space-y-6">

                {{-- BUKTI PEMBAYARAN --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">

                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h2 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-paper-clip class="w-5 h-5 text-orange-500" />
                            Bukti Pembayaran
                        </h2>
                    </div>

                    <div class="p-6">

                        {{-- Bukti yang sudah tersimpan --}}
                        @if ($transaction->attachments->count())
                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Sudah tersimpan</p>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach ($transaction->attachments as $attachment)
                                        <div class="relative group" wire:key="attachment-{{ $attachment->id }}">
                                            <a href="{{ route('proofs.show', $attachment->file_path) }}" target="_blank">
                                                <img src="{{ route('proofs.show', $attachment->file_path) }}"
                                                    class="rounded-lg border border-gray-200 dark:border-gray-700 h-28 w-full object-cover">
                                            </a>
                                            <button type="button" wire:click="deleteAttachment({{ $attachment->id }})"
                                                wire:confirm="Hapus bukti ini?"
                                                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full p-1 shadow">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <label
                            class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg py-8 cursor-pointer hover:border-emerald-500 dark:hover:border-emerald-500 transition">
                            <x-heroicon-o-cloud-arrow-up class="w-8 h-8 text-gray-400" />
                            <span class="text-sm text-gray-500 dark:text-gray-400">Klik untuk tambah bukti (bisa lebih dari 1)</span>
                            <input type="file" wire:model="proofs" multiple accept="image/*" class="hidden">
                        </label>

                        <div wire:loading wire:target="proofs" class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                            Mengunggah...
                        </div>

                        @error('proofs.*')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror

                        @if (count($proofs))
                            <div class="mt-4">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Baru (belum disimpan)</p>
                                <div class="space-y-2">
                                    @foreach ($proofs as $index => $file)
                                        <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-2">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <x-heroicon-o-photo class="w-5 h-5 text-emerald-500 shrink-0" />
                                                <span class="text-sm text-gray-700 dark:text-gray-200 truncate">{{ $file->getClientOriginalName() }}</span>
                                            </div>
                                            <button type="button" wire:click="removeProof({{ $index }})"
                                                class="shrink-0 inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 px-2 py-1 rounded transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>

                </div>

                {{-- SUBMIT --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed text-white py-3 rounded-lg font-semibold flex items-center justify-center gap-2 transition">
                        <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                            Simpan Transaksi
                        </span>
                        <span wire:loading wire:target="save">
                            Menyimpan...
                        </span>
                    </button>
                </div>

            </div>

        </div>

    </form>

</div>