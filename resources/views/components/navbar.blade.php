<header class="sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">

    <div class="flex justify-end items-center px-8 py-5">

        <div class="flex items-left gap-5">

           

            <div x-data="{ open: false }" class="relative">

                <button @click="open=!open" class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex justify-center items-center">

                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                    </div>

                </button>

                <div x-show="open" @click.outside="open=false" x-transition
                    class="absolute right-0 mt-3 bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-800 w-52">

                    <a href="profile" class="block px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-800">

                        Profile

                    </a>

                    <form method="POST" action="{{ route('logout') }}">

                        @csrf

                        <button class="w-full text-left px-4 py-3 hover:bg-red-50 text-red-600">

                            Logout

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</header>
