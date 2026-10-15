<div class="ioc-view space-y-6">
    {{-- CSS lokal halaman operasional — tidak perlu npm run build --}}
    <style>
    @verbatim
/* ================================================================
   BIAYA OPERASIONAL INTERNAL — CSS BIASA TANPA NPM / VITE
   Semua selector dibatasi pada .ioc-view. Dashboard lain tidak berubah.
   Source tetap Blade asli: fitur dan event wire: dipertahankan.
   ================================================================ */

.ioc-view {
    width: 100%;
    min-width: 0;
    box-sizing: border-box;
    color: #111827;
    font-family: inherit;
}

.ioc-view *, .ioc-view *::before, .ioc-view *::after {
    box-sizing: border-box;
}

.ioc-view.space-y-6 > :not(style):not([hidden]) ~ :not(style):not([hidden]) {
    margin-top: 24px;
}

.ioc-view .space-y-2 > * + * { margin-top: 8px; }
.ioc-view .space-y-3 > * + * { margin-top: 12px; }
.ioc-view .space-y-5 > * + * { margin-top: 20px; }
.ioc-view table { border-collapse: collapse; border-spacing: 0; }
.ioc-view button { cursor: pointer; font: inherit; }
.ioc-view input, .ioc-view textarea { font: inherit; min-width: 0; }
.ioc-view textarea { resize: vertical; }
.ioc-view input[type="text"], .ioc-view input[type="number"],
.ioc-view input[type="month"], .ioc-view input[type="date"],
.ioc-view textarea {
    padding: 9px 12px;
    border-width: 1px;
    border-style: solid;
    background-clip: padding-box;
}
.ioc-view input[type="text"]:focus, .ioc-view input[type="number"]:focus,
.ioc-view input[type="month"]:focus, .ioc-view input[type="date"]:focus,
.ioc-view textarea:focus { outline: none; }
.ioc-view input::placeholder, .ioc-view textarea::placeholder { color:#9ca3af; }
.ioc-view a { text-decoration: none; }
.ioc-view .divide-y > * + * { border-top: 1px solid #f3f4f6; }
.ioc-view .last\:border-0:last-child { border-width: 0; }
.ioc-view .animate-spin { animation: ioc-spin 1s linear infinite; }
@keyframes ioc-spin { to { transform:rotate(360deg); } }

/* Default desktop/mobile fallback sebelum media query aktif */
.ioc-view .lg\:block { display: none; }
.ioc-view .lg\:hidden { display: block; }


/* ================== TATA LETAK & POSISI ================== */

.ioc-view .flex {
    display: flex;
}

.ioc-view .inline-flex {
    display: inline-flex;
}

.ioc-view .grid {
    display: grid;
}

.ioc-view .block {
    display: block;
}

.ioc-view .hidden {
    display: none;
}

.ioc-view .flex-col {
    flex-direction: column;
}

.ioc-view .flex-wrap {
    flex-wrap: wrap;
}

.ioc-view .flex-1 {
    flex: 1 1 0%;
}

.ioc-view .items-center {
    align-items: center;
}

.ioc-view .items-start {
    align-items: flex-start;
}

.ioc-view .justify-between {
    justify-content: space-between;
}

.ioc-view .justify-center {
    justify-content: center;
}

.ioc-view .justify-end {
    justify-content: flex-end;
}

.ioc-view .relative {
    position: relative;
}

.ioc-view .absolute {
    position: absolute;
}

.ioc-view .fixed {
    position: fixed;
}

.ioc-view .sticky {
    position: sticky;
}

.ioc-view .inset-0 {
    inset: 0;
}

.ioc-view .inset-y-0 {
    top: 0; bottom: 0;
}

.ioc-view .left-0 {
    left: 0;
}

.ioc-view .top-0 {
    top: 0;
}

.ioc-view .bottom-0 {
    bottom: 0;
}

.ioc-view .z-10 {
    z-index: 10;
}

.ioc-view .z-40 {
    z-index: 40;
}

.ioc-view .grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

.ioc-view .grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.ioc-view .grid-cols-12 {
    grid-template-columns: repeat(12, minmax(0, 1fr));
}

.ioc-view .col-span-2 {
    grid-column: span 2 / span 2;
}

.ioc-view .col-span-3 {
    grid-column: span 3 / span 3;
}

.ioc-view .col-span-5 {
    grid-column: span 5 / span 5;
}

.ioc-view .w-full {
    width: 100%;
}

.ioc-view .min-w-full {
    min-width: 100%;
}

.ioc-view .min-w-0 {
    min-width: 0;
}

.ioc-view .min-w-\[420px\] {
    min-width: 420px;
}

.ioc-view .max-w-2xl {
    max-width: 42rem;
}

.ioc-view .max-h-\[92vh\] {
    max-height: 92vh;
}

.ioc-view .h-4 {
    height: 1rem;
}

.ioc-view .w-4 {
    width: 1rem;
}

.ioc-view .h-5 {
    height: 1.25rem;
}

.ioc-view .w-5 {
    width: 1.25rem;
}

.ioc-view .overflow-x-auto {
    overflow-x: auto;
}

.ioc-view .overflow-y-auto {
    overflow-y: auto;
}


/* ================== SPACING YANG DIPAKAI ================== */

.ioc-view .gap-1 {
    gap: 4px;
}

.ioc-view .gap-2 {
    gap: 8px;
}

.ioc-view .gap-3 {
    gap: 12px;
}

.ioc-view .gap-4 {
    gap: 16px;
}

.ioc-view .mb-1 {
    margin-bottom: 4px;
}

.ioc-view .mt-0\.5 {
    margin-top: 2px;
}

.ioc-view .mt-1 {
    margin-top: 4px;
}

.ioc-view .mt-2 {
    margin-top: 8px;
}

.ioc-view .p-3 {
    padding: 12px;
}

.ioc-view .p-4 {
    padding: 16px;
}

.ioc-view .p-5 {
    padding: 20px;
}

.ioc-view .pb-2 {
    padding-bottom: 8px;
}

.ioc-view .pt-1 {
    padding-top: 4px;
}

.ioc-view .pt-3 {
    padding-top: 12px;
}

.ioc-view .px-3 {
    padding-left: 12px; padding-right: 12px;
}

.ioc-view .px-4 {
    padding-left: 16px; padding-right: 16px;
}

.ioc-view .px-5 {
    padding-left: 20px; padding-right: 20px;
}

.ioc-view .py-1\.5 {
    padding-top:6px; padding-bottom:6px;
}

.ioc-view .py-10 {
    padding-top: 40px; padding-bottom: 40px;
}

.ioc-view .py-2 {
    padding-top: 8px; padding-bottom: 8px;
}

.ioc-view .py-3 {
    padding-top: 12px; padding-bottom: 12px;
}

.ioc-view .py-4 {
    padding-top: 16px; padding-bottom: 16px;
}

.ioc-view .py-5 {
    padding-top: 20px; padding-bottom: 20px;
}


/* ================== BENTUK, WARNA, DAN TYPOGRAPHY ================== */

.ioc-view .align-top {
    vertical-align: top;
}

.ioc-view .bg-black\/60 {
    background-color: rgba(0,0,0,.60);
}

.ioc-view .bg-emerald-600 {
    background-color: #059669;
}

.ioc-view .bg-gray-100 {
    background-color: #f3f4f6;
}

.ioc-view .bg-gray-200 {
    background-color: #e5e7eb;
}

.ioc-view .bg-gray-50 {
    background-color: #f9fafb;
}

.ioc-view .bg-gray-50\/50 {
    background-color: rgba(249,250,251,.50);
}

.ioc-view .bg-red-600 {
    background-color: #dc2626;
}

.ioc-view .bg-white {
    background-color: #ffffff;
}

.ioc-view .bg-white\/60 {
    background-color: rgba(255,255,255,.60);
}

.ioc-view .bg-yellow-500 {
    background-color: #eab308;
}

.ioc-view .border {
    border-width: 1px; border-style: solid;
}

.ioc-view .border-b {
    border-bottom-width: 1px; border-bottom-style: solid;
}

.ioc-view .border-gray-100 {
    border-color: #f3f4f6;
}

.ioc-view .border-gray-200 {
    border-color: #e5e7eb;
}

.ioc-view .border-gray-300 {
    border-color: #d1d5db;
}

.ioc-view .border-t {
    border-top-width: 1px; border-top-style: solid;
}

.ioc-view .break-words {
    overflow-wrap: break-word;
}

.ioc-view .divide-gray-100 {
    border-color:#f3f4f6;
}

.ioc-view .font-bold {
    font-weight: 700;
}

.ioc-view .font-medium {
    font-weight: 500;
}

.ioc-view .font-semibold {
    font-weight: 600;
}

.ioc-view .leading-none {
    line-height: 1;
}

.ioc-view .opacity-25 {
    opacity: .25;
}

.ioc-view .opacity-75 {
    opacity: .75;
}

.ioc-view .pl-3 {
    padding-left: 12px;
}

.ioc-view .pl-9 {
    padding-left: 36px;
}

.ioc-view .pointer-events-none {
    pointer-events: none;
}

.ioc-view .rounded-lg {
    border-radius: 0.5rem;
}

.ioc-view .rounded-xl {
    border-radius: 0.75rem;
}

.ioc-view .shadow {
    box-shadow: 0 1px 3px rgba(0,0,0,.12), 0 1px 2px rgba(0,0,0,.07);
}

.ioc-view .shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0,0,0,.1),0 8px 10px -6px rgba(0,0,0,.1);
}

.ioc-view .text-base {
    font-size: 1rem; line-height: 1.5rem;
}

.ioc-view .text-center {
    text-align: center;
}

.ioc-view .text-gray-400 {
    color: #9ca3af;
}

.ioc-view .text-gray-500 {
    color: #6b7280;
}

.ioc-view .text-gray-600 {
    color: #4b5563;
}

.ioc-view .text-gray-700 {
    color: #374151;
}

.ioc-view .text-gray-800 {
    color: #1f2937;
}

.ioc-view .text-gray-900 {
    color: #111827;
}

.ioc-view .text-left {
    text-align: left;
}

.ioc-view .text-lg {
    font-size: 1.125rem; line-height: 1.75rem;
}

.ioc-view .text-red-500 {
    color: #ef4444;
}

.ioc-view .text-red-600 {
    color: #dc2626;
}

.ioc-view .text-right {
    text-align: right;
}

.ioc-view .text-sm {
    font-size: .875rem; line-height: 1.25rem;
}

.ioc-view .text-white {
    color: #ffffff;
}

.ioc-view .text-xl {
    font-size: 1.25rem; line-height: 1.75rem;
}

.ioc-view .text-xs {
    font-size: .75rem; line-height: 1rem;
}

.ioc-view .tracking-wide {
    letter-spacing: .025em;
}

.ioc-view .transition-colors {
    transition-property: color, background-color, border-color; transition-duration: .15s;
}

.ioc-view .uppercase {
    text-transform: uppercase;
}

.ioc-view .whitespace-nowrap {
    white-space: nowrap;
}


/* Detail tabel, border antar baris, font control */
.ioc-view .min-w-full { width:100%; }
.ioc-view thead th { font-weight:600; }
.ioc-view .divide-y > * + * { border-top-width:1px; border-top-style:solid; }
.ioc-view input, .ioc-view textarea { border-radius:8px; }
.ioc-view .px-4 input { max-width:100%; }


/* ================== INTERAKSI TOMBOL & FOKUS ================== */

.ioc-view .hover\:bg-emerald-700:hover {
    background-color: #047857;
}

.ioc-view .hover\:bg-yellow-600:hover {
    background-color: #ca8a04;
}

.ioc-view .hover\:bg-red-700:hover {
    background-color: #b91c1c;
}

.ioc-view .hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

.ioc-view .hover\:bg-gray-300:hover {
    background-color: #d1d5db;
}

.ioc-view .hover\:text-gray-600:hover {
    color: #4b5563;
}

.ioc-view .hover\:text-red-600:hover {
    color: #dc2626;
}

.ioc-view .focus\:border-emerald-500:focus {
    border-color: #10b981;
}

.ioc-view .focus\:ring-emerald-500:focus {
    box-shadow: 0 0 0 2px rgba(16,185,129,.28);
}

.ioc-view .disabled\:opacity-60:disabled {
    opacity: .60;
}

.ioc-view .disabled\:cursor-not-allowed:disabled {
    cursor: not-allowed;
}


/* ================== MODAL MULTI ITEM (BUKAN UTILITY VITE) ================== */
.ioc-view .ioc-modal-overlay {
    position: fixed !important;
    z-index: 99999 !important;
    inset: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 12px !important;
    margin-top: 0 !important;
    box-sizing: border-box !important;
}
.ioc-view .ioc-modal-backdrop {
    position: absolute !important;
    inset: 0 !important;
    background: rgba(0,0,0,.67) !important;
}
.ioc-view .ioc-modal-panel {
    position: relative !important;
    z-index: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    width: 100% !important;
    max-width: 680px !important;
    max-height: calc(100vh - 24px) !important;
    max-height: calc(100dvh - 24px) !important;
    overflow: hidden !important;
    border-radius: 12px !important;
    box-shadow: 0 18px 50px rgba(0,0,0,.32);
}
.ioc-view .ioc-modal-head {
    position: relative;
    top: auto;
    z-index: 2;
    flex-shrink: 0;
}
.ioc-view .ioc-modal-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto !important;
    overscroll-behavior: contain;
}
.ioc-view .ioc-modal-foot {
    position: relative;
    bottom: auto;
    flex-shrink: 0;
    z-index: 2;
}
.ioc-view .ioc-modal-foot button:disabled { opacity: .6; cursor: not-allowed; }
.ioc-view [wire\:loading\.flex] { pointer-events: none; }


/* ================== RESPONSIVE >= 640PX ================== */
@media (min-width:640px) {

    .ioc-view .sm\:flex-row {
        flex-direction: row;
    }

    .ioc-view .sm\:items-center {
        align-items: center;
    }

    .ioc-view .sm\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .ioc-view .sm\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .ioc-view .sm\:text-2xl {
        font-size: 1.5rem; line-height: 2rem;
    }

    .ioc-view .sm\:text-xl {
        font-size: 1.25rem; line-height: 1.75rem;
    }

    .ioc-view .sm\:p-4 {
        padding: 16px;
    }

    .ioc-view .sm\:p-5 {
        padding: 20px;
    }

    .ioc-view .sm\:px-6 {
        padding-left: 24px; padding-right: 24px;
    }

}


/* ================== RESPONSIVE >= 1024PX ================== */
@media (min-width:1024px) {

    .ioc-view .lg\:block {
        display: block;
    }

    .ioc-view .lg\:hidden {
        display: none;
    }

}


/* ================== MOBILE KHUSUS ================== */
@media (max-width:639px) {
    .ioc-view .ioc-modal-overlay { padding: 8px !important; }
    .ioc-view .ioc-modal-panel {
        max-height: calc(100vh - 16px) !important;
        max-height: calc(100dvh - 16px) !important;
    }
    .ioc-view .ioc-modal-body { padding: 14px; }
    .ioc-view .ioc-modal-head, .ioc-view .ioc-modal-foot { padding-left:14px; padding-right:14px; }
}


/* ================== DARK MODE ================== */

.dark .ioc-view .dark\:bg-gray-800 {
    background-color: #1f2937;
}

.dark .ioc-view .dark\:bg-gray-700 {
    background-color: #374151;
}

.dark .ioc-view .dark\:bg-gray-700\/60 {
    background-color: rgba(55,65,81,.60);
}

.dark .ioc-view .dark\:bg-gray-700\/50 {
    background-color: rgba(55,65,81,.50);
}

.dark .ioc-view .dark\:bg-gray-700\/40 {
    background-color: rgba(55,65,81,.40);
}

.dark .ioc-view .dark\:bg-gray-700\/30 {
    background-color: rgba(55,65,81,.30);
}

.dark .ioc-view .dark\:bg-gray-800\/60 {
    background-color: rgba(31,41,55,.60);
}

.dark .ioc-view .dark\:border-gray-700 {
    border-color: #374151;
}

.dark .ioc-view .dark\:border-gray-600 {
    border-color: #4b5563;
}

.dark .ioc-view .dark\:text-white {
    color: #ffffff;
}

.dark .ioc-view .dark\:text-gray-200 {
    color: #e5e7eb;
}

.dark .ioc-view .dark\:text-gray-300 {
    color: #d1d5db;
}

.dark .ioc-view .dark\:text-gray-400 {
    color: #9ca3af;
}

.dark .ioc-view .dark\:text-gray-500 {
    color: #6b7280;
}

.dark .ioc-view .dark\:text-red-400 {
    color: #f87171;
}

.dark .ioc-view .dark\:hover\:bg-gray-600:hover {
    background-color: #4b5563;
}

.dark .ioc-view .dark\:hover\:bg-gray-700\/40:hover {
    background-color: rgba(55,65,81,.40);
}

.dark .ioc-view .dark\:hover\:text-white:hover {
    color: #ffffff;
}

.dark .ioc-view .dark\:hover\:text-gray-200:hover {
    color: #e5e7eb;
}


.dark .ioc-view .divide-y > * + * { border-top-color:#374151; }
.dark .ioc-view .dark\:divide-gray-700 > * + * { border-top-color:#374151; }
.dark .ioc-view input::placeholder, .dark .ioc-view textarea::placeholder { color:#9ca3af; }
.dark .ioc-view input[type="date"], .dark .ioc-view input[type="month"] { color-scheme:dark; }
.dark .ioc-view .ioc-modal-panel { border-color: #374151; }
    @endverbatim
    </style>


    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                Biaya Operasional Internal
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Kelola biaya operasional internal berdasarkan bon dan rincian item.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreateModal"
            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white inline-flex items-center justify-center gap-2 text-sm">

            <span class="text-lg leading-none">+</span>
            Tambah Data
        </button>
    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400">
                Total Bulan Ini
            </p>

            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">
                Rp {{ number_format($totalThisMonth ?? 0, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400">
                Total Minggu Ini
            </p>

            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">
                Rp {{ number_format($totalThisWeek ?? 0, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs uppercase tracking-wide text-gray-400">
                Jumlah Transaksi
            </p>

            <p class="text-xl font-bold text-gray-800 dark:text-white mt-1">
                {{ $datas->total() }}
            </p>
        </div>

    </div>

    {{-- DATA --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">

        {{-- FILTER --}}
        <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">

                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Data Biaya
                </h2>

                <div class="flex flex-col sm:flex-row gap-2">

                    <input
                        type="month"
                        wire:model.live="monthFilter"
                        class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">

                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Cari item, supplier, atau invoice..."
                        class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">

                </div>
            </div>
        </div>

        <div class="relative">

            <div
                wire:loading.flex
                wire:target="search,monthFilter"
                style="display:none"
                class="absolute inset-0 bg-white/60 dark:bg-gray-800/60 items-center justify-center z-10">

                <span class="text-sm text-gray-500 dark:text-gray-300">
                    Memuat...
                </span>
            </div>

            {{-- DESKTOP --}}
            <div class="hidden lg:block overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="bg-gray-50 dark:bg-gray-700/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">
                                Tanggal
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">
                                No. Invoice
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">
                                Supplier
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">
                                Rincian Item
                            </th>

                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">
                                Total
                            </th>

                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($datas as $cost)

                            <tr
                                wire:key="cost-{{ $cost->id }}"
                                class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40">

                                {{-- TANGGAL --}}
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-200 align-top">
                                    {{ \Illuminate\Support\Carbon::parse($cost->transaction_date)->format('d/m/Y') }}
                                </td>

                                {{-- INVOICE --}}
                                <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-300 align-top">
                                    {{ $cost->invoice_number ?: '-' }}
                                </td>

                                {{-- SUPPLIER --}}
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200 align-top">
                                    {{ $cost->supplier ?: '-' }}
                                </td>

                                {{-- RINCIAN ITEM --}}
                                <td class="px-4 py-3 align-top">

                                    <div class="min-w-[420px]">

                                        {{-- HEADER RINCIAN --}}
                                        <div class="grid grid-cols-12 gap-2 pb-2 border-b border-gray-200 dark:border-gray-600 text-xs font-medium text-gray-400">

                                            <span class="col-span-5">
                                                Item
                                            </span>

                                            <span class="col-span-2 text-right">
                                                Qty
                                            </span>

                                            <span class="col-span-2 text-right">
                                                Harga
                                            </span>

                                            <span class="col-span-3 text-right">
                                                Subtotal
                                            </span>

                                        </div>

                                        {{-- BARIS ITEM --}}
                                        @forelse($cost->items as $detail)

                                            <div class="grid grid-cols-12 gap-2 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0 text-xs text-gray-700 dark:text-gray-200">

                                                <span class="col-span-5 break-words">
                                                    {{ $detail->item_name }}
                                                </span>

                                                <span class="col-span-2 text-right">
                                                    {{ number_format($detail->qty, 2, ',', '.') }}
                                                </span>

                                                <span class="col-span-2 text-right whitespace-nowrap">
                                                    {{ number_format($detail->price, 0, ',', '.') }}
                                                </span>

                                                <span class="col-span-3 text-right whitespace-nowrap font-medium">
                                                    {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                </span>

                                            </div>

                                        @empty
                                            {{-- Transaksi lama, sebelum rincian multi-item tersedia --}}
                                            <div class="grid grid-cols-12 gap-2 py-2 text-xs text-gray-700 dark:text-gray-200">
                                                <span class="col-span-5 break-words">{{ $cost->item_name ?: '-' }}</span>
                                                <span class="col-span-2 text-right">{{ number_format($cost->qty ?? 0, 2, ',', '.') }}</span>
                                                <span class="col-span-2 text-right">{{ number_format($cost->price ?? 0, 0, ',', '.') }}</span>
                                                <span class="col-span-3 text-right">{{ number_format($cost->total ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        @endforelse

                                    </div>

                                    @if($cost->notes)
                                        <p class="text-xs text-gray-400 mt-2">
                                            Catatan: {{ $cost->notes }}
                                        </p>
                                    @endif

                                </td>

                                {{-- TOTAL --}}
                                <td class="px-4 py-3 text-right align-top whitespace-nowrap font-bold text-red-600 dark:text-red-400">
                                    Rp {{ number_format($cost->total, 0, ',', '.') }}
                                </td>

                                {{-- AKSI --}}
                                <td class="px-4 py-3 align-top">

                                    <div class="flex flex-wrap justify-center gap-2">

                                        <button
                                            type="button"
                                            wire:click="edit({{ $cost->id }})"
                                            class="px-3 py-1.5 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white text-xs">
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            x-on:click="
                                                Swal.fire({
                                                    title: 'Yakin hapus bon ini?',
                                                    text: 'Seluruh rincian item juga akan dihapus.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'Ya, hapus',
                                                    cancelButtonText: 'Batal',
                                                    confirmButtonColor: '#dc2626',
                                                    cancelButtonColor: '#6b7280'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.delete({{ $cost->id }})
                                                    }
                                                })
                                            "
                                            class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs">
                                            Hapus
                                        </button>

                                        <a
                                            href="{{ route('internal-operational-costs.invoice', $cost->id) }}"
                                            target="_blank"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs">
                                            Invoice
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-500 dark:text-gray-400">

                                    @if($search || $monthFilter)
                                        Tidak ada data yang cocok dengan filter.
                                    @else
                                        Belum ada data.
                                    @endif

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- MOBILE / TABLET --}}
            <div class="lg:hidden divide-y divide-gray-100 dark:divide-gray-700">

                @forelse($datas as $cost)

                    <div
                        wire:key="cost-mobile-{{ $cost->id }}"
                        class="p-4 space-y-3">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $cost->items->count() ?: 1 }} Item
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    {{ \Illuminate\Support\Carbon::parse($cost->transaction_date)->format('d/m/Y') }}

                                    @if($cost->invoice_number)
                                        &bull; {{ $cost->invoice_number }}
                                    @endif
                                </p>

                            </div>

                            <p class="font-bold text-red-600 dark:text-red-400 whitespace-nowrap">
                                Rp {{ number_format($cost->total, 0, ',', '.') }}
                            </p>

                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Supplier: {{ $cost->supplier ?: '-' }}
                        </p>

                        {{-- ITEM MOBILE --}}
                        <div class="space-y-2">

                            @forelse($cost->items as $detail)

                                <div class="rounded-lg bg-gray-50 dark:bg-gray-700/40 p-3">

                                    <p class="text-sm font-medium text-gray-800 dark:text-white">
                                        {{ $detail->item_name }}
                                    </p>

                                    <div class="flex justify-between items-center gap-2 mt-2 text-xs">

                                        <span class="text-gray-500 dark:text-gray-400">
                                            {{ number_format($detail->qty, 2, ',', '.') }}
                                            x Rp {{ number_format($detail->price, 0, ',', '.') }}
                                        </span>

                                        <span class="font-semibold text-gray-800 dark:text-white">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </span>

                                    </div>

                                </div>

                            @empty
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-700/40 p-3">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $cost->item_name ?: 'Item lama' }}</p>
                                    <div class="flex justify-between gap-2 mt-2 text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">{{ number_format($cost->qty ?? 0, 2, ',', '.') }} x Rp {{ number_format($cost->price ?? 0, 0, ',', '.') }}</span>
                                        <span class="font-semibold text-gray-800 dark:text-white">Rp {{ number_format($cost->total ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforelse

                        </div>

                        @if($cost->notes)
                            <p class="text-xs text-gray-400">
                                Catatan: {{ $cost->notes }}
                            </p>
                        @endif

                        {{-- AKSI MOBILE --}}
                        <div class="flex gap-2 pt-1">

                            <button
                                type="button"
                                wire:click="edit({{ $cost->id }})"
                                class="flex-1 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white text-xs">
                                Edit
                            </button>

                            <button
                                type="button"
                                x-on:click="
                                    Swal.fire({
                                        title: 'Yakin hapus bon ini?',
                                        text: 'Seluruh rincian item juga akan dihapus.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, hapus',
                                        cancelButtonText: 'Batal',
                                        confirmButtonColor: '#dc2626'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.delete({{ $cost->id }})
                                        }
                                    })
                                "
                                class="flex-1 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs">
                                Hapus
                            </button>

                            <a
                                href="{{ route('internal-operational-costs.invoice', $cost->id) }}"
                                target="_blank"
                                class="flex-1 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs text-center">
                                Invoice
                            </a>

                        </div>

                    </div>

                @empty

                    <div class="py-10 text-center text-sm text-gray-500">
                        Belum ada data.
                    </div>

                @endforelse

                @if($datas->count())

                    <div class="p-4 flex justify-between items-center bg-gray-50 dark:bg-gray-700/40">

                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Total Halaman Ini
                        </span>

                        <span class="font-bold text-red-600 dark:text-red-400">
                            Rp {{ number_format($datas->sum('total'), 0, ',', '.') }}
                        </span>

                    </div>

                @endif

            </div>

        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $datas->links() }}
        </div>

    </div>


    {{-- MODAL TAMBAH / EDIT --}}
    @if($showFormModal)

        <div
            class="ioc-modal-overlay fixed inset-0 z-40 flex items-center justify-center p-3 sm:p-4"
            wire:key="modal-{{ $editingId ?? 'new' }}">

            {{-- BACKDROP --}}
            <div
                class="ioc-modal-backdrop absolute inset-0 bg-black/60"
                wire:click="closeModal">
            </div>

            {{-- MODAL CONTENT --}}
            <div class="ioc-modal-panel relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[92vh] overflow-y-auto border border-gray-100 dark:border-gray-700">

                {{-- MODAL HEADER --}}
                <div class="ioc-modal-head sticky top-0 z-10 flex items-center justify-between px-4 sm:px-6 py-4 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $editingId ? 'Edit Data' : 'Tambah Data' }}
                        </h2>

                        <p class="text-xs text-gray-400 mt-1">
                            Satu bon dapat memiliki beberapa item.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-white">

                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                        </svg>

                    </button>

                </div>

                {{-- BODY --}}
                <div class="ioc-modal-body px-4 sm:px-6 py-5 space-y-5">

                    {{-- HEADER BON --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- TANGGAL --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tanggal Bon
                            </label>

                            <input
                                type="date"
                                wire:model="transaction_date"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">

                            @error('transaction_date')
                                <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- NO INVOICE --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                No. Invoice
                            </label>

                            <input
                                type="text"
                                wire:model="invoice_number"
                                placeholder="Opsional"
                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">

                            @error('invoice_number')
                                <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>

                    {{-- SUPPLIER --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Supplier
                        </label>

                        <input
                            type="text"
                            wire:model="supplier"
                            placeholder="Nama supplier (opsional)"
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">

                        @error('supplier')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- RINCIAN ITEM --}}
                    <div class="space-y-3">

                        <div class="flex items-center justify-between gap-3">

                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                Rincian Item
                            </h3>

                            <button
                                type="button"
                                wire:click="addItem"
                                class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-2 text-xs font-semibold text-white transition-colors">

                                <span class="text-base leading-none">+</span>
                                Tambah Item
                            </button>

                        </div>

                        {{-- LOOP ITEM --}}
                        @foreach($items as $index => $detail)

                            <div
                                wire:key="detail-item-{{ $detail['key'] }}"
                                class="rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700/30 p-3 sm:p-4 space-y-3">

                                {{-- ITEM HEADER --}}
                                <div class="flex items-center justify-between">

                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        Item {{ $index + 1 }}
                                    </span>

                                    @if(count($items) > 1)

                                        <button
                                            type="button"
                                            wire:click="removeItem({{ $index }})"
                                            class="text-xs font-medium text-red-500 hover:text-red-600">
                                            Hapus Item
                                        </button>

                                    @endif

                                </div>

                                {{-- NAMA ITEM --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                                        Nama Item / Keterangan
                                    </label>

                                    <input
                                        type="text"
                                        wire:model="items.{{ $index }}.item_name"
                                        placeholder="Contoh: Kardus, lakban, plastik packing"
                                        class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">

                                    @error('items.'.$index.'.item_name')
                                        <small class="text-red-500">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- QTY DAN HARGA --}}
                                <div class="grid grid-cols-2 gap-3">

                                    {{-- QTY --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                                            Qty
                                        </label>

                                        <input
                                            type="number"
                                            min="0.01"
                                            step="any"
                                            wire:model.live.debounce.300ms="items.{{ $index }}.qty"
                                            class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">

                                        @error('items.'.$index.'.qty')
                                            <small class="text-red-500">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- HARGA --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                                            Harga Pokok / Satuan
                                        </label>

                                        <div class="relative">

                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-gray-400 pointer-events-none">
                                                Rp
                                            </span>

                                            <input
                                                type="number"
                                                min="0"
                                                step="1"
                                                wire:model.live.debounce.300ms="items.{{ $index }}.price"
                                                placeholder="0"
                                                class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm pl-9 focus:border-emerald-500 focus:ring-emerald-500">

                                        </div>

                                        @error('items.'.$index.'.price')
                                            <small class="text-red-500">{{ $message }}</small>
                                        @enderror
                                    </div>

                                </div>

                                {{-- SUBTOTAL --}}
                                <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-600 pt-3">

                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        Subtotal Item {{ $index + 1 }}
                                    </span>

                                    <span class="text-sm font-bold text-gray-800 dark:text-white">
                                        Rp {{ number_format(
                                            (float) ($detail['qty'] ?? 0) *
                                            (float) ($detail['price'] ?? 0),
                                            0, ',', '.'
                                        ) }}
                                    </span>

                                </div>

                            </div>

                        @endforeach

                        @error('items')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror

                    </div>

                    {{-- GRAND TOTAL --}}
                    @php
                        $grandTotal = collect($items)->sum(function ($row) {
                            return (float) ($row['qty'] ?? 0)
                                 * (float) ($row['price'] ?? 0);
                        });
                    @endphp

                    <div class="rounded-xl bg-gray-100 dark:bg-gray-700 px-4 py-4 flex items-center justify-between gap-3">

                        <div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Total Bon
                            </p>

                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ count($items) }} item
                            </p>
                        </div>

                        <span class="text-lg sm:text-xl font-bold text-red-600 dark:text-red-400 whitespace-nowrap">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </span>

                    </div>

                    {{-- CATATAN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Catatan
                        </label>

                        <textarea
                            wire:model="notes"
                            rows="2"
                            placeholder="Catatan tambahan (opsional)"
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>

                        @error('notes')
                            <small class="text-red-500">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                {{-- FOOTER MODAL --}}
                <div class="ioc-modal-foot sticky bottom-0 z-10 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 px-4 sm:px-6 py-4">

                    <div class="flex items-center justify-end gap-2">

                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white text-sm transition-colors">
                            Batal
                        </button>

                        <button
                            type="button"
                            wire:click="save"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed text-white inline-flex items-center gap-2 text-sm transition-colors">

                            <svg
                                wire:loading
                                wire:target="save"
                                class="animate-spin h-4 w-4"
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

                            {{ $editingId ? 'Update' : 'Simpan' }}

                        </button>

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>
