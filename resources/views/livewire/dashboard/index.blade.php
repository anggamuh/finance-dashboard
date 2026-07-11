<div class="space-y-8">

    <div class="flex flex-col gap-2 mb-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white tracking-tight flex items-center gap-2">
            <x-heroicon-o-home class="w-7 h-7 text-emerald-500" />
            Dashboard
        </h1>
        <p class="text-gray-500 dark:text-gray-400 text-base">Selamat datang di Finance Dashboard <span
                class="align-middle">👋</span></p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <!-- Total -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-emerald-500" />
                <p class="text-sm text-gray-500 font-medium">Total Transaksi</p>
            </div>

            <h2 class="text-3xl font-bold mt-1 tracking-tight">
                {{ number_format($stats['total_transactions']) }}
            </h2>
        </div>

        <!-- Dari -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calendar class="w-6 h-6 text-blue-500" />
                <p class="text-sm text-gray-500 font-medium">Dari</p>
            </div>

            <h2 class="text-xl font-bold text-blue-600 mt-1 tracking-tight">
                {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : '-' }}
            </h2>
        </div>

        <!-- Sampai -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calendar class="w-6 h-6 text-orange-500" />
                <p class="text-sm text-gray-500 font-medium">Sampai</p>
            </div>

            <h2 class="text-xl font-bold text-orange-600 mt-2">
                {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : '-' }}
            </h2>
        </div>

        <!-- Lama Periode -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clock class="w-6 h-6 text-emerald-500" />
                <p class="text-sm text-gray-500 font-medium">Periode</p>
            </div>

            <h2 class="text-2xl font-bold text-emerald-600 mt-2">
                @if ($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->diffInDays($endDate) + 1 }} Hari
                @else
                    -
                @endif
            </h2>
        </div>

    </div>

    <!-- Chart Pengeluaran (full width, di luar grid statistik) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Pengeluaran Bulanan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">6 bulan terakhir</p>
            </div>

            <div class="flex items-center gap-4 flex-wrap">
                <input type="month" wire:model.live="selectedMonth"
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">

                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-4 py-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">vs bulan lalu</span>
                    <span class="font-bold text-sm {{ $expenseDiffPercent > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                        {{ $expenseDiffPercent > 0 ? '↑' : '↓' }} {{ abs($expenseDiffPercent) }}%
                    </span>
                </div>
            </div>
        </div>

        <div wire:ignore style="position: relative; height: 320px;">
            <canvas id="expenseChart"></canvas>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {

                const ctx = document.getElementById('expenseChart');

                let chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json(array_column($monthly, 'label')),
                        datasets: [{
                            label: 'Pengeluaran',
                            data: @json(array_column($monthly, 'total')),
                            backgroundColor: '#10B981',
                            borderRadius: 6,
                            maxBarThickness: 56,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => 'Rp' + Number(value).toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                });

                Livewire.on('monthly-expense-updated', ({
                    labels,
                    totals
                }) => {

                    chart.data.labels = [...labels];
                    chart.data.datasets[0].data = [...totals];

                    chart.update('none');
                });

            });
        </script>
    @endpush

</div>
