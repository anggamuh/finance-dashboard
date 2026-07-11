<div class="space-y-6 text-gray-800 dark:text-white">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">Chart of Accounts</h1>
            <p class="text-gray-500 dark:text-gray-400">
                Daftar akun yang digunakan pada sistem.
            </p>
        </div>

        <button class="flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg">
            <x-heroicon-o-plus class="w-5 h-5" />
            Tambah Akun
        </button>

    </div>

    {{-- TABLE --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">

        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Cari kode atau nama akun..."
               class="w-full md:w-96 mb-5 px-4 py-2 rounded-lg border dark:bg-gray-900 text-gray-800 dark:text-white">

        <table class="w-full text-sm">

            <thead class="text-gray-500 border-b dark:border-gray-700">
                <tr>
                    <th class="py-3 px-4">Kode</th>
                    <th class="py-3 px-4">Nama</th>
                    <th class="py-3 px-4">Parent</th>
                    <th class="py-3 px-4">Tipe</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @foreach($accounts as $account)
                <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700/30">

                    <td class="py-3 px-4 font-mono text-gray-800 dark:text-white">
                        {{ $account->code }}
                    </td>

                    <td class="py-3 px-4">{{ $account->name }}</td>

                    <td class="py-3 px-4 text-gray-500">
                        {{ optional($account->parent)->name ?? '-' }}
                    </td>

                    <td class="py-3 px-4">
                        <span class="px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">
                            {{ ucfirst($account->type) }}
                        </span>
                    </td>

                    <td class="py-3 px-4">
                        <div class="flex justify-center gap-2">

                            <button class="flex items-center gap-1 px-3 py-1 bg-blue-500 text-white rounded">
                                <x-heroicon-o-pencil class="w-4 h-4" />
                                Edit
                            </button>

                            <button class="flex items-center gap-1 px-3 py-1 bg-red-500 text-white rounded">
                                <x-heroicon-o-trash class="w-4 h-4" />
                                Hapus
                            </button>

                        </div>
                    </td>

                </tr>
                @endforeach

            </tbody>

        </table>

        <div class="mt-6">
            {{ $accounts->links() }}
        </div>

    </div>

</div>