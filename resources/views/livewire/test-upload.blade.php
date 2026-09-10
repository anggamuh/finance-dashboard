<div class="mx-auto max-w-xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Uji Upload</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Validasi file tanpa menyimpannya secara permanen.</p>
    </div>

    <form wire:submit="save" class="app-card space-y-4 p-6">
        <label for="test-upload-file" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Pilih file</label>
        <input id="test-upload-file" type="file" wire:model="file" class="app-input block w-full">
        @error('file') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

        <button type="submit" class="app-btn-primary" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">Validasi File</span>
            <span wire:loading wire:target="save">Memvalidasi...</span>
        </button>
    </form>
</div>
