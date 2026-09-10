<?php

namespace App\Livewire\CashBook;

use App\Models\Account;
use App\Models\CashOpeningBalance;
use App\Models\InternalOperationalCost;
use App\Models\Revenue;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public $month;

    public $year;

    public $search = '';

    public $openingBalance = 0;

    public $showOpeningBalanceModal = false;

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;

        $this->loadOpeningBalance();
    }

    public function updatedMonth()
    {
        $this->loadOpeningBalance();
    }

    public function updatedYear()
    {
        $this->loadOpeningBalance();
    }

    public function loadOpeningBalance()
    {
        $balance = CashOpeningBalance::query()
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->first();

        $this->openingBalance = (float) ($balance?->amount ?? 0);
    }

    public function openOpeningBalanceModal()
    {
        $this->loadOpeningBalance();

        $this->showOpeningBalanceModal = true;
    }

    public function closeOpeningBalanceModal()
    {
        $this->showOpeningBalanceModal = false;

        $this->resetErrorBag();
    }

    public function saveOpeningBalance()
    {
        $this->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'openingBalance' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        CashOpeningBalance::updateOrCreate(
            [
                'year' => $this->year,
                'month' => $this->month,
            ],
            [
                'amount' => $this->openingBalance,
            ]
        );

        $this->showOpeningBalanceModal = false;

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Saldo awal berhasil disimpan.',
        ]);
    }

    /**
     * Akun untuk omzet.
     */
    protected function getRevenueAccount(): ?Account
    {
        return Account::query()
            ->where('code', '1001')
            ->first()
            ?? Account::query()
                ->where('type', 'income')
                ->orderBy('code')
                ->first();
    }

    /**
     * Jika suatu saat Master Akun sudah memiliki
     * akun bernama "Pembelian Barang", otomatis gunakan.
     *
     * Kalau belum ada, tetap tampil sebagai Pembelian Barang
     * tanpa membuat kode akun palsu.
     */
    protected function getPurchaseAccount(): ?Account
    {
        return Account::query()
            ->where('name', 'Pembelian Barang')
            ->first();
    }

    /**
     * OMZET
     * Revenue -> Kas Masuk
     */
    protected function revenueRows(): Collection
    {
        $revenueAccount = $this->getRevenueAccount();

        return Revenue::query()
            ->whereYear('date_to', $this->year)
            ->whereMonth('date_to', $this->month)
            ->get()
            ->map(function ($revenue) use ($revenueAccount) {

                $description = $revenue->notes;

                if (! $description) {
                    $description =
                        'Omzet '
                        .$revenue->date_from->format('d/m/Y')
                        .' - '
                        .$revenue->date_to->format('d/m/Y');
                }

                return [
                    'unique_key' => 'revenue-'.$revenue->id,

                    'source_type' => 'revenue',
                    'source_id' => $revenue->id,

                    /*
                     * Revenue merupakan rekap periode,
                     * sehingga tanggal Buku Kas menggunakan date_to.
                     */
                    'date' => $revenue->date_to,

                    'sort_time' => $revenue->created_at?->format('H:i:s') ?? '00:00:00',

                    'proof_number' => 'REV-'.str_pad(
                        $revenue->id,
                        5,
                        '0',
                        STR_PAD_LEFT
                    ),

                    'description' => $description,

                    'account_code' => $revenueAccount?->code ?? '1001',

                    'account_name' => $revenueAccount?->name
                        ?? 'Penerimaan hasil penjualan',

                    'cash_in' => (float) $revenue->amount,

                    'cash_out' => 0,

                    'attachments' => collect(),
                ];
            });
    }

    /**
     * PENGELUARAN
     * Transaction type expense -> Kas Keluar
     */
    protected function expenseRows(): Collection
    {
        return Transaction::query()
            ->with([
                'account',
                'attachments',
            ])
            ->where('transaction_type', 'expense')
            ->whereYear('transaction_date', $this->year)
            ->whereMonth('transaction_date', $this->month)
            ->get()
            ->map(function ($transaction) {

                return [
                    'unique_key' => 'transaction-'.$transaction->id,

                    'source_type' => 'transaction',
                    'source_id' => $transaction->id,

                    'date' => $transaction->transaction_date,

                    'sort_time' => $transaction->created_at?->format('H:i:s') ?? '00:00:00',

                    'proof_number' => $transaction->transaction_number
                        ?: 'TRX-'.str_pad(
                            $transaction->id,
                            5,
                            '0',
                            STR_PAD_LEFT
                        ),

                    'description' => $transaction->description
                        ?: $transaction->account_name
                        ?: 'Pengeluaran',

                    'account_code' => $transaction->account_code,

                    'account_name' => $transaction->account?->name
                        ?? $transaction->account_name,

                    /*
                     * Pada sistem sekarang expense disimpan
                     * pada kolom debit.
                     */
                    'cash_in' => 0,

                    'cash_out' => (float) $transaction->debit,

                    'attachments' => $transaction->attachments,
                ];
            });
    }

    /**
     * PEMBELIAN BARANG
     * InternalOperationalCost -> Kas Keluar
     */
    protected function purchaseRows(): Collection
    {
        $purchaseAccount = $this->getPurchaseAccount();

        return InternalOperationalCost::query()
            ->whereYear('transaction_date', $this->year)
            ->whereMonth('transaction_date', $this->month)
            ->get()
            ->map(function ($purchase) use ($purchaseAccount) {

                $description = $purchase->item_name ?: 'Pembelian Barang';

                if ($purchase->supplier) {
                    $description .= ' - '.$purchase->supplier;
                }

                return [
                    'unique_key' => 'purchase-'.$purchase->id,

                    'source_type' => 'purchase',
                    'source_id' => $purchase->id,

                    'date' => $purchase->transaction_date,

                    'sort_time' => $purchase->created_at?->format('H:i:s') ?? '00:00:00',

                    /*
                     * Gunakan nomor invoice asli apabila ada.
                     * Jika kosong, buat referensi PB-xxxxx.
                     */
                    'proof_number' => $purchase->invoice_number
                        ?: 'PB-'.str_pad(
                            $purchase->id,
                            5,
                            '0',
                            STR_PAD_LEFT
                        ),

                    'description' => $description,

                    /*
                     * Kalau Master Akun sudah memiliki
                     * "Pembelian Barang", pakai kode akun tersebut.
                     *
                     * Jika belum, jangan membuat kode akuntansi palsu.
                     */
                    'account_code' => $purchaseAccount?->code ?? '-',

                    'account_name' => $purchaseAccount?->name
                        ?? 'Pembelian Barang',

                    'cash_in' => 0,

                    'cash_out' => (float) $purchase->total,

                    'attachments' => collect(),
                ];
            });
    }

    /**
     * Gabungkan seluruh transaksi pada periode.
     *
     * Di tahap ini saldo berjalan dihitung SEBELUM search.
     * Jadi ketika user mencari transaksi tertentu,
     * saldo baris tidak berubah atau dihitung ulang secara keliru.
     */
    protected function periodRows(): Collection
    {
        $rows = $this->revenueRows()
            ->concat($this->expenseRows())
            ->concat($this->purchaseRows())
            ->sortBy(function ($row) {
                return sprintf(
                    '%s-%s-%s-%010d',
                    $row['date']->format('Y-m-d'),
                    $row['sort_time'],
                    $row['source_type'],
                    $row['source_id']
                );
            })
            ->values();

        $runningBalance = (float) $this->openingBalance;

        return $rows->map(function ($row) use (&$runningBalance) {

            $runningBalance += (float) $row['cash_in'];
            $runningBalance -= (float) $row['cash_out'];

            $row['balance'] = $runningBalance;

            return $row;
        });
    }

    /**
     * Search hanya untuk tampilan tabel.
     */
    protected function filteredRows(Collection $rows): Collection
    {
        $keyword = trim($this->search);

        if ($keyword === '') {
            return $rows;
        }

        $keyword = mb_strtolower($keyword);

        return $rows
            ->filter(function ($row) use ($keyword) {

                return str_contains(
                    mb_strtolower((string) $row['proof_number']),
                    $keyword
                )
                    || str_contains(
                        mb_strtolower((string) $row['description']),
                        $keyword
                    )
                    || str_contains(
                        mb_strtolower((string) $row['account_code']),
                        $keyword
                    )
                    || str_contains(
                        mb_strtolower((string) $row['account_name']),
                        $keyword
                    )
                    || str_contains(
                        mb_strtolower((string) $row['source_type']),
                        $keyword
                    );
            })
            ->values();
    }

    public function render()
    {
        /*
         * Seluruh transaksi bulan terpilih.
         * Digunakan untuk card summary.
         */
        $periodRows = $this->periodRows();

        /*
         * Search hanya mempengaruhi tabel.
         */
        $rows = $this->filteredRows($periodRows);

        $totalCashIn = (float) $periodRows->sum('cash_in');

        $totalCashOut = (float) $periodRows->sum('cash_out');

        $endingBalance =
            (float) $this->openingBalance
            + $totalCashIn
            - $totalCashOut;

        $totalRevenue = (float) $periodRows
            ->where('source_type', 'revenue')
            ->sum('cash_in');

        $totalExpense = (float) $periodRows
            ->where('source_type', 'transaction')
            ->sum('cash_out');

        $totalPurchase = (float) $periodRows
            ->where('source_type', 'purchase')
            ->sum('cash_out');

        return view('livewire.cash-book.index', [
            'rows' => $rows,

            'totalCashIn' => $totalCashIn,
            'totalCashOut' => $totalCashOut,
            'endingBalance' => $endingBalance,

            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'totalPurchase' => $totalPurchase,

            'totalTransactions' => $periodRows->count(),
            'displayedTransactions' => $rows->count(),
        ])->layout('layouts.app');
    }
}
