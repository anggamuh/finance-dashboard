<aside x-bind:class="sidebar ? 'w-72' : 'w-20'"
    class="sticky top-0 h-screen flex flex-col
           transition-all duration-300
           bg-white dark:bg-gray-900
           border-r border-gray-200 dark:border-gray-800">

    {{-- HEADER --}}
    <div class="flex items-center justify-between px-5 py-5 border-b border-gray-100 dark:border-gray-800">

        <div x-show="sidebar" class="leading-tight">
            <h1 class="text-lg font-bold text-emerald-600">
                Finance Dashboard
            </h1>
            <p class="text-xs text-gray-500">
                Accurate Reporting
            </p>
        </div>

        <button @click="sidebar=!sidebar"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition text-gray-700 dark:text-white">
            ☰
        </button>
    </div>

    {{-- MENU (hilang total saat sidebar ditutup, sisa hamburger doang) --}}
    <nav x-show="sidebar"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="sidebar-scroll flex-1 overflow-y-auto px-3 py-4 space-y-1">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-gray-700 dark:text-white
                  hover:bg-gray-100 dark:hover:bg-gray-800
                  {{ request()->routeIs('dashboard') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-home class="w-6 h-6 text-emerald-600" />
            <span>Dashboard</span>
        </a>

        <div class="pt-4 px-4">
            <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">
                Omzet
            </span>
        </div>

        <a href="{{ route('internal-operational-costs') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-600" />
            <span>Operasional</span>
        </a>

        <a href="{{ route('revenues') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-currency-dollar class="w-6 h-6 text-emerald-600" />
            <span>Omzet</span>
        </a>

        <a href="{{ route('closing') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-check-badge class="w-6 h-6 text-emerald-600" />
            <span>Closing</span>
        </a>

        <div class="pt-4 px-4">
            <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">
                Transaksi
            </span>
        </div>

        <a href="{{ route('transactions') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-chart-bar class="w-6 h-6 text-emerald-600" />
            <span>Tambah Transaksi</span>
        </a>

        <a href="{{ route('upload') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                  text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800">
            <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-emerald-600" />
            <span>Upload Accurate</span>
        </a>

        <a href="{{ route('accurate') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-chart-bar class="w-6 h-6 text-emerald-600" />
            <span>Arsip Import Accurate</span>
        </a>

        <div class="pt-4 px-4">
            <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">
                Laporan
            </span>
        </div>

        <a href="{{ route('ledger.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-book-open class="w-6 h-6 text-emerald-600" />
            <span>Buku Besar</span>
        </a>
        
        <a href="{{ route('cash-book') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-book-open class="w-6 h-6 text-emerald-600" />
            <span>Buku Kas</span>
        </a>

        <a href="{{ route('accounts') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-rectangle-stack class="w-6 h-6 text-emerald-600" />
            <span>Master Akun</span>
        </a>

        <a href="{{ route('reports.operating-expense') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-document-text class="w-6 h-6 text-emerald-600" />
            <span>Beban Operasional</span>
        </a>

        <a href="{{ route('reports.operating-expense-detail') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-document-text class="w-6 h-6 text-emerald-600" />
            <span>Beban Operasional Detail</span>
        </a>

    </nav>

    {{-- FOOTER --}}
    <div x-show="sidebar" class="border-t border-gray-200 dark:border-gray-800 p-4">
        <div class="flex items-center gap-3">

            <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div>
                <p class="font-semibold text-gray-800 dark:text-white text-sm">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-500">
                    Administrator
                </p>
            </div>

        </div>
    </div>

</aside>

<style>
    /* Scrollbar tipis khusus untuk menu sidebar */
    .sidebar-scroll {
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: #cbd5e1 transparent; /* Firefox: thumb, track */
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 5px; /* lebar scrollbar, sebelumnya default browser ~15-17px */
    }

    .sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 9999px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    .dark .sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: #4b5563;
    }

    .dark .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background-color: #6b7280;
    }
</style>