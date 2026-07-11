<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Upload Accurate
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Import data CSV ke sistem
            </p>
        </div>

        <button 
            wire:click="downloadTemplate"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium shadow-sm transition"
        >
            <!-- Heroicon: download -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l4-4m-4 4l-4-4M4 20h16" />
            </svg>
            Download Template
        </button>
    </div>

    <!-- Success Messages -->
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 dark:bg-green-900/20 px-5 py-4 flex gap-3 items-start">
            <!-- Heroicon: check -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm text-green-700 dark:text-green-200">
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if($uploadMessage)
        <div class="rounded-xl border border-green-200 bg-green-50 dark:bg-green-900/20 px-5 py-4 flex gap-3 items-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm text-green-700 dark:text-green-200">
                {{ $uploadMessage }}
            </span>
        </div>
    @endif

    @if($uploadError)
        <div class="rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/20 px-5 py-4 flex gap-3 items-start">
            <!-- Heroicon: error -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span class="text-sm text-red-700 dark:text-red-200">
                {{ $uploadError }}
            </span>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="p-6 sm:p-8 space-y-6">

            <!-- Format Info -->
            <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-5">
                <div class="flex items-start gap-3">
                    <!-- Heroicon: info -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>

                    <div>
                        <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">
                            📋 Format CSV yang dibutuhkan:
                        </p>
                        <ul class="text-sm text-blue-800 dark:text-blue-200 list-disc list-inside space-y-1">
                            <li><strong>Kolom:</strong> account_code, account_name, transaction_number, transaction_date, transaction_type, description, debit, credit</li>
                            <li><strong>Tanggal:</strong> Format DD/MM/YYYY (contoh: 30/06/2026)</li>
                            <li><strong>Jumlah:</strong> Gunakan koma atau titik untuk desimal (contoh: 1000000 atau 1.000.000,50)</li>
                            <li><strong>Header:</strong> Baris pertama adalah header (akan di-skip otomatis)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="importCsv" enctype="multipart/form-data" class="space-y-5">
                
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih file CSV
                    </label>

                    <div class="relative">
                        <input 
                            type="file" 
                            wire:model="file" 
                            accept=".csv,.txt"
                            class="block w-full text-sm text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >

                        @if($file)
                            <div class="mt-2 flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                                <!-- Heroicon: check -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                File: {{ $file->getClientOriginalName() }} ({{ number_format($file->getSize() / 1024, 2) }} KB)
                            </div>
                        @endif
                    </div>

                    @error('file')
                        <p class="text-sm text-red-500 mt-2 flex items-center gap-2">
                            <!-- Heroicon: error -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    wire:target="importCsv" 
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-400 text-white font-semibold rounded-lg px-6 py-3 transition shadow-sm"
                >
                    <!-- Loading State -->
                    <span wire:loading wire:target="importCsv">
                        ⏳ Sedang mengupload...
                    </span>

                    <!-- Default State -->
                    <span wire:loading.remove wire:target="importCsv" class="inline-flex items-center gap-2">
                        <!-- Heroicon: upload -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12V4m0 0l-4 4m4-4l4 4" />
                        </svg>
                        Upload CSV
                    </span>
                </button>

            </form>

        </div>
    </div>

</div>