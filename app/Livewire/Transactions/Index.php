<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $dateFrom = '';

    public $dateTo = '';

    // ============================================================
    // SELECT ALL
    // ============================================================

    public array $selected = [];

    public bool $selectAll = false;

    public bool $showTrash = false;

    /**
     * Session filter
     */
    protected string $sessionKey = 'transactions.index.filters';

    /**
     * COA prefix
     *
     * Contoh:
     * 531304 -> prefix 5313
     * 532236 -> prefix 5322
     */
    protected array $coaCodes = [
        '5311',
        '5313',
        '5314',
        '5315',
        '5317',
        '5320',
        '5322',
        '5399',
    ];

    // ============================================================
    // PASTE IMPORT
    // ============================================================

    public bool $showPasteModal = false;

    public string $pasteData = '';

    public array $pastePreview = [];

    public ?string $pastePeriodStart = null;

    public ?string $pastePeriodEnd = null;

    public int $pasteAdjusted = 0;

    // ============================================================
    // MOUNT
    // ============================================================

    public function mount()
    {
        $filters = session($this->sessionKey, []);

        $this->search = $filters['search'] ?? '';

        $this->perPage = $filters['perPage'] ?? 10;

        $this->dateFrom = $filters['dateFrom']
            ?? now()->startOfMonth()->format('Y-m-d');

        $this->dateTo = $filters['dateTo']
            ?? now()->endOfMonth()->format('Y-m-d');

        $this->showTrash = $filters['showTrash'] ?? false;
    }

    // ============================================================
    // QUERY TRANSAKSI
    // ============================================================

    /**
     * Query utama transaksi.
     *
     * Semua tempat yang membutuhkan daftar transaksi:
     * - render()
     * - Select All
     * - cek jumlah transaksi
     *
     * menggunakan query yang sama.
     */
    protected function transactionQuery()
    {
        $query = Transaction::query();

        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        return $query
            ->whereRaw(
                'SUBSTR(account_code, 1, 4) IN ('.
                implode(
                    ',',
                    array_fill(
                        0,
                        count($this->coaCodes),
                        '?'
                    )
                ).
                ')',
                $this->coaCodes
            )
            ->where(function ($q) {
                $q->whereNull('description')
                    ->orWhere(
                        'description',
                        'not like',
                        'Saldo per%'
                    );
            })
            ->when(
                $this->search,
                function ($query) {
                    $query->where(function ($q) {

                        $q->where(
                            'account_name',
                            'like',
                            "%{$this->search}%"
                        )
                            ->orWhere(
                                'transaction_number',
                                'like',
                                "%{$this->search}%"
                            )
                            ->orWhere(
                                'description',
                                'like',
                                "%{$this->search}%"
                            );

                    });
                }
            )
            ->when(
                $this->dateFrom,
                function ($query) {
                    $query->whereDate(
                        'transaction_date',
                        '>=',
                        $this->dateFrom
                    );
                }
            )
            ->when(
                $this->dateTo,
                function ($query) {
                    $query->whereDate(
                        'transaction_date',
                        '<=',
                        $this->dateTo
                    );
                }
            );
    }

    // ============================================================
    // FILTER
    // ============================================================

    public function updating($property)
    {
        if (in_array($property, [
            'search',
            'perPage',
            'dateFrom',
            'dateTo',
            'showTrash',
        ])) {

            $this->resetPage();

            /*
             * Kalau filter berubah, pilihan lama jangan dibawa.
             * Karena transaksi yang terpilih sebelumnya bisa sudah
             * tidak termasuk dalam filter baru.
             */
            $this->selected = [];

            $this->selectAll = false;
        }
    }

    public function updated($property)
    {
        if (in_array($property, [
            'search',
            'perPage',
            'dateFrom',
            'dateTo',
            'showTrash',
        ])) {

            $this->saveFiltersToSession();
        }

    }

    protected function saveFiltersToSession()
    {
        session([
            $this->sessionKey => [
                'search' => $this->search,
                'perPage' => $this->perPage,
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
                'showTrash' => $this->showTrash,
            ],
        ]);
    }

    // ============================================================
    // SELECT ALL
    // ============================================================

    /**
     * Klik checkbox paling atas.
     *
     * TRUE:
     * semua transaksi yang sedang sesuai filter dipilih.
     *
     * FALSE:
     * semua pilihan dikosongkan.
     */
    public function updatedSelectAll($value)
    {
        if (! $value) {

            $this->selected = [];

            return;
        }

        $this->selected = $this->transactionQuery()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
    }

    /**
     * Ketika checkbox per transaksi berubah.
     *
     * Kalau semua transaksi hasil filter sudah terpilih,
     * checkbox Select All otomatis aktif.
     *
     * Kalau ada satu saja yang tidak terpilih,
     * Select All otomatis mati.
     */
    public function updatedSelected()
    {
        $totalFiltered = $this->transactionQuery()
            ->count();

        $selectedCount = count($this->selected);

        $this->selectAll =
            $totalFiltered > 0 &&
            $selectedCount === $totalFiltered;
    }

    // ============================================================
    // DELETE
    // ============================================================

    #[On('delete')]
    public function delete($id = null)
    {
        if (! $id) {
            return;
        }

        $transaction = Transaction::find($id);

        if (! $transaction) {

            session()->flash(
                'error',
                'Transaksi tidak ditemukan.'
            );

            return;
        }

        $transaction->delete();

        /*
         * Kalau transaksi yang dihapus sedang terpilih,
         * keluarkan dari selected.
         */
        $this->selected = array_values(
            array_diff(
                $this->selected,
                [(string) $id, $id]
            )
        );

        $this->selectAll = false;

        session()->flash(
            'success',
            'Transaksi berhasil dihapus.'
        );
    }

    // ============================================================
    // BULK DELETE
    // ============================================================

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        Transaction::whereIn(
            'id',
            $this->selected
        )->delete();

        $this->selected = [];

        $this->selectAll = false;

        session()->flash(
            'success',
            'Transaksi berhasil dihapus.'
        );
    }

    // ============================================================
    // RESTORE
    // ============================================================

    public function restore($id)
    {
        Transaction::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        session()->flash(
            'success',
            'Transaksi berhasil direstore.'
        );
    }

    // ============================================================
    // RESTORE SELECTED
    // ============================================================

    public function restoreSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        Transaction::onlyTrashed()
            ->whereIn(
                'id',
                $this->selected
            )
            ->restore();

        $this->selected = [];

        $this->selectAll = false;

        session()->flash(
            'success',
            'Transaksi berhasil direstore.'
        );
    }

    // ============================================================
    // FORCE DELETE
    // ============================================================

    public function forceDelete($id)
    {
        $transaction = Transaction::onlyTrashed()
            ->with('attachments')
            ->findOrFail($id);

        foreach ($transaction->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $transaction->forceDelete();

        session()->flash(
            'success',
            'Transaksi dihapus permanen.'
        );
    }

    // ============================================================
    // PASTE MODAL
    // ============================================================

    public function openPasteModal()
    {
        $this->resetPaste();

        $this->showPasteModal = true;
    }

    public function closePasteModal()
    {
        $this->showPasteModal = false;

        $this->resetPaste();
    }

    protected function resetPaste()
    {
        $this->pasteData = '';

        $this->pastePreview = [];

        $this->pastePeriodStart = null;

        $this->pastePeriodEnd = null;

        $this->pasteAdjusted = 0;

        $this->resetErrorBag();
    }

    // ============================================================
    // PROCESS PASTE
    // ============================================================

    public function processPaste()
    {
        $this->resetErrorBag();

        $this->pastePreview = [];

        $this->pasteAdjusted = 0;

        if (trim($this->pasteData) === '') {

            $this->addError(
                'pasteData',
                'Data paste masih kosong.'
            );

            return;
        }

        // ========================================================
        // AMBIL PERIODE
        // ========================================================

        $period = $this->extractPeriod(
            $this->pasteData
        );

        if (! $period) {

            $this->addError(
                'pasteData',
                'Periode tidak ditemukan. Contoh format: Periode Agustus 24 - 30 Agustus 2026'
            );

            return;
        }

        $periodStart = Carbon::parse(
            $period['start']
        )->startOfDay();

        $periodEnd = Carbon::parse(
            $period['end']
        )->startOfDay();

        $this->pastePeriodStart =
            $periodStart->format('Y-m-d');

        $this->pastePeriodEnd =
            $periodEnd->format('Y-m-d');

        // ========================================================
        // STATE PARSER
        // ========================================================

        $company = null;

        $branch = null;

        $category = null;

        // ========================================================
        // PECAH BARIS
        // ========================================================

        $lines = preg_split(
            '/\r\n|\r|\n/',
            $this->pasteData
        );

        foreach ($lines as $rawLine) {

            $line = trim($rawLine);

            if ($line === '') {
                continue;
            }

            // ====================================================
            // SKIP MARKDOWN SEPARATOR
            // ====================================================

            if (
                preg_match(
                    '/^[\s|:\-]+$/',
                    $line
                )
            ) {
                continue;
            }

            // ====================================================
            // BERSIHKAN MARKDOWN
            // ====================================================

            $cleanLine = preg_replace(
                '/[*_`]/',
                '',
                $line
            );

            $cleanLine = trim(
                $cleanLine
            );

            // ====================================================
            // PECAH KOLOM
            // ====================================================

            $columns = $this->splitPasteColumns(
                $cleanLine
            );

            if (empty($columns)) {
                continue;
            }

            // ====================================================
            // BERSIHKAN KOLOM
            // ====================================================

            $columns = array_map(
                function ($value) {

                    return trim(
                        preg_replace(
                            '/\s+/',
                            ' ',
                            str_replace(
                                "\xC2\xA0",
                                ' ',
                                $value
                            )
                        )
                    );

                },
                $columns
            );

            // ====================================================
            // HAPUS KOLOM KOSONG
            // ====================================================

            $columns = array_values(
                array_filter(
                    $columns,
                    fn ($value) => $value !== ''
                )
            );

            if (empty($columns)) {
                continue;
            }

            // ====================================================
            // TEKS GABUNGAN
            // ====================================================

            $upperText = strtoupper(
                implode(
                    ' ',
                    $columns
                )
            );

            $normalizedText = strtoupper(
                preg_replace(
                    '/[^A-Z0-9]+/',
                    '',
                    $upperText
                )
            );

            // ====================================================
            // SKIP HEADER PERIODE
            // ====================================================

            if (
                str_contains(
                    $upperText,
                    'PERIODE'
                )
            ) {
                continue;
            }

            // ====================================================
            // SKIP TOTAL
            // ====================================================

            if (
                preg_match(
                    '/^\s*TOTAL\b/i',
                    $cleanLine
                )
            ) {
                continue;
            }

            // ====================================================
            // DETEKSI PERUSAHAAN
            // ====================================================

            if ($normalizedText === 'AMR') {

                $company = 'AMR';

                $branch = null;

                $category = null;

                continue;
            }

            if ($normalizedText === 'SECONDTSTAR') {

                $company = 'SECONDTSTAR';

                $branch = null;

                $category = null;

                continue;
            }

            // ====================================================
            // DETEKSI CABANG
            // ====================================================

            if ($normalizedText === 'MALANG') {

                $branch = 'MALANG';

                $category = null;

                continue;
            }

            if ($normalizedText === 'BANDUNG') {

                $branch = 'BANDUNG';

                $category = null;

                continue;
            }

            // ====================================================
            // DETEKSI KATEGORI
            // ====================================================

            if (
                str_contains(
                    strtoupper($cleanLine),
                    'ATK & REFUND PESANAN'
                )
            ) {

                $category =
                    'ATK & REFUND PESANAN';

                continue;
            }

            if (
                preg_match(
                    '/^\s*GAJI\s*$/i',
                    $cleanLine
                )
            ) {

                $category = 'GAJI';

                continue;
            }

            // ====================================================
            // AMBIL NOMINAL
            // ====================================================

            $amount = $this->extractAmount(
                $columns
            );

            if ($amount === null) {
                continue;
            }

            if ($amount <= 0) {
                continue;
            }

            // ====================================================
            // AMBIL TANGGAL
            // ====================================================

            $originalDate = $this->extractDate(
                $columns
            );

            $adjusted = false;

            // ====================================================
            // TIDAK ADA TANGGAL
            //
            // Contoh:
            //
            // Rizal | Rp700.000 | Admin
            //
            // otomatis:
            //
            // 24/08/2026
            // ====================================================

            if (! $originalDate) {

                $transactionDate =
                    $periodStart->copy();

            } else {

                $transactionDate =
                    $originalDate->copy();

                // ==================================================
                // SEBELUM PERIODE
                //
                // 22/08/2026
                //
                // periode:
                // 24/08/2026 - 30/08/2026
                //
                // menjadi:
                // 24/08/2026
                // ==================================================

                if (
                    $transactionDate->lt(
                        $periodStart
                    )
                ) {

                    $transactionDate =
                        $periodStart->copy();

                    $adjusted = true;

                    $this->pasteAdjusted++;
                }

            }

            // ====================================================
            // BUILD DESCRIPTION
            // ====================================================

            $description = $this->buildDescription($columns);

            if (! $description) {
                continue;
            }

            // ====================================================
            // BERSIHKAN PREFIX PERUSAHAAN / CABANG / KATEGORI
            // ====================================================
            //
            // Contoh yang mungkin terbentuk dari paste lama:
            // AMR - MALANG - ATK & REFUND PESANAN - Rizal - Admin
            //
            // Yang disimpan hanya:
            // Rizal - Admin
            //
            // Begitu juga:
            // SECONDTSTAR - BANDUNG - ATK & REFUND PESANAN - baraka
            // menjadi:
            // baraka
            // ====================================================

            $description = $this->cleanImportedDescription(
                $description,
                $company,
                $branch,
                $category
            );

            if (! $description) {
                continue;
            }

            // ====================================================
            // PREVIEW
            // ====================================================

            $this->pastePreview[] = [

                'date' => $transactionDate->format(
                    'Y-m-d'
                ),

                'date_display' => $transactionDate->format(
                    'd/m/Y'
                ),

                'description' => $description,

                'amount' => $amount,

                'company' => $company,

                'branch' => $branch,

                'category' => $category,

                'adjusted' => $adjusted,

                // Akun dipilih per transaksi pada tabel preview.
                'account_code' => null,
                'account_name' => null,
            ];
        }

        // ========================================================
        // TIDAK ADA DATA
        // ========================================================

        if (
            empty($this->pastePreview)
        ) {

            $this->addError(
                'pasteData',
                'Tidak ada transaksi yang berhasil dibaca dari data tersebut.'
            );
        }
    }

    // ============================================================
    // SPLIT KOLOM
    // ============================================================

    protected function splitPasteColumns(
        string $line
    ): array {

        // ========================================================
        // EXCEL TAB
        // ========================================================

        if (str_contains($line, "\t")) {

            return array_map(
                'trim',
                preg_split(
                    '/\t+/',
                    $line
                )
            );
        }

        // ========================================================
        // MARKDOWN TABLE
        // ========================================================

        if (str_contains($line, '|')) {

            $line = trim(
                $line,
                '|'
            );

            return array_map(
                'trim',
                explode(
                    '|',
                    $line
                )
            );
        }

        // ========================================================
        // SATU KOLOM
        // ========================================================

        return [
            trim($line),
        ];
    }

    // ============================================================
    // EXTRACT PERIODE
    // ============================================================

    protected function extractPeriod(
        string $text
    ): ?array {

        $months = [

            'januari' => 1,
            'februari' => 2,
            'maret' => 3,
            'april' => 4,
            'mei' => 5,
            'juni' => 6,
            'juli' => 7,
            'agustus' => 8,
            'september' => 9,
            'oktober' => 10,
            'november' => 11,
            'desember' => 12,

        ];

        $monthPattern =
            implode(
                '|',
                array_keys($months)
            );

        /*
         * Format:
         *
         * Periode Agustus 24 - 30 Agustus 2026
         */

        $pattern =
            '/periode\s+('
            .$monthPattern
            .')\s+'
            .'(\d{1,2})'
            .'\s*[-–—]\s*'
            .'(\d{1,2})'
            .'\s+('
            .$monthPattern
            .')\s+'
            .'(\d{4})'
            .'/iu';

        if (
            ! preg_match(
                $pattern,
                $text,
                $matches
            )
        ) {
            return null;
        }

        $startMonth =
            $months[
                strtolower(
                    $matches[1]
                )
            ];

        $startDay =
            (int) $matches[2];

        $endDay =
            (int) $matches[3];

        $endMonth =
            $months[
                strtolower(
                    $matches[4]
                )
            ];

        $year =
            (int) $matches[5];

        try {

            $start = Carbon::create(
                $year,
                $startMonth,
                $startDay
            )->startOfDay();

            $end = Carbon::create(
                $year,
                $endMonth,
                $endDay
            )->startOfDay();

            /*
             * Kalau end < start,
             * anggap periode melewati tahun.
             */

            if ($end->lt($start)) {
                $end->addYear();
            }

            return [

                'start' => $start->format(
                    'Y-m-d'
                ),

                'end' => $end->format(
                    'Y-m-d'
                ),

            ];

        } catch (\Throwable $e) {

            return null;
        }
    }

    // ============================================================
    // EXTRACT DATE
    // ============================================================

    protected function extractDate(
        array $columns
    ): ?Carbon {

        foreach ($columns as $column) {

            $value = trim(
                $column
            );

            /*
             * 22/08/2026
             */

            if (
                preg_match(
                    '/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/',
                    $value,
                    $match
                )
            ) {

                try {

                    return Carbon::create(
                        (int) $match[3],
                        (int) $match[2],
                        (int) $match[1]
                    )->startOfDay();

                } catch (\Throwable $e) {

                    return null;
                }
            }
        }

        return null;
    }

    // ============================================================
    // EXTRACT AMOUNT
    // ============================================================

    protected function extractAmount(
        array $columns
    ): ?float {

        foreach (
            array_reverse($columns) as $column
        ) {

            $value = trim(
                $column
            );

            /*
             * Rp700.000
             * Rp 700.000
             * 700.000
             */

            if (
                ! preg_match(
                    '/^(?:Rp\.?\s*)?([\d\.,]+)$/i',
                    $value,
                    $match
                )
            ) {
                continue;
            }

            $number =
                preg_replace(
                    '/[^\d]/',
                    '',
                    $match[1]
                );

            if (
                $number === ''
            ) {
                continue;
            }

            return (float) $number;
        }

        return null;
    }

    // ============================================================
    // BUILD DESCRIPTION
    // ============================================================

    protected function buildDescription(
        array $columns
    ): ?string {

        $parts = [];

        foreach ($columns as $column) {

            $value = trim(
                preg_replace(
                    '/[*_`]/',
                    '',
                    $column
                )
            );

            if ($value === '') {
                continue;
            }

            // Skip tanggal
            if (preg_match(
                '/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/',
                $value
            )) {
                continue;
            }

            // Skip nominal
            if (preg_match(
                '/^(?:Rp\.?\s*)?[\d\.,]+$/i',
                $value
            )) {
                continue;
            }

            // Skip total
            if (preg_match('/^TOTAL\b/i', $value)) {
                continue;
            }

            // Skip header/konteks
            if (in_array(
                strtoupper($value),
                [
                    'AMR',
                    'SECONDTSTAR',
                    'MALANG',
                    'BANDUNG',
                    'GAJI',
                    'ATK & REFUND PESANAN',
                ],
                true
            )) {
                continue;
            }

            $parts[] = $value;
        }

        return empty($parts)
            ? null
            : implode(' - ', $parts);
    }

    // ============================================================
    // CLEAN IMPORTED DESCRIPTION
    // ============================================================

    protected function cleanImportedDescription(
        ?string $description,
        ?string $company = null,
        ?string $branch = null,
        ?string $category = null
    ): ?string {

        if (! $description) {
            return null;
        }

        // Normalisasi karakter hasil copy Excel/HTML.
        $description = html_entity_decode(
            (string) $description,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $description = str_replace(
            ["\xC2\xA0", "\xE2\x80\x93", "\xE2\x80\x94"],
            [' ', '-', '-'],
            $description
        );

        $description = preg_replace('/\s+/u', ' ', $description);
        $description = trim($description);

        /*
        |------------------------------------------------------------------
        | PALING PENTING:
        | Ambil teks SETELAH kategori terakhir.
        |
        | Contoh:
        | AMR - MALANG - ATK & REFUND PESANAN - Rizal - Admin
        |                                      ^^^^^^^^^^^^^
        |                                      hasil
        |
        | SECONDTSTAR - BANDUNG - ATK & REFUND PESANAN - baraka
        |                                                ^^^^^^
        |                                                hasil
        |
        | Untuk GAJI:
        | AMR - MALANG - GAJI - Rizal - Admin
        |                         ^^^^^^^^^^^^^
        |                         hasil
        |------------------------------------------------------------------
        */

        $categoryPattern = '(?:ATK\s*&\s*REFUND\s+PESANAN|GAJI)';

        $parts = preg_split(
            '/\s*[-:]\s*'.$categoryPattern.'\s*[-:]\s*/iu',
            $description
        );

        if (count($parts) > 1) {
            $description = trim(
                end($parts)
            );
        }

        // Kalau masih ada prefix perusahaan/cabang, buang dari awal.
        $description = preg_replace(
            '/^(?:AMR|SECONDTSTAR)\s*[-:]\s*/iu',
            '',
            $description
        );

        $description = preg_replace(
            '/^(?:MALANG|BANDUNG)\s*[-:]\s*/iu',
            '',
            $description
        );

        // Buang kategori jika ternyata masih tertinggal.
        $description = preg_replace(
            '/^(?:ATK\s*&\s*REFUND\s+PESANAN|GAJI)\s*[-:]\s*/iu',
            '',
            $description
        );

        // Bersihkan separator yang tersisa.
        $description = preg_replace(
            '/\s*[-:]\s*$/u',
            '',
            $description
        );

        $description = trim(
            $description,
            " \t\r\n-:|"
        );

        return $description !== ''
            ? $description
            : null;
    }

    // ============================================================
    // IMPORT KE DATABASE
    // ============================================================

    public function importPastedTransactions()
    {
        $this->resetErrorBag();

        if (empty($this->pastePreview)) {
            $this->addError(
                'pasteData',
                'Tidak ada data untuk diimport.'
            );

            return;
        }

        // Semua transaksi wajib mempunyai akun masing-masing.
        foreach ($this->pastePreview as $index => $row) {
            if (empty($row['account_code'])) {
                $this->addError(
                    'pasteData',
                    'Masih ada transaksi yang belum dipilihkan akun pada baris '.($index + 1).'.'
                );

                return;
            }
        }

        // Ambil nama akun berdasarkan seluruh kode akun yang dipilih.
        $accountCodes = collect($this->pastePreview)
            ->pluck('account_code')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $accounts = Transaction::query()
            ->whereIn('account_code', $accountCodes)
            ->whereNotNull('account_name')
            ->select('account_code', 'account_name')
            ->distinct()
            ->get()
            ->keyBy('account_code');

        // Pastikan semua kode akun yang dipilih benar-benar ada.
        foreach ($this->pastePreview as $index => $row) {
            if (! isset($accounts[$row['account_code']])) {
                $this->addError(
                    'pasteData',
                    'Akun pada baris '.($index + 1).' tidak ditemukan.'
                );

                return;
            }
        }

        DB::transaction(function () use ($accounts) {
            foreach ($this->pastePreview as $row) {
                $account = $accounts[$row['account_code']];

                Transaction::create([
                    'transaction_date' => $row['date'],
                    'description' => $row['description'],
                    'account_code' => $account->account_code,
                    'account_name' => $account->account_name,
                    'payment_method' => null,
                    'debit' => $row['amount'],
                    'credit' => 0,
                ]);
            }
        });

        $count = count($this->pastePreview);

        $this->closePasteModal();

        session()->flash(
            'success',
            "{$count} transaksi berhasil diimport."
        );

        $this->resetPage();
    }

    // ============================================================
    // RENDER
    // ============================================================

    public function render()
    {
        $transactions = $this->transactionQuery()
            ->with('attachments')
            ->latest('transaction_date')
            ->paginate(
                $this->perPage
            );

        /*
         * Daftar akun untuk dropdown per transaksi pada preview Paste.
         */

        $accounts = Transaction::query()
            ->whereNotNull('account_code')
            ->whereNotNull('account_name')
            ->select(
                'account_code',
                'account_name'
            )
            ->distinct()
            ->orderBy(
                'account_code'
            )
            ->get();

        return view(
            'livewire.transactions.index',
            compact(
                'transactions',
                'accounts'
            )
        )->layout(
            'layouts.app'
        );
    }
}
