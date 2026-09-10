<div class="space-y-6">

    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-800 dark:text-white">
                Buku Kas
            </h1>

            <p class="mt-1 text-gray-400">
                Ringkasan kas masuk, kas keluar, dan saldo berjalan
            </p>
        </div>

        <button
            type="button"
            wire:click="openOpeningBalanceModal"
            class="inline-flex items-center justify-center gap-2
                   rounded-lg bg-emerald-600 px-5 py-3
                   text-sm font-semibold text-white
                   transition hover:bg-emerald-700">

            <x-heroicon-o-pencil-square class="h-5 w-5" />

            <span>Atur Saldo Awal</span>

        </button>

    </div>


    {{-- ============================================================
        SUMMARY CARDS
    ============================================================ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- SALDO AWAL --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Saldo Awal
                    </p>

                    <p class="mt-2 truncate text-xl font-bold tracking-tight
                              text-gray-900 dark:text-white">

                        Rp {{ number_format((float) $openingBalance, 0, ',', '.') }}

                    </p>

                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center
                            rounded-lg bg-gray-100 text-gray-500
                            dark:bg-gray-700 dark:text-gray-300">

                    <x-heroicon-o-banknotes class="h-5 w-5" />

                </div>

            </div>

            <p class="mt-4 text-xs text-gray-400">
                Saldo awal periode terpilih
            </p>

        </div>


        {{-- KAS MASUK --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Kas Masuk
                    </p>

                    <p class="mt-2 truncate text-xl font-bold tracking-tight
                              text-emerald-600 dark:text-emerald-400">

                        Rp {{ number_format($totalCashIn, 0, ',', '.') }}

                    </p>

                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center
                            rounded-lg bg-emerald-50 text-emerald-600
                            dark:bg-emerald-500/10 dark:text-emerald-400">

                    <x-heroicon-o-arrow-trending-up class="h-5 w-5" />

                </div>

            </div>

            <p class="mt-4 text-xs text-gray-400">
                Otomatis dari Omzet
            </p>

        </div>


        {{-- KAS KELUAR --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Kas Keluar
                    </p>

                    <p class="mt-2 truncate text-xl font-bold tracking-tight
                              text-orange-600 dark:text-orange-400">

                        Rp {{ number_format($totalCashOut, 0, ',', '.') }}

                    </p>

                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center
                            rounded-lg bg-orange-50 text-orange-600
                            dark:bg-orange-500/10 dark:text-orange-400">

                    <x-heroicon-o-arrow-trending-down class="h-5 w-5" />

                </div>

            </div>

            <p class="mt-4 text-xs text-gray-400">
                Expense + Pembelian Barang
            </p>

        </div>


        {{-- SALDO AKHIR --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Saldo Akhir
                    </p>

                    <p class="mt-2 truncate text-xl font-bold tracking-tight
                        {{ $endingBalance < 0
                            ? 'text-red-600 dark:text-red-400'
                            : 'text-gray-900 dark:text-white' }}">

                        Rp {{ number_format($endingBalance, 0, ',', '.') }}

                    </p>

                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center
                            rounded-lg
                    {{ $endingBalance < 0
                        ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400'
                        : 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' }}">

                    <x-heroicon-o-scale class="h-5 w-5" />

                </div>

            </div>

            <p class="mt-4 text-xs text-gray-400">
                Saldo akhir periode terpilih
            </p>

        </div>

    </div>


    {{-- ============================================================
        MAIN TABLE CARD
    ============================================================ --}}
    <div class="overflow-hidden rounded-xl border border-gray-200
                bg-white shadow-sm
                dark:border-gray-700 dark:bg-gray-800">

        {{-- ========================================================
            FILTER
        ======================================================== --}}
        <div class="border-b border-gray-200 p-4 sm:p-6 dark:border-gray-700">

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

                {{-- BULAN --}}
                <div>

                    <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">
                        Bulan
                    </label>

                    <select
                        wire:model.live="month"
                        class="w-full rounded-lg border-gray-300
                               bg-white text-sm text-gray-800
                               focus:border-emerald-500 focus:ring-emerald-500
                               dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                        @foreach ([
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ] as $number => $name)

                            <option value="{{ $number }}">
                                {{ $name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- TAHUN --}}
                <div>

                    <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">
                        Tahun
                    </label>

                    <select
                        wire:model.live="year"
                        class="w-full rounded-lg border-gray-300
                               bg-white text-sm text-gray-800
                               focus:border-emerald-500 focus:ring-emerald-500
                               dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                        @for ($y = now()->year + 1; $y >= now()->year - 10; $y--)

                            <option value="{{ $y }}">
                                {{ $y }}
                            </option>

                        @endfor

                    </select>

                </div>


                {{-- SEARCH --}}
                <div class="sm:col-span-2">

                    <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">
                        Pencarian
                    </label>

                    <div class="relative">

                        <x-heroicon-o-magnifying-glass
                            class="pointer-events-none absolute left-3 top-1/2
                                   h-4 w-4 -translate-y-1/2 text-gray-400"
                        />

                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Cari nomor bukti, keterangan, kode atau nama akun..."
                            class="w-full rounded-lg border-gray-300
                                   bg-white py-2 pl-9 pr-4
                                   text-sm text-gray-800
                                   placeholder:text-gray-400
                                   focus:border-emerald-500 focus:ring-emerald-500
                                   dark:border-gray-600 dark:bg-gray-700 dark:text-white">

                    </div>

                </div>

            </div>


            {{-- ====================================================
                BREAKDOWN
            ==================================================== --}}
            <div class="mt-5 flex flex-col gap-3 border-t border-gray-100 pt-4
                        lg:flex-row lg:items-center lg:justify-between
                        dark:border-gray-700">

                <div class="flex flex-wrap items-center gap-x-6 gap-y-3">

                    {{-- OMZET --}}
                    <div class="flex items-center gap-2">

                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Omzet
                        </span>

                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </span>

                    </div>


                    {{-- EXPENSE --}}
                    <div class="flex items-center gap-2">

                        <span class="h-2 w-2 rounded-full bg-orange-500"></span>

                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Pengeluaran
                        </span>

                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                            Rp {{ number_format($totalExpense, 0, ',', '.') }}
                        </span>

                    </div>


                    {{-- PURCHASE --}}
                    <div class="flex items-center gap-2">

                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>

                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Pembelian Barang
                        </span>

                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                            Rp {{ number_format($totalPurchase, 0, ',', '.') }}
                        </span>

                    </div>

                </div>


                {{-- JUMLAH DATA --}}
                <div class="text-xs text-gray-400">

                    @if (trim($search))

                        Menampilkan
                        <span class="font-semibold text-gray-700 dark:text-gray-200">
                            {{ $displayedTransactions }}
                        </span>
                        dari
                        <span class="font-semibold text-gray-700 dark:text-gray-200">
                            {{ $totalTransactions }}
                        </span>
                        transaksi

                    @else

                        <span class="font-semibold text-gray-700 dark:text-gray-200">
                            {{ $totalTransactions }}
                        </span>
                        transaksi

                    @endif

                </div>

            </div>

        </div>


        {{-- ========================================================
            TABLE
        ======================================================== --}}
        <div class="overflow-x-auto">

            <table class="min-w-[1180px] w-full text-sm">

                <thead class="border-b border-gray-200 bg-gray-50
                              dark:border-gray-700 dark:bg-gray-700/50">

                    <tr>

                        <th class="whitespace-nowrap px-4 py-3 text-left
                                   text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Tanggal
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-left
                                   text-xs font-semibold text-gray-600 dark:text-gray-300">
                            No. Bukti
                        </th>

                        <th class="min-w-[260px] px-4 py-3 text-left
                                   text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Keterangan
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-left
                                   text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Kode
                        </th>

                        <th class="min-w-[180px] px-4 py-3 text-left
                                   text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Nama Akun
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-right
                                   text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Kas Masuk
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-right
                                   text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Kas Keluar
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-right
                                   text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Saldo
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-center
                                   text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Bukti
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                    {{-- ====================================================
                        SALDO AWAL
                    ==================================================== --}}
                    @if (! trim($search))

                        <tr class="bg-emerald-50/40 dark:bg-emerald-500/5">

                            <td class="whitespace-nowrap px-4 py-3
                                       text-gray-700 dark:text-gray-300">

                                01/{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}

                            </td>

                            <td class="whitespace-nowrap px-4 py-3
                                       text-xs font-semibold
                                       text-emerald-700 dark:text-emerald-400">

                                SALDO AWAL

                            </td>

                            <td class="px-4 py-3 font-medium
                                       text-gray-700 dark:text-gray-300">

                                Saldo awal periode

                            </td>

                            <td class="px-4 py-3 text-gray-400">
                                -
                            </td>

                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                Saldo Awal
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right
                                       font-medium text-emerald-600
                                       dark:text-emerald-400">

                                {{ number_format((float) $openingBalance, 0, ',', '.') }}

                            </td>

                            <td class="px-4 py-3 text-right text-gray-400">
                                -
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right
                                       font-semibold text-gray-900 dark:text-white">

                                {{ number_format((float) $openingBalance, 0, ',', '.') }}

                            </td>

                            <td class="px-4 py-3 text-center text-gray-400">
                                -
                            </td>

                        </tr>

                    @endif


                    {{-- ====================================================
                        TRANSAKSI
                    ==================================================== --}}
                    @forelse ($rows as $row)

                        <tr
                            wire:key="{{ $row['unique_key'] }}"
                            class="transition-colors
                                   hover:bg-gray-50
                                   dark:hover:bg-gray-700/30">

                            {{-- TANGGAL --}}
                            <td class="whitespace-nowrap px-4 py-3
                                       text-gray-700 dark:text-gray-300">

                                {{ $row['date']->format('d/m/Y') }}

                            </td>


                            {{-- NO BUKTI --}}
                            <td class="whitespace-nowrap px-4 py-3
                                       font-mono text-xs
                                       text-gray-700 dark:text-gray-300">

                                {{ $row['proof_number'] }}

                            </td>


                            {{-- KETERANGAN --}}
                            <td class="px-4 py-3">

                                <div class="text-sm font-medium
                                            text-gray-800 dark:text-gray-200">

                                    {{ $row['description'] }}

                                </div>

                                <div class="mt-1 text-[11px]">

                                    @if ($row['source_type'] === 'revenue')

                                        <span class="text-emerald-600 dark:text-emerald-400">
                                            Omzet
                                        </span>

                                    @elseif ($row['source_type'] === 'purchase')

                                        <span class="text-blue-600 dark:text-blue-400">
                                            Pembelian Barang
                                        </span>

                                    @else

                                        <span class="text-orange-600 dark:text-orange-400">
                                            Pengeluaran
                                        </span>

                                    @endif

                                </div>

                            </td>


                            {{-- KODE --}}
                            <td class="whitespace-nowrap px-4 py-3
                                       font-mono text-xs
                                       text-gray-600 dark:text-gray-300">

                                {{ $row['account_code'] === '-' ? '-' : $row['account_code'] }}

                            </td>


                            {{-- NAMA AKUN --}}
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">

                                {{ $row['account_name'] }}

                            </td>


                            {{-- KAS MASUK --}}
                            <td class="whitespace-nowrap px-4 py-3 text-right">

                                @if ($row['cash_in'] > 0)

                                    <span class="font-medium text-emerald-600
                                                 dark:text-emerald-400">

                                        {{ number_format($row['cash_in'], 0, ',', '.') }}

                                    </span>

                                @else

                                    <span class="text-gray-300 dark:text-gray-600">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- KAS KELUAR --}}
                            <td class="whitespace-nowrap px-4 py-3 text-right">

                                @if ($row['cash_out'] > 0)

                                    <span class="font-medium text-orange-600
                                                 dark:text-orange-400">

                                        {{ number_format($row['cash_out'], 0, ',', '.') }}

                                    </span>

                                @else

                                    <span class="text-gray-300 dark:text-gray-600">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- SALDO --}}
                            <td class="whitespace-nowrap px-4 py-3 text-right
                                       font-semibold
                                {{ $row['balance'] < 0
                                    ? 'text-red-600 dark:text-red-400'
                                    : 'text-gray-900 dark:text-white' }}">

                                {{ number_format($row['balance'], 0, ',', '.') }}

                            </td>


                            {{-- BUKTI --}}
                            <td class="whitespace-nowrap px-4 py-3 text-center">

                                @if (
                                    $row['source_type'] === 'transaction'
                                    && $row['attachments']->count() > 0
                                )

                                    <div class="inline-flex items-center gap-1.5
                                                text-xs font-medium
                                                text-gray-500 dark:text-gray-300">

                                        <x-heroicon-o-paper-clip class="h-4 w-4" />

                                        <span>
                                            {{ $row['attachments']->count() }}
                                        </span>

                                    </div>

                                @else

                                    <span class="text-gray-300 dark:text-gray-600">
                                        -
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9"
                                class="px-6 py-16 text-center">

                                <div class="mx-auto max-w-md">

                                    <p class="font-medium text-gray-600 dark:text-gray-300">

                                        @if (trim($search))
                                            Data tidak ditemukan
                                        @else
                                            Belum ada transaksi
                                        @endif

                                    </p>

                                    <p class="mt-1 text-sm text-gray-400">

                                        @if (trim($search))

                                            Tidak ada transaksi yang sesuai
                                            dengan pencarian "{{ $search }}".

                                        @else

                                            Belum ada data Omzet, Pengeluaran,
                                            atau Pembelian Barang pada periode ini.

                                        @endif

                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>


                {{-- ====================================================
                    TOTAL
                ==================================================== --}}
                @if ($rows->count() > 0 && ! trim($search))

                    <tfoot class="border-t border-gray-200 bg-gray-50
                                  dark:border-gray-700 dark:bg-gray-700/40">

                        <tr>

                            <td colspan="5"
                                class="px-4 py-4 text-right
                                       text-sm font-semibold
                                       text-gray-700 dark:text-gray-200">

                                Total Periode

                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right
                                       font-bold text-emerald-600
                                       dark:text-emerald-400">

                                {{ number_format($totalCashIn, 0, ',', '.') }}

                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right
                                       font-bold text-orange-600
                                       dark:text-orange-400">

                                {{ number_format($totalCashOut, 0, ',', '.') }}

                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right
                                       font-bold
                                {{ $endingBalance < 0
                                    ? 'text-red-600 dark:text-red-400'
                                    : 'text-gray-900 dark:text-white' }}">

                                {{ number_format($endingBalance, 0, ',', '.') }}

                            </td>

                            <td></td>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>


        {{-- ========================================================
            TABLE FOOTER
        ======================================================== --}}
        <div class="flex flex-col gap-1
                    border-t border-gray-200
                    px-4 py-3
                    sm:flex-row sm:items-center sm:justify-between
                    dark:border-gray-700">

            <p class="text-xs text-gray-400">

                @if (trim($search))

                    Menampilkan {{ $displayedTransactions }}
                    dari {{ $totalTransactions }} transaksi

                @else

                    Menampilkan seluruh {{ $totalTransactions }} transaksi

                @endif

            </p>

            <p class="text-xs text-gray-400">
                Semua data ditampilkan tanpa pagination
            </p>

        </div>

    </div>
{{-- ============================================================
    MODAL SALDO AWAL
============================================================ --}}
@if ($showOpeningBalanceModal)

    <div
        class="fixed inset-0 z-[100] flex items-center justify-center
               bg-black/60 p-4 backdrop-blur-[2px]"
        wire:click.self="closeOpeningBalanceModal">

        <div
            class="overflow-hidden rounded-xl
                   border border-gray-200 bg-white
                   shadow-2xl dark:border-gray-700 dark:bg-gray-800"
            style="width: 100%; max-width: 440px;">

            {{-- HEADER --}}
            <div class="flex items-start justify-between
                        border-b border-gray-200
                        px-5 py-4 dark:border-gray-700">

                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">
                        Atur Saldo Awal
                    </h2>

                    <p class="mt-1 text-xs text-gray-400">
                        Periode {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="closeOpeningBalanceModal"
                    class="flex h-8 w-8 items-center justify-center
                           rounded-lg text-gray-400 transition
                           hover:bg-gray-100 hover:text-gray-700
                           dark:hover:bg-gray-700 dark:hover:text-white">

                    <x-heroicon-o-x-mark class="h-4 w-4" />

                </button>

            </div>


            {{-- BODY --}}
            <div class="px-5 py-5">

                <label
                    for="openingBalance"
                    class="mb-2 block text-sm font-medium
                           text-gray-700 dark:text-gray-300">

                    Saldo Awal

                </label>

                <div class="flex overflow-hidden rounded-lg
                            border border-gray-300
                            bg-white
                            focus-within:border-emerald-500
                            focus-within:ring-1 focus-within:ring-emerald-500
                            dark:border-gray-600 dark:bg-gray-700">

                    <div class="flex items-center border-r border-gray-300
                                bg-gray-50 px-3
                                text-sm font-semibold text-gray-500
                                dark:border-gray-600
                                dark:bg-gray-700 dark:text-gray-300">

                        Rp

                    </div>

                    <input
                        id="openingBalance"
                        type="number"
                        min="0"
                        step="1"
                        wire:model="openingBalance"
                        placeholder="0"
                        class="min-w-0 flex-1 border-0
                               bg-transparent px-3 py-2.5
                               text-sm font-semibold
                               text-gray-900
                               outline-none ring-0
                               focus:border-0 focus:ring-0
                               dark:text-white">

                </div>

                @error('openingBalance')
                    <p class="mt-2 text-xs font-medium text-red-500">
                        {{ $message }}
                    </p>
                @enderror


                <div class="mt-4 rounded-lg
                            bg-gray-50 px-4 py-3
                            dark:bg-gray-700/50">

                    <p class="text-xs leading-5
                              text-gray-500 dark:text-gray-400">

                        Saldo awal diinput manual. Omzet, Pengeluaran,
                        dan Pembelian Barang akan masuk otomatis ke Buku Kas.

                    </p>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="flex items-center justify-end gap-2
                        border-t border-gray-200
                        bg-gray-50 px-5 py-3
                        dark:border-gray-700 dark:bg-gray-800">

                <button
                    type="button"
                    wire:click="closeOpeningBalanceModal"
                    class="rounded-lg border border-gray-300
                           bg-white px-4 py-2
                           text-sm font-medium text-gray-600
                           transition hover:bg-gray-100
                           dark:border-gray-600
                           dark:bg-gray-700
                           dark:text-gray-300
                           dark:hover:bg-gray-600">

                    Batal

                </button>

                <button
                    type="button"
                    wire:click="saveOpeningBalance"
                    wire:loading.attr="disabled"
                    wire:target="saveOpeningBalance"
                    class="inline-flex min-w-[76px] items-center justify-center
                           rounded-lg bg-emerald-600
                           px-4 py-2
                           text-sm font-semibold text-white
                           transition hover:bg-emerald-700
                           disabled:cursor-not-allowed disabled:opacity-60">

                    <span
                        wire:loading.remove
                        wire:target="saveOpeningBalance">
                        Simpan
                    </span>

                    <span
                        wire:loading
                        wire:target="saveOpeningBalance">
                        ...
                    </span>

                </button>

            </div>

        </div>

    </div>

@endif

</div>