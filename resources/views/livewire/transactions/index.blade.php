<div class="space-y-6">

    {{-- ============================================================
        HEADER
        ============================================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-tight flex items-center gap-2">
                Transaksi
            </h1>

            <p class="text-gray-400 mt-1">
                Kelola transaksi manual
            </p>
        </div>


        <div class="flex flex-wrap items-center gap-2">

            {{-- BULK ACTION --}}
            @if(count($selected))

                @if(!$showTrash)

                    <button
                        wire:click="deleteSelected"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 hover:bg-red-700 text-white px-4 py-3">

                        <x-heroicon-o-trash class="w-5 h-5"/>

                        Hapus ({{ count($selected) }})

                    </button>

                @else

                    <button
                        wire:click="restoreSelected"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3">

                        <x-heroicon-o-arrow-path class="w-5 h-5"/>

                        Pulihkan ({{ count($selected) }})

                    </button>

                @endif

            @endif


            {{-- TRASH --}}
            <button
                wire:click="$toggle('showTrash')"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-700 hover:bg-slate-800 text-white px-4 py-3">

                <x-heroicon-o-archive-box class="w-5 h-5"/>

                {{ $showTrash ? 'Kembali' : 'Sampah' }}

            </button>


            {{-- ====================================================
                PASTE DATA
                ==================================================== --}}
            @if(!$showTrash)

                <button
                    type="button"
                    wire:click="openPasteModal"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 font-medium transition">

                    <x-heroicon-o-clipboard-document class="w-5 h-5"/>

                    Tempel Data

                </button>

            @endif


            {{-- TAMBAH TRANSAKSI --}}
            <a
                href="{{ route('transactions.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 font-medium transition">

                <x-heroicon-o-plus class="w-5 h-5" />

                Tambah Transaksi

            </a>

        </div>

    </div>


    {{-- ============================================================
        SUCCESS
        ============================================================ --}}
    @if (session('success'))

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                if (typeof Swal !== 'undefined') {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: @json(session('success')),
                        confirmButtonColor: '#10B981'
                    });

                } else {

                    alert(@json(session('success')));

                }

            });
        </script>

    @endif


    {{-- ============================================================
        FILTER CARD
        ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">

        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                <input
                    wire:model.live="search"
                    placeholder="Cari transaksi..."
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">


                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">


                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">


                <select
                    wire:model.live="perPage"
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">

                    <option value="10">
                        10 Data
                    </option>

                    <option value="25">
                        25 Data
                    </option>

                    <option value="50">
                        50 Data
                    </option>

                    <option value="100">
                        100 Data
                    </option>

                    <option value="150">
                        150 Data
                    </option>

                </select>

            </div>

        </div>


        {{-- ============================================================
            TABLE
            ============================================================ --}}
        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">

                    <tr>

                        <th class="w-12 text-center">

                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="rounded border-gray-300">

                        </th>

                        <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300">
                            Tanggal
                        </th>

                        <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300 hidden sm:table-cell">
                            Keterangan
                        </th>

                        <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300">
                            Akun
                        </th>

                        <th class="text-left p-4 font-semibold text-gray-700 dark:text-gray-300 hidden md:table-cell">
                            Metode
                        </th>

                        <th class="text-right p-4 font-semibold text-gray-700 dark:text-gray-300">
                            Nominal
                        </th>

                        <th class="text-center p-4 font-semibold text-gray-700 dark:text-gray-300 hidden lg:table-cell">
                            Bukti
                        </th>

                        <th class="text-center p-4 font-semibold text-gray-700 dark:text-gray-300">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    @forelse($transactions as $trx)

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">

                            {{-- CHECKBOX --}}
                            <td class="text-center">

                                <input
                                    type="checkbox"
                                    value="{{ $trx->id }}"
                                    wire:model.live="selected"
                                    class="rounded border-gray-300">

                            </td>


                            {{-- TANGGAL --}}
                            <td class="p-4 text-gray-900 dark:text-gray-200 text-sm">

                                {{ $trx->transaction_date->format('d/m/Y') }}

                            </td>


                            {{-- KETERANGAN --}}
                            <td class="p-4 text-gray-900 dark:text-gray-200 text-sm hidden sm:table-cell">

                                {{ $trx->description ?: '-' }}

                            </td>


                            {{-- AKUN --}}
                            <td class="p-4">

                                <div class="font-semibold text-gray-900 dark:text-white text-sm">

                                    {{ $trx->account_code }}

                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">

                                    {{ $trx->account_name }}

                                </div>

                            </td>


                            {{-- METODE --}}
                            <td class="p-4 hidden md:table-cell">

                                @if ($trx->payment_method == 'Bank BCA')

                                    <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 text-xs font-medium gap-1">

                                        <x-heroicon-o-building-library class="w-3 h-3" />

                                        BCA

                                    </span>

                                @elseif ($trx->payment_method == 'Petty Cash')

                                    <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-2 py-1 text-xs font-medium gap-1">

                                        <x-heroicon-o-banknotes class="w-3 h-3" />

                                        Petty Cash

                                    </span>

                                @else

                                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 text-xs font-medium">

                                        {{ $trx->payment_method ?: '-' }}

                                    </span>

                                @endif

                            </td>


                            {{-- NOMINAL --}}
                            <td class="p-4 text-right font-semibold text-gray-900 dark:text-white text-sm">

                                Rp{{ number_format(
                                    $trx->debit > 0
                                        ? $trx->debit
                                        : $trx->credit,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            {{-- BUKTI --}}
                            <td class="p-4 text-center hidden lg:table-cell">

                                @php
                                    $attachment = $trx->attachments->first();
                                @endphp


                                @if($attachment)

                                    <a
                                        href="{{ route('proofs.show', $attachment->file_path) }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="text-emerald-600 dark:text-emerald-400 hover:underline text-sm">

                                        Lihat

                                    </a>

                                @else

                                    <span class="text-gray-400 text-sm">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td class="p-4">

                                <div class="flex justify-center gap-2">

                                    @if(!$showTrash)

                                        {{-- EDIT --}}
                                        <a
                                            href="{{ route('transactions.edit', $trx->id) }}"
                                            aria-label="Edit transaksi"
                                            class="inline-flex items-center justify-center p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition">

                                            <x-heroicon-o-pencil-square class="w-5 h-5"/>

                                        </a>


                                        {{-- DELETE --}}
                                        <button
                                            type="button"
                                            onclick="confirmDelete({{ $trx->id }})"
                                            aria-label="Hapus transaksi"
                                            class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition">

                                            <x-heroicon-o-trash class="w-5 h-5"/>

                                        </button>

                                    @else

                                        {{-- RESTORE --}}
                                        <button
                                            type="button"
                                            wire:click="restore({{ $trx->id }})"
                                            aria-label="Pulihkan transaksi"
                                            class="inline-flex items-center justify-center p-2 text-emerald-600 hover:bg-emerald-50 rounded">

                                            <x-heroicon-o-arrow-path class="w-5 h-5"/>

                                        </button>


                                        {{-- FORCE DELETE --}}
                                        <button
                                            type="button"
                                            wire:click="forceDelete({{ $trx->id }})"
                                            onclick="return confirm('Hapus permanen?')"
                                            aria-label="Hapus transaksi secara permanen"
                                            class="inline-flex items-center justify-center p-2 text-red-700 hover:bg-red-50 rounded">

                                            <x-heroicon-o-x-circle class="w-5 h-5"/>

                                        </button>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="py-16 text-center text-gray-500 dark:text-gray-400 text-sm">

                                Belum ada transaksi.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        <div class="p-4 sm:p-5 border-t border-gray-200 dark:border-gray-700">

            {{ $transactions->links() }}

        </div>

    </div>


    {{-- ================================================================
        PASTE DATA MODAL
        ================================================================ --}}
    @if($showPasteModal)

        <div
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            wire:key="paste-data-modal">

            {{-- BACKDROP --}}
            <div
                class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                wire:click="closePasteModal">
            </div>


            {{-- MODAL --}}
            <div
                class="relative w-full max-w-6xl overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-2xl"
                style="max-height: 92vh;">


                {{-- ====================================================
                    MODAL HEADER
                    ==================================================== --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">

                    <div>

                        <div class="flex items-center gap-3">

                            <div class="p-2.5 rounded-xl bg-indigo-100 dark:bg-indigo-900/30">

                                <x-heroicon-o-clipboard-document
                                    class="w-6 h-6 text-indigo-600 dark:text-indigo-400"/>

                            </div>

                            <div>

                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">

                                    Paste Data Transaksi

                                </h2>

                                <p class="text-sm text-gray-500 dark:text-gray-400">

                                    Paste langsung dari Excel.

                                </p>

                            </div>

                        </div>

                    </div>


                    <button
                        type="button"
                        wire:click="closePasteModal"
                        class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white">

                        <x-heroicon-o-x-mark class="w-6 h-6"/>

                    </button>

                </div>


                {{-- ====================================================
                    MODAL BODY
                    ==================================================== --}}
                <div
                    class="overflow-y-auto"
                    style="max-height: calc(92vh - 75px);">

                    <div class="p-6 space-y-5">


                        {{-- INFO --}}
                        <div class="rounded-xl border border-indigo-200 bg-indigo-50 dark:border-indigo-800 dark:bg-indigo-900/20 p-4">

                            <div class="flex gap-3">

                                <x-heroicon-o-information-circle
                                    class="w-5 h-5 mt-0.5 shrink-0 text-indigo-600 dark:text-indigo-400"/>

                                <div class="text-sm text-indigo-800 dark:text-indigo-200">

                                    <div class="font-semibold">
                                        Sistem akan membaca periode otomatis.
                                    </div>

                                    <div class="mt-1">

                                        Contoh:

                                        <strong>
                                            Periode Agustus 24 - 30 Agustus 2026
                                        </strong>

                                    </div>

                                    <ul class="mt-2 list-disc list-inside space-y-1 text-xs">

                                        <li>
                                            Transaksi sebelum tanggal 24 otomatis menjadi tanggal 24.
                                        </li>

                                        <li>
                                            Transaksi tanggal 24 sampai 30 tetap menggunakan tanggal aslinya.
                                        </li>
<li>
                                            Data tanpa tanggal menggunakan tanggal awal periode.
                                        </li>

                                        <li>
                                            Baris TOTAL tidak akan diimport.
                                        </li>

                                    </ul>

                                </div>

                            </div>

                        </div>


                        {{-- ====================================================
                            TEXTAREA
                            ==================================================== --}}
                        <div>

                            <div class="flex items-center justify-between mb-2">

                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">

                                    Paste Data Excel

                                </label>

                                @if($pasteData)

                                    <span class="text-xs text-gray-400">

                                        {{ number_format(strlen($pasteData)) }}
                                        karakter

                                    </span>

                                @endif

                            </div>


                            <textarea
                                wire:model="pasteData"
                                rows="14"
                                spellcheck="false"
                                placeholder="Paste seluruh data Excel di sini..."
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white font-mono text-xs leading-relaxed focus:border-indigo-500 focus:ring-indigo-500"></textarea>


                            @error('pasteData')

                                <div class="mt-2 flex items-start gap-2 text-sm text-red-500">

                                    <x-heroicon-o-exclamation-circle class="w-4 h-4 mt-0.5 shrink-0"/>

                                    {{ $message }}

                                </div>

                            @enderror

                        </div>


                        {{-- PROCESS --}}
                        <div class="flex justify-end">

                            <button
                                type="button"
                                wire:click="processPaste"
                                wire:loading.attr="disabled"
                                wire:target="processPaste"
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white px-5 py-2.5 font-medium transition">

                                <span
                                    wire:loading.remove
                                    wire:target="processPaste"
                                    class="inline-flex items-center gap-2">

                                    <x-heroicon-o-sparkles class="w-5 h-5"/>

                                    Proses & Preview

                                </span>


                                <span
                                    wire:loading
                                    wire:target="processPaste"
                                    class="inline-flex items-center gap-2">

                                    <svg
                                        class="w-5 h-5 animate-spin"
                                        viewBox="0 0 24 24"
                                        fill="none">

                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4">
                                        </circle>

                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                        </path>

                                    </svg>

                                    Membaca...

                                </span>

                            </button>

                        </div>


                        {{-- ====================================================
                            PREVIEW
                            ==================================================== --}}
                        @if(count($pastePreview) > 0)

                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">


                                {{-- PREVIEW SUMMARY --}}
                                <div class="p-5 bg-gray-50 dark:bg-gray-700/40 border-b border-gray-200 dark:border-gray-700">

                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                                        <div>

                                            <h3 class="font-bold text-gray-900 dark:text-white">
                                                Preview Import
                                            </h3>

                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">

                                                Periode:

                                                <strong class="text-gray-800 dark:text-gray-200">

                                                    {{ \Carbon\Carbon::parse($pastePeriodStart)->format('d/m/Y') }}

                                                    -

                                                    {{ \Carbon\Carbon::parse($pastePeriodEnd)->format('d/m/Y') }}

                                                </strong>

                                            </p>

                                        </div>


                                        {{-- SUMMARY BADGES --}}
                                        <div class="flex flex-wrap gap-2">

                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 px-3 py-1.5 text-xs font-semibold">

                                                <x-heroicon-o-check-circle class="w-4 h-4"/>

                                                {{ count($pastePreview) }}
                                                transaksi

                                            </span>


                                            @if($pasteAdjusted > 0)

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-3 py-1.5 text-xs font-semibold">

                                                    <x-heroicon-o-arrow-path class="w-4 h-4"/>

                                                    {{ $pasteAdjusted }}
                                                    disesuaikan

                                                </span>

                                            @endif
</div>

                                    </div>

                                </div>


                                {{-- ====================================================
                                    PREVIEW TABLE
                                    ==================================================== --}}
                                <div class="overflow-auto max-h-[430px]">

                                    <table class="w-full min-w-[1300px] text-sm">

                                        <thead class="sticky top-0 z-10 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

                                            <tr>

                                                <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">
                                                    Tanggal
                                                </th>

                                                <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">
                                                    Perusahaan
                                                </th>

                                                <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">
                                                    Cabang
                                                </th>

                                                <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">
                                                    Kategori
                                                </th>

                                                <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">
                                                    Keterangan
                                                </th>

                                                <th class="text-left px-4 py-3 font-semibold text-gray-700 dark:text-gray-300 min-w-[300px]">
                                                    Akun
                                                </th>

                                                <th class="text-right px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">
                                                    Nominal
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                                            @foreach($pastePreview as $index => $row)

                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">


                                                    {{-- TANGGAL --}}
                                                    <td class="px-4 py-3 whitespace-nowrap">

                                                        @if($row['adjusted'])

                                                            <div class="flex flex-col gap-1">

                                                                <span class="font-semibold text-amber-600 dark:text-amber-400">

                                                                    {{ $row['date_display'] }}

                                                                </span>

                                                                <span class="inline-flex w-fit rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-2 py-0.5 text-[10px] font-semibold">

                                                                    Disesuaikan

                                                                </span>

                                                            </div>

                                                        @else

                                                            <span class="text-gray-700 dark:text-gray-200">

                                                                {{ $row['date_display'] }}

                                                            </span>

                                                        @endif

                                                    </td>


                                                    {{-- PERUSAHAAN --}}
                                                    <td class="px-4 py-3 whitespace-nowrap">

                                                        {{ $row['company'] ?: '-' }}

                                                    </td>


                                                    {{-- CABANG --}}
                                                    <td class="px-4 py-3 whitespace-nowrap">

                                                        {{ $row['branch'] ?: '-' }}

                                                    </td>


                                                    {{-- KATEGORI --}}
                                                    <td class="px-4 py-3">

                                                        @if($row['category'])

                                                            <span class="inline-flex rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2.5 py-1 text-xs font-medium">

                                                                {{ $row['category'] }}

                                                            </span>

                                                        @else

                                                            <span class="text-gray-400">
                                                                -
                                                            </span>

                                                        @endif

                                                    </td>


                                                    {{-- KETERANGAN --}}
                                                    <td class="px-4 py-3 max-w-[450px]">

                                                        <div class="break-words text-gray-700 dark:text-gray-200">

                                                            {{ $row['description'] }}

                                                        </div>

                                                    </td>


                                                    {{-- AKUN PER TRANSAKSI --}}
                                                    <td class="px-4 py-3 min-w-[300px]">

                                                        <select
                                                            wire:model.live="pastePreview.{{ $index }}.account_code"
                                                            wire:key="paste-account-{{ $index }}"
                                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs focus:border-indigo-500 focus:ring-indigo-500">

                                                            <option value="">
                                                                -- Pilih Akun --
                                                            </option>

                                                            @foreach($accounts as $account)
                                                                <option value="{{ $account->account_code }}">
                                                                    {{ $account->account_code }} - {{ $account->account_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        @if(!empty($row['account_code']))
                                                            @php
                                                                $selectedAccount = $accounts->firstWhere(
                                                                    'account_code',
                                                                    $row['account_code']
                                                                );
                                                            @endphp

                                                            @if($selectedAccount)
                                                                <div class="mt-1 text-[10px] text-emerald-600 dark:text-emerald-400">
                                                                    ✓ {{ $selectedAccount->account_name }}
                                                                </div>
                                                            @endif
                                                        @endif

                                                    </td>

                                                    {{-- NOMINAL --}}
                                                    <td class="px-4 py-3 text-right whitespace-nowrap font-semibold text-gray-900 dark:text-white">

                                                        Rp{{ number_format(
                                                            $row['amount'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        ) }}

                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>


                            {{-- ====================================================
                                MODAL FOOTER
                                ==================================================== --}}
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                                <div class="text-xs text-gray-500 dark:text-gray-400">

                                    <span class="font-medium text-gray-700 dark:text-gray-300">

                                        {{ count($pastePreview) }}

                                    </span>

                                    transaksi siap diimport.

                                    @if($pasteAdjusted > 0)

                                        <span class="text-amber-600 dark:text-amber-400">

                                            {{ $pasteAdjusted }}
                                            tanggal otomatis dipindahkan ke awal periode.

                                        </span>

                                    @endif

                                </div>


                                <div class="flex justify-end gap-2">

                                    <button
                                        type="button"
                                        wire:click="closePasteModal"
                                        class="rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-5 py-2.5 font-medium">

                                        Batal

                                    </button>


                                    <button
                                        type="button"
                                        wire:click="importPastedTransactions"
                                        wire:loading.attr="disabled"
                                        wire:target="importPastedTransactions"
                                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white px-5 py-2.5 font-semibold">

                                        <span
                                            wire:loading.remove
                                            wire:target="importPastedTransactions"
                                            class="inline-flex items-center gap-2">

                                            <x-heroicon-o-arrow-down-tray class="w-5 h-5"/>

                                            Import
                                            {{ count($pastePreview) }}
                                            Transaksi

                                        </span>


                                        <span
                                            wire:loading
                                            wire:target="importPastedTransactions"
                                            class="inline-flex items-center gap-2">

                                            <svg
                                                class="w-5 h-5 animate-spin"
                                                viewBox="0 0 24 24"
                                                fill="none">

                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4">
                                                </circle>

                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                                </path>

                                            </svg>

                                            Menyimpan...

                                        </span>

                                    </button>

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- ============================================================
        SCRIPTS
        ============================================================ --}}
    @push('scripts')

        <script>

            function confirmDelete(id) {

                if (typeof Swal === 'undefined') {

                    if (confirm('Yakin ingin menghapus transaksi ini?')) {

                        Livewire.dispatch('delete', {
                            id: id
                        });

                    }

                    return;
                }


                Swal.fire({

                    title: 'Yakin?',

                    text: 'Transaksi akan dihapus dan tidak dapat dikembalikan.',

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#e11d48',

                    cancelButtonColor: '#6b7280',

                    confirmButtonText: 'Ya, hapus'

                }).then((result) => {

                    if (result.isConfirmed) {

                        Livewire.dispatch('delete', {
                            id: id
                        });

                    }

                });

            }

        </script>

    @endpush

</div>
