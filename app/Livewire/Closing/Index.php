<?php

namespace App\Livewire\Closing;

use App\Models\ClosingStock;
use App\Models\InternalOperationalCost;
use App\Models\Revenue;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    /**
     * Harga satuan stok, dipakai di rumus laba bersih.
     * (Stok Awal x HARGA_STOK) + Biaya Operasional Internal - Omzet - Biaya Operasional
     */
    const HARGA_STOK = 19500;

    public $year;

    public function mount()
    {
        $this->year = now()->year;
    }

    /**
     * Stok awal tahun berjalan, diambil dari opening_stock tahun sebelumnya.
     */
    public function getStockAwal()
    {
        return ClosingStock::where('year', $this->year - 1)
            ->value('opening_stock') ?? 28285;
    }

    public function getRows()
    {
        $rows = [];

        $previousStockEnd = $this->getStockAwal();

        for ($month = 1; $month <= 12; $month++) {

            $omzet = Revenue::whereYear('date_from', $this->year)
                ->whereMonth('date_from', $month)
                ->sum('amount');

            $internal = InternalOperationalCost::whereYear('transaction_date', $this->year)
                ->whereMonth('transaction_date', $month)
                ->sum('total');

            $coa = [
                '5311',
                '5313',
                '5314',
                '5315',
                '5317',
                '5320',
                '5322',
                '5399',
            ];

            $operasional = Transaction::query()
                ->whereNotIn('account_name', [
                    'Bank BCA',
                    'Petty Cash',
                ])
                ->where(function ($q) {
                    $q->whereNull('description')
                        ->orWhere('description', 'not like', 'Saldo per%');
                })
                ->whereYear('transaction_date', $this->year)
                ->whereMonth('transaction_date', $month)
                ->where('debit', '>', 0)
                ->where(function ($q) use ($coa) {

                    foreach ($coa as $code) {
                        $q->orWhereRaw('LEFT(account_code,4)=?', [$code]);
                    }

                })
                ->sum('debit');

            $stockIn = InternalOperationalCost::whereYear(
                'transaction_date',
                $this->year
            )
                ->whereMonth('transaction_date', $month)
                ->sum('qty');

            $stockOut = Revenue::whereYear(
                'date_from',
                $this->year
            )
                ->whereMonth('date_from', $month)
                ->sum('qty');

            $stockStart = $previousStockEnd;

            $stockEnd = $stockStart + $stockIn - $stockOut;

            $rows[] = [

                'month' => Carbon::create()->month($month)->translatedFormat('F'),

                'omzet' => $omzet,

                'internal' => $internal,

                'operasional' => $operasional,

                'stock_start' => $stockStart,

                'stock_in' => $stockIn,

                'stock_out' => $stockOut,

                'stock_end' => $stockEnd,

            ];

            $previousStockEnd = $stockEnd;
        }

        return collect($rows);
    }

    /**
     * Hitung laba bersih sesuai rumus (3 langkah):
     *
     * Langkah 1: Hasil1 = (Stok Awal x HARGA_STOK) + Biaya Operasional Internal
     *                     - (Stok Akhir Tahun x HARGA_STOK)
     * Langkah 2: Hasil2 = Omzet - Hasil1
     * Langkah 3: Laba   = Hasil2 - Biaya Operasional (Accurate)
     *
     * Mengembalikan array berisi setiap langkah supaya bisa ditampilkan
     * rinciannya di view, bukan cuma angka akhir.
     */
    public function calculateProfit($stockAwal, $stockEnd, $totalInternal, $totalOmzet, $totalOperational)
    {
        $stockAwalRupiah = $stockAwal * self::HARGA_STOK;

        $stockEndRupiah = $stockEnd * self::HARGA_STOK;

        $hasil1 = $stockAwalRupiah + $totalInternal - $stockEndRupiah;

        $hasil2 = $totalOmzet - $hasil1;

        $laba = $hasil2 - $totalOperational;

        return [
            'stock_awal_rupiah' => $stockAwalRupiah,
            'stock_end_rupiah' => $stockEndRupiah,
            'hasil1' => $hasil1,
            'hasil2' => $hasil2,
            'laba' => $laba,
        ];
    }

    public function exportPdf()
    {
        $rows = $this->getRows();

        $stockAwal = $this->getStockAwal();
        $stockEnd = optional($rows->last())['stock_end'] ?? 0;
        $totalOmzet = $rows->sum('omzet');
        $totalInternal = $rows->sum('internal');
        $totalOperational = $rows->sum('operasional');

        $profitCalc = $this->calculateProfit($stockAwal, $stockEnd, $totalInternal, $totalOmzet, $totalOperational);

        $data = [
            'rows' => $rows,
            'year' => $this->year,
            'stockAwal' => $stockAwal,
            'totalOmzet' => $totalOmzet,
            'totalInternal' => $totalInternal,
            'totalOperational' => $totalOperational,
            'stockAwalRupiah' => $profitCalc['stock_awal_rupiah'],
            'stockEndRupiah' => $profitCalc['stock_end_rupiah'],
            'hasil1' => $profitCalc['hasil1'],
            'hasil2' => $profitCalc['hasil2'],
            'profit' => $profitCalc['laba'],
            'stockEnd' => $stockEnd,
            'generatedAt' => now('Asia/Jakarta')->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('livewire.closing.export-pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'closing-tahunan-'.$this->year.'.pdf';

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $filename
        );
    }

    public function render()
    {
        $rows = $this->getRows();

        $stockAwal = $this->getStockAwal();
        $stockEnd = optional($rows->last())['stock_end'] ?? 0;
        $totalOmzet = $rows->sum('omzet');
        $totalInternal = $rows->sum('internal');
        $totalOperational = $rows->sum('operasional');

        $profitCalc = $this->calculateProfit($stockAwal, $stockEnd, $totalInternal, $totalOmzet, $totalOperational);

        return view('livewire.closing.index', [

            'rows' => $rows,

            'stockAwal' => $stockAwal,

            'totalOmzet' => $totalOmzet,

            'totalInternal' => $totalInternal,

            'totalOperational' => $totalOperational,

            'stockAwalRupiah' => $profitCalc['stock_awal_rupiah'],

            'stockEndRupiah' => $profitCalc['stock_end_rupiah'],

            'hasil1' => $profitCalc['hasil1'],

            'hasil2' => $profitCalc['hasil2'],

            'profit' => $profitCalc['laba'],

            'stockEnd' => $stockEnd,

        ])->layout('layouts.app');
    }
}