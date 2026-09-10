<div class="space-y-6 text-gray-800 dark:text-white">
    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold sm:text-3xl">Master Akun</h1>
            <p class="text-gray-500 dark:text-gray-400">
                Daftar akun yang digunakan pada sistem.
            </p>
        </div>
        <button type="button" wire:click="openCreateModal" class="app-btn-primary self-start sm:self-auto">
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
        <div class="overflow-x-auto">
        <table class="min-w-[720px] w-full text-sm">
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
                @forelse($accounts as $account)
                <tr wire:key="account-{{ $account->id }}" class="border-b hover:bg-gray-50 dark:hover:bg-gray-700/30">
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
                            <button wire:click="edit({{ $account->id }})" class="flex items-center gap-1 px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded transition-colors">
                                <x-heroicon-o-pencil class="w-4 h-4" />
                                Edit
                            </button>
                            <button type="button"
                                x-on:click="
                                    Swal.fire({
                                        title: 'Yakin hapus akun ini?',
                                        text: 'Data tidak bisa dikembalikan.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, hapus',
                                        cancelButtonText: 'Batal',
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.delete({{ $account->id }})
                                        }
                                    })
                                "
                                class="flex items-center gap-1 px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded transition-colors">
                                <x-heroicon-o-trash class="w-4 h-4" />
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-500 dark:text-gray-400">
                        Belum ada akun. Klik "Tambah Akun" untuk membuat yang pertama.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="mt-6">
            {{ $accounts->links() }}
        </div>
    </div>

    {{-- MODAL: Tambah / Edit Akun --}}
    @if ($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:key="account-modal-{{ $editingId ?? 'new' }}">

            <div class="absolute inset-0 bg-black/50" wire:click="closeModal"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg p-6 border border-gray-100 dark:border-gray-700 max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Akun' : 'Tambah Akun' }}
                    </h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" aria-label="Tutup">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 001.06 1.06L10 11.06l3.72 3.72a.75.75 0 001.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-5">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="account-code" class="text-sm font-medium text-gray-700 dark:text-gray-300">Kode Akun</label>
                            <input id="account-code" type="text" wire:model="code" placeholder="Contoh: 5311"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                            @error('code') <small class="text-red-500">{{ $message }}</small> @enderror
                        </div>

                        <div>
                            <label for="account-type" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tipe</label>
                            <select id="account-type" wire:model="type"
                                class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Pilih tipe</option>
                                <option value="asset">Aset</option>
                                <option value="liability">Kewajiban</option>
                                <option value="equity">Modal</option>
                                <option value="income">Pendapatan</option>
                                <option value="expense">Beban</option>
                            </select>
                            @error('type') <small class="text-red-500">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="account-name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Akun</label>
                        <input id="account-name" type="text" wire:model="name" placeholder="Contoh: Gaji Karyawan"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                        @error('name') <small class="text-red-500">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label for="account-parent" class="text-sm font-medium text-gray-700 dark:text-gray-300">Parent Akun (opsional)</label>
                        <select id="account-parent" wire:model="parent_id"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Tidak ada (akun utama)</option>
                            @foreach($parentOptions as $option)
                                <option value="{{ $option->id }}">{{ $option->code }} — {{ $option->name }}</option>
                            @endforeach
                        </select>
                        @error('parent_id') <small class="text-red-500">{{ $message }}</small> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="app-btn-secondary">
                            Batal
                        </button>
                        <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                            class="app-btn-primary">
                            <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" />
                                <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                            </svg>
                            {{ $editingId ? 'Update' : 'Simpan' }}
                        </button>
                    </div>

                </div>

            </div>

        </div>
    @endif

</div>
