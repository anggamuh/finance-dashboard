<aside x-bind:class="[
        sidebar ? 'lg:w-72' : 'lg:w-20',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed inset-y-0 left-0 z-50 flex h-screen w-72 flex-col lg:sticky lg:top-0
           transition-all duration-300
           bg-white dark:bg-slate-900
           border-r border-gray-200 dark:border-slate-800">

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

        <button type="button" @click="window.innerWidth >= 1024 ? sidebar = !sidebar : sidebarOpen = false"
            aria-label="Ubah tampilan menu"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition text-gray-700 dark:text-white">
            <x-heroicon-o-bars-3 class="h-5 w-5" />
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
        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
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

        <a href="{{ route('internal-operational-costs') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('internal-operational-costs') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-600" />
            <span>Operasional</span>
        </a>

        <a href="{{ route('revenues') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('revenues') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-currency-dollar class="w-6 h-6 text-emerald-600" />
            <span>Omzet</span>
        </a>

        <a href="{{ route('closing') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('closing') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-check-badge class="w-6 h-6 text-emerald-600" />
            <span>Closing</span>
        </a>

        <div class="pt-4 px-4">
            <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">
                Transaksi
            </span>
        </div>

        <a href="{{ route('transactions') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('transactions*') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-chart-bar class="w-6 h-6 text-emerald-600" />
            <span>Tambah Transaksi</span>
        </a>

        <a href="{{ route('upload') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                  text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('upload') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-emerald-600" />
            <span>Upload Accurate</span>
        </a>

        <a href="{{ route('accurate') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('accurate') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-chart-bar class="w-6 h-6 text-emerald-600" />
            <span>Arsip Import Accurate</span>
        </a>

        <div class="pt-4 px-4">
            <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">
                Laporan
            </span>
        </div>

        <a href="{{ route('ledger.index') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('ledger.index') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-book-open class="w-6 h-6 text-emerald-600" />
            <span>Buku Besar</span>
        </a>
        
        <a href="{{ route('cash-book') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('cash-book') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-book-open class="w-6 h-6 text-emerald-600" />
            <span>Buku Kas</span>
        </a>

        <a href="{{ route('accounts') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('accounts') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-rectangle-stack class="w-6 h-6 text-emerald-600" />
            <span>Master Akun</span>
        </a>

        <a href="{{ route('reports.operating-expense') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('reports.operating-expense') ? 'bg-emerald-500 text-white' : '' }}">
            <x-heroicon-o-document-text class="w-6 h-6 text-emerald-600" />
            <span>Beban Operasional</span>
        </a>

        <a href="{{ route('reports.operating-expense-detail') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white {{ request()->routeIs('reports.operating-expense-detail') ? 'bg-emerald-500 text-white' : '' }}">
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
