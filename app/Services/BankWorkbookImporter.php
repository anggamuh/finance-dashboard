<?php

namespace App\Services;

use App\Models\BankEntry;
use App\Services\BankTransactionSync;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use RuntimeException;

class BankWorkbookImporter
{
    public function parse(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $rows = [];
        $skipped = 0;
        $warnings = [];

        try {
            foreach (['BCA', 'MANDIRI', 'KAS'] as $ledger) {
                $sheet = $spreadsheet->getSheetByName($ledger);
                if (! $sheet) {
                    $warnings[] = "Sheet {$ledger} tidak ditemukan.";
                    continue;
                }

                // BCA/MANDIRI have leading LINE and separate DBACC/name columns;
                // KAS starts directly at DBACC.
                $offset = $ledger === 'KAS' ? 0 : 1;
                $dataStart = $ledger === 'MANDIRI' ? 2 : 3;
                $maxRow = $sheet->getHighestDataRow();
                for ($line = $dataStart; $line <= $maxRow; $line++) {
                    $v = $sheet->rangeToArray("A{$line}:L{$line}", null, true, false)[0];
                    $get = static fn (int $i) => $v[$i + $offset] ?? null;
                    $date = $this->date($get(3));
                    $debit = $this->money($get(8));
                    $credit = $this->money($get(9));
                    if (! $date || ($debit == 0 && $credit == 0)) {
                        $skipped++;
                        continue;
                    }

                    $row = [
                        'ledger' => $ledger,
                        'entry_date' => $date,
                        'account_code' => $this->string($get(0)),
                        'account_name' => $this->string($get(1)),
                        'invoice' => $this->string($get(2)),
                        'document_number' => $this->string($get(4)),
                        'payment_channel' => $this->string($get(5)),
                        'cheque_number' => $this->string($get(6)),
                        'description' => $this->string($get(7)),
                        'debit' => $debit,
                        'credit' => $credit,
                        'source_balance' => $this->nullableMoney($get(10)),
                        'source' => 'excel',
                    ];
                    $row['fingerprint'] = BankEntry::fingerprintFor($row);
                    $rows[] = $row;
                }
            }
        } finally {
            $spreadsheet->disconnectWorksheets();
        }

        if (! $rows) {
            throw new RuntimeException('Tidak ada transaksi valid pada sheet BCA, MANDIRI, atau KAS.');
        }

        return ['rows' => $rows, 'skipped' => $skipped, 'warnings' => $warnings];
    }

    public function import(string $path): array
    {
        $parsed = $this->parse($path);
        $inserted = 0;
        $duplicates = 0;
        DB::transaction(function () use ($parsed, &$inserted, &$duplicates) {
            $sync = app(BankTransactionSync::class);
            foreach ($parsed['rows'] as $row) {
                $created = BankEntry::query()->insertOrIgnore(array_merge(
                    $row, ['created_at' => now(), 'updated_at' => now()]
                ));
                $inserted += $created;
                $duplicates += 1 - $created;
                // Older entries and new rows both stay linked to the Transactions table.
                $entry = BankEntry::query()->where('fingerprint', $row['fingerprint'])->first();
                if ($entry) {
                    $sync->sync($entry);
                }
            }
        });

        return ['inserted' => $inserted, 'duplicates' => $duplicates,
            'skipped' => $parsed['skipped'], 'warnings' => $parsed['warnings']];
    }

    private function string(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));
        return $text === '' ? null : $text;
    }

    private function money(mixed $value): float
    {
        if ($value === null || $value === '') return 0;
        if (is_numeric($value)) return round((float) $value, 2);
        $str = trim((string) $value);
        $str = str_replace(['Rp', ' ', '.'], '', $str);
        $str = str_replace(',', '.', $str);
        return is_numeric($str) ? round((float) $str, 2) : 0;
    }

    private function nullableMoney(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : $this->money($value);
    }

    private function date(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;
        try {
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject((float) $value))->toDateString();
            }
            return Carbon::parse((string) $value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
