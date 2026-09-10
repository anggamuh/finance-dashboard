<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <button type="button" @click="sidebarOpen = true"
            class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 lg:hidden"
            aria-label="Buka menu">
            <x-heroicon-o-bars-3 class="h-6 w-6" />
        </button>

        <div class="ml-auto flex items-center gap-2 sm:gap-4">
            <button type="button" @click="toggleTheme()"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                :aria-label="darkMode ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'">
                <x-heroicon-o-sun x-cloak x-show="darkMode" class="h-5 w-5" />
                <x-heroicon-o-moon x-show="! darkMode" class="h-5 w-5" />
            </button>

            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = ! open" class="flex items-center gap-3 rounded-full"
                    aria-label="Buka menu pengguna" :aria-expanded="open">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-600 font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </button>

                <div x-cloak x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-3 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900">
                    <a href="{{ route('profile') }}"
                        class="block px-4 py-3 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                        Profil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full px-4 py-3 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
