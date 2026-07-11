<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-tight flex items-center gap-2">
                Buku Besar Accurate
            </h1>
            <p class="text-gray-400 mt-1">
                Simpan file hasil export/import Accurate agar mudah diakses kembali.
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-emerald-100 border border-emerald-300 text-emerald-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- List File --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">

        <div class="border-b px-4 sm:px-6 py-4">
            <h2 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2 text-sm sm:text-base">
                <x-heroicon-o-folder class="w-5 h-5 text-blue-600" />
                Daftar File
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">
                            File Import
                        </th>
                        <th class="hidden sm:table-cell px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">
                            Tanggal Upload
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($files as $file)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-o-document-text class="w-5 h-5 sm:w-6 sm:h-6 text-blue-500" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white truncate text-sm sm:text-base">
                                            {{ $file->file_name }}
                                        </p>
                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 sm:hidden">
                                            {{ $file->created_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell px-4 sm:px-6 py-4 text-gray-600 dark:text-gray-400 text-sm">
                                {{ $file->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-center">
                                <button type="button"
                                    onclick="Swal.fire({
                                        title: 'Yakin?',
                                        text: 'Yakin ingin menghapus import ini?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Ya, hapus'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            @this.call('delete', {{ $file->id }});
                                        }
                                    })"
                                    class="inline-flex items-center gap-1 px-3 py-2 sm:px-4 sm:py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium transition text-sm">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                    <span class="hidden sm:inline">Hapus</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-gray-400 text-sm sm:text-base">
                                Belum ada riwayat import.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $files->links() }}
        </div>
    </div>

</div>
