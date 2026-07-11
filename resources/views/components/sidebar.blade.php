<aside
    x-bind:class="sidebar ? 'w-72' : 'w-20'"
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

        <button
            @click="sidebar=!sidebar"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition text-gray-700 dark:text-white">
            ☰
        </button>
    </div>

    {{-- MENU --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-gray-700 dark:text-white
                  hover:bg-gray-100 dark:hover:bg-gray-800
                  {{ request()->routeIs('dashboard') ? 'bg-emerald-500 text-white' : '' }}">

            <x-heroicon-o-home class="w-6 h-6 text-emerald-600" />
            <span x-show="sidebar">Dashboard</span>
        </a>

        {{-- Upload --}}
      
        </a>
         <div class="pt-4 px-4">
        
            <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase ">
                Transaksi
            </span>
        </div>
         <a href="{{ route('transactions') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-chart-bar class="w-6 h-6 text-emerald-600" />
            <span x-show="sidebar">Tambah Transaksi</span>
        </a>
          <a href="{{ route('upload') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition
                  text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800">

            <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-emerald-600" />
            <span x-show="sidebar">Upload Accurate</span>
        <a href="{{ route('accurate') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-chart-bar class="w-6 h-6 text-emerald-600" />
            <span x-show="sidebar">Arsip Import Accurate</span>
        </a>

        <div class="pt-4 px-4">
            <span class="text-xs font-semibold tracking-widest text-gray-400 uppercase">
                Laporan
            </span>
        </div>

        <a href="{{ route('ledger.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-book-open class="w-6 h-6 text-emerald-600" />
            <span x-show="sidebar">Buku Besar</span>
        </a>

        <a href="{{ route('accounts') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-rectangle-stack class="w-6 h-6 text-emerald-600" />
            <span x-show="sidebar">Master Akun</span>
        </a>

        <a href="{{ route('reports.operating-expense') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-document-text class="w-6 h-6 text-emerald-600" />
            <span x-show="sidebar">Beban Operasional</span>
        </a>

        <a href="{{ route('reports.operating-expense-detail') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-white">
            <x-heroicon-o-document-text class="w-6 h-6 text-emerald-600" />
            <span x-show="sidebar">Beban Operasional Detail</span>
        </a>

    </nav>

    {{-- FOOTER --}}
    <div class="border-t border-gray-200 dark:border-gray-800 p-4">
        <div class="flex items-center gap-3">

            <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div x-show="sidebar">
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