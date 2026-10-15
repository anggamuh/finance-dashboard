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

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;

        $this->loadOpeningBalance();
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER UPDATE
    |--------------------------------------------------------------------------
    */

    public function updatedMonth()
    {
        $this->loadOpeningBalance();
    }

    public function updatedYear()
    {
        $this->loadOpeningBalance();
    }

    /*
    |--------------------------------------------------------------------------
    | SALDO AWAL
    |--------------------------------------------------------------------------
    */

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
            'year' => [
                'required',
                'integer',
                'between:2000,2100',
            ],

            'month' => [
                'required',
                'integer',
                'between:1,12',
            ],

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

    /*
    |--------------------------------------------------------------------------
    | ACCOUNT OMZET
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | ACCOUNT PEMBELIAN
    |--------------------------------------------------------------------------
    */

    protected function getPurchaseAccount(): ?Account
    {
        return Account::query()
            ->where('name', 'Pembelian Barang')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | OMZET
    |--------------------------------------------------------------------------
    |
    | Revenue -> Kas Masuk
    |
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
                    'unique_key' =>
                        'revenue-'.$revenue->id,

                    'source_type' =>
                        'revenue',

                    'source_id' =>
                        $revenue->id,

                    /*
                     * Revenue merupakan rekap periode,
                     * maka tanggal Buku Kas menggunakan date_to.
                     */
                    'date' =>
                        $revenue->date_to,

                    'sort_time' =>
                        $revenue->created_at?->format('H:i:s')
                        ?? '00:00:00',

                    'proof_number' =>
                        'REV-'.str_pad(
                            $revenue->id,
                            5,
                            '0',
                            STR_PAD_LEFT
                        ),

                    'description' =>
                        $description,

                    'account_code' =>
                        $revenueAccount?->code
                        ?? '1001',

                    'account_name' =>
                        $revenueAccount?->name
                        ?? 'Penerimaan hasil penjualan',

                    'cash_in' =>
                        (float) $revenue->amount,

                    'cash_out' =>
                        0,

                    'attachments' =>
                        collect(),
                ];
            });
    }

    /*
    |--------------------------------------------------------------------------
    | BIAYA OPERASIONAL
    |--------------------------------------------------------------------------
    |
    | Rule dibuat sama dengan Closing.
    |
    | Hanya transaksi debit dengan COA:
    | 5311, 5313, 5314, 5315, 5317, 5320, 5322, 5399
    |
    */

    protected function expenseRows(): Collection
    {
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

        return Transaction::query()
            ->with([
                'account',
                'attachments',
            ])

            /*
             * Jangan masukkan akun kas / bank
             * sebagai biaya operasional.
             */
            ->whereNotIn('account_name', [
                'Bank BCA',
                'Petty Cash',
            ])

            /*
             * Jangan masukkan transaksi saldo periode.
             */
            ->where(function ($query) {
                $query
                    ->whereNull('description')
                    ->orWhere(
                        'description',
                        'not like',
                        'Saldo per%'
                    );
            })

            /*
             * Filter tahun.
             */
            ->whereYear(
                'transaction_date',
                $this->year
            )

            /*
             * Filter bulan.
             */
            ->whereMonth(
                'transaction_date',
                $this->month
            )

            /*
             * Biaya operasional harus memiliki debit > 0.
             */
            ->where(
                'debit',
                '>',
                0
            )

            /*
             * Hanya COA operasional.
             */
            ->where(function ($query) use ($coa) {

                foreach ($coa as $code) {
                    $query->orWhereRaw(
                        'SUBSTR(account_code, 1, 4) = ?',
                        [$code]
                    );
                }
            })

            ->get()

            ->map(function ($transaction) {

                return [
                    'unique_key' =>
                        'transaction-'.$transaction->id,

                    'source_type' =>
                        'transaction',

                    'source_id' =>
                        $transaction->id,

                    'date' =>
                        $transaction->transaction_date,

                    'sort_time' =>
                        $transaction->created_at?->format('H:i:s')
                        ?? '00:00:00',

                    'proof_number' =>
                        $transaction->transaction_number
                        ?: 'TRX-'.str_pad(
                            $transaction->id,
                            5,
                            '0',
                            STR_PAD_LEFT
                        ),

                    'description' =>
                        $transaction->description
                        ?: $transaction->account_name
                        ?: 'Biaya Operasional',

                    'account_code' =>
                        $transaction->account_code,

                    'account_name' =>
                        $transaction->account?->name
                        ?? $transaction->account_name,

                    'cash_in' =>
                        0,

                    /*
                     * Pada sistem saat ini
                     * biaya operasional disimpan di debit.
                     */
                    'cash_out' =>
                        (float) $transaction->debit,

                    'attachments' =>
                        $transaction->attachments,
                ];
            });
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBELIAN BARANG
    |--------------------------------------------------------------------------
    |
    | InternalOperationalCost -> Kas Keluar
    |
    */

    protected function purchaseRows(): Collection
    {
        $purchaseAccount = $this->getPurchaseAccount();

        return InternalOperationalCost::query()
            ->whereYear(
                'transaction_date',
                $this->year
            )
            ->whereMonth(
                'transaction_date',
                $this->month
            )
            ->get()
            ->map(function ($purchase) use ($purchaseAccount) {

                $description =
                    $purchase->item_name
                    ?: 'Pembelian Barang';

                if ($purchase->supplier) {
                    $description .=
                        ' - '.$purchase->supplier;
                }

                return [
                    'unique_key' =>
                        'purchase-'.$purchase->id,

                    'source_type' =>
                        'purchase',

                    'source_id' =>
                        $purchase->id,

                    'date' =>
                        $purchase->transaction_date,

                    'sort_time' =>
                        $purchase->created_at?->format('H:i:s')
                        ?? '00:00:00',

                    'proof_number' =>
                        $purchase->invoice_number
                        ?: 'PB-'.str_pad(
                            $purchase->id,
                            5,
                            '0',
                            STR_PAD_LEFT
                        ),

                    'description' =>
                        $description,

                    'account_code' =>
                        $purchaseAccount?->code
                        ?? '-',

                    'account_name' =>
                        $purchaseAccount?->name
                        ?? 'Pembelian Barang',

                    'cash_in' =>
                        0,

                    'cash_out' =>
                        (float) $purchase->total,

                    'attachments' =>
                        collect(),
                ];
            });
    }

    /*
    |--------------------------------------------------------------------------
    | SELURUH TRANSAKSI PERIODE
    |--------------------------------------------------------------------------
    |
    | Gabungkan:
    | - Omzet
    | - Biaya Operasional
    | - Pembelian Barang
    |
    | Saldo berjalan dihitung SEBELUM search.
    |
    */

    protected function periodRows(): Collection
    {
        $rows = $this->revenueRows()
            ->concat(
                $this->expenseRows()
            )
            ->concat(
                $this->purchaseRows()
            )
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

        $runningBalance =
            (float) $this->openingBalance;

        return $rows->map(
            function ($row) use (&$runningBalance) {

                $runningBalance +=
                    (float) $row['cash_in'];

                $runningBalance -=
                    (float) $row['cash_out'];

                $row['balance'] =
                    $runningBalance;

                return $row;
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    |
    | Search hanya memfilter tampilan tabel.
    | Tidak mengubah summary dan saldo periode.
    |
    */

    protected function filteredRows(
        Collection $rows
    ): Collection {
        $keyword =
            trim($this->search);

        if ($keyword === '') {
            return $rows;
        }

        $keyword =
            mb_strtolower($keyword);

        return $rows
            ->filter(function ($row) use ($keyword) {

                return str_contains(
                    mb_strtolower(
                        (string) $row['proof_number']
                    ),
                    $keyword
                )
                    || str_contains(
                        mb_strtolower(
                            (string) $row['description']
                        ),
                        $keyword
                    )
                    || str_contains(
                        mb_strtolower(
                            (string) $row['account_code']
                        ),
                        $keyword
                    )
                    || str_contains(
                        mb_strtolower(
                            (string) $row['account_name']
                        ),
                        $keyword
                    )
                    || str_contains(
                        mb_strtolower(
                            (string) $row['source_type']
                        ),
                        $keyword
                    );
            })
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        /*
         * Semua transaksi bulan terpilih.
         */
        $periodRows =
            $this->periodRows();

        /*
         * Search hanya mempengaruhi tabel.
         */
        $rows =
            $this->filteredRows(
                $periodRows
            );

        /*
        |--------------------------------------------------------------------------
        | TOTAL OMZET
        |--------------------------------------------------------------------------
        */

        $totalRevenue =
            (float) $periodRows
                ->where(
                    'source_type',
                    'revenue'
                )
                ->sum('cash_in');

        /*
        |--------------------------------------------------------------------------
        | TOTAL BIAYA OPERASIONAL
        |--------------------------------------------------------------------------
        */

        $totalExpense =
            (float) $periodRows
                ->where(
                    'source_type',
                    'transaction'
                )
                ->sum('cash_out');

        /*
        |--------------------------------------------------------------------------
        | TOTAL PEMBELIAN BARANG
        |--------------------------------------------------------------------------
        */

        $totalPurchase =
            (float) $periodRows
                ->where(
                    'source_type',
                    'purchase'
                )
                ->sum('cash_out');

        /*
        |--------------------------------------------------------------------------
        | KAS MASUK
        |--------------------------------------------------------------------------
        |
        | Kas Masuk = Omzet
        |
        */

        $totalCashIn =
            $totalRevenue;

        /*
        |--------------------------------------------------------------------------
        | KAS KELUAR
        |--------------------------------------------------------------------------
        |
        | Kas Keluar =
        | Biaya Operasional
        | +
        | Pembelian Barang
        |
        */

        $totalCashOut =
            $totalExpense
            + $totalPurchase;

        /*
        |--------------------------------------------------------------------------
        | SALDO AKHIR
        |--------------------------------------------------------------------------
        |
        | Saldo Akhir =
        | Saldo Awal
        | + Kas Masuk
        | - Kas Keluar
        |
        */

        $endingBalance =
            (float) $this->openingBalance
            + $totalCashIn
            - $totalCashOut;

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'livewire.cash-book.index',
            [
                'rows' =>
                    $rows,

                'totalCashIn' =>
                    $totalCashIn,

                'totalCashOut' =>
                    $totalCashOut,

                'endingBalance' =>
                    $endingBalance,

                'totalRevenue' =>
                    $totalRevenue,

                'totalExpense' =>
                    $totalExpense,

                'totalPurchase' =>
                    $totalPurchase,

                'totalTransactions' =>
                    $periodRows->count(),

                'displayedTransactions' =>
                    $rows->count(),
            ]
        )->layout('layouts.app');
    }
}