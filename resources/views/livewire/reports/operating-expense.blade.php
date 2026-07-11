<div class="space-y-8">

    <!-- Header -->
    <div class="flex flex-col gap-2 mb-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-tight flex items-center gap-2">
            <x-heroicon-o-document-text class="w-7 h-7 text-emerald-500" />
            Laporan Beban Operasional
        </h1>
        <p class="text-gray-500 dark:text-gray-400 text-base">
            Ringkasan beban operasional perusahaan
        </p>
    </div>

    <!-- Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">

        <!-- Filter -->
        <div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-6">

            <div class="flex items-center gap-2">
              
                <input type="date"
                    wire:model.live="dateFrom"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                    focus:ring-2 focus:ring-emerald-500
                    dark:bg-gray-700 dark:text-white">
            </div>

            <div class="flex items-center gap-2">
                

                <input type="date"
                    wire:model.live="dateTo"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                    focus:ring-2 focus:ring-emerald-500
                    dark:bg-gray-700 dark:text-white">
            </div>

            <a href="{{ route('reports.operating-expense.pdf', [
                'from' => $dateFrom,
                'to' => $dateTo,
            ]) }}"
                target="_blank"
                class="inline-flex items-center justify-center gap-2
                bg-red-600 hover:bg-red-700
                text-white px-4 py-2 rounded-lg font-medium shadow-sm transition">

                <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                Export PDF
            </a>

        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">
                            Kode
                        </th>

                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">
                            Nama Akun
                        </th>

                        <th class="text-right py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">
                            Total
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($expenses as $expense)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">

                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300 font-mono">
                                {{ $expense->account_code }}
                            </td>

                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                {{ $expense->account_name }}
                            </td>

                            <td class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-white">
                                Rp{{ number_format($expense->total, 0, ',', '.') }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="border-t border-gray-200 dark:border-gray-700 font-bold text-base">

                        <td colspan="2" class="pt-5 px-4 text-gray-800 dark:text-white">
                            Total Beban Operasional
                        </td>

                        <td class="text-right pt-5 px-4 text-gray-900 dark:text-white">
                            Rp{{ number_format($grandTotal, 0, ',', '.') }}
                        </td>

                    </tr>
                </tfoot>

            </table>
        </div>

    </div>
</div>