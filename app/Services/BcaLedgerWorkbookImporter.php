<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Import the BCA worksheet of the company cash/bank workbook. This is an
 * accounting ledger, not a bank statement: debit = cash in, credit = cash out.
 *
 * Source layout:
 *   E TGL, F No. Bank, I URAIAN, J DEBIT, K KREDIT, L SALDO
 * Header is expected on row 2, followed by historical months and subtotals.
 */
class BcaLedgerWorkbookImporter
{
    public function read(string $path, string $start, string $end): array
    {
        $from = CarbonImmutable::parse($start)->toDateString();
        $to = CarbonImmutable::parse($end)->toDateString();
        if ($from > $to) {
            throw ValidationException::withMessages(['ledgerFile'=>'Periode bank tidak valid.']);
        }

        $spreadsheet = IOFactory::load($path);
        try {
            if (! $spreadsheet->sheetNameExists('BCA')) {
                throw ValidationException::withMessages(['ledgerFile'=>'Sheet BCA tidak ditemukan dalam Excel.']);
            }
            $sheet = $spreadsheet->getSheetByName('BCA');
            $headers = [
                'E' => 'TGL',
                'F' => 'No. Bank',
                'I' => 'URAIAN',
                'J' => 'DEBIT',
                'K' => 'KREDIT',
                'L' => 'SALDO',
            ];
            foreach ($headers as $column => $expected) {
                $value = strtolower(trim((string)$sheet->getCell($column.'2')->getValue()));
                if ($value !== strtolower($expected)) {
                    throw ValidationException::withMessages(['ledgerFile'=>
                        'Format sheet BCA berbeda pada kolom '.$column.'; perlu mapping manual.'
                    ]);
                }
            }

            $rows = [];
            $invalid = [];
            $lastRow = min($sheet->getHighestDataRow(), 20000);
            for ($index = 3; $index <= $lastRow; $index++) {
                $rawDate = $sheet->getCell('E'.$index)->getValue();
                if ($rawDate === null || trim((string)$rawDate) === '') continue;
                try {
                    if (is_numeric($rawDate)) {
                        $date = CarbonImmutable::instance(ExcelDate::excelToDateTimeObject((float)$rawDate))->toDateString();
                    } else {
                        $text = trim((string)$rawDate);
                        $date = null;
                        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y'] as $format) {
                            try {
                                $parsed = CarbonImmutable::createFromFormat('!'.$format, $text);
                                if ($parsed && $parsed->format($format) === $text) { $date = $parsed->toDateString(); break; }
                            } catch (\Throwable) {}
                        }
                        if ($date === null) continue; // Non-journal label/summary.
                    }
                } catch (\Throwable) {
                    continue; // Labels, month subtotals and stray cells are not journal entries.
                }
                if ($date < $from || $date > $to) continue;

                try {
                    $in = $this->money($sheet->getCell('J'.$index)->getValue());
                    $out = $this->money($sheet->getCell('K'.$index)->getValue());
                    if (($in <= 0 && $out <= 0) || ($in > 0 && $out > 0) || $in < 0 || $out < 0) {
                        throw new \InvalidArgumentException('Debit/kredit tidak valid');
                    }
                    $description = trim(implode(' / ', array_filter([
                        (string)$sheet->getCell('D'.$index)->getFormattedValue(),
                        (string)$sheet->getCell('I'.$index)->getFormattedValue(),
                    ], fn ($text) => trim($text) !== '')));
                    $reference = trim((string)$sheet->getCell('F'.$index)->getFormattedValue());
                    if ($description === '') $description = 'Jurnal BCA Excel baris '.$index;
                    $amount = $in > 0 ? $in : $out;
                    $rows[] = [
                        'posted_at'=>$date,
                        'reference'=>mb_substr($reference,0,255),
                        'description'=>mb_substr($description,0,2000),
                        'direction'=>$in > 0 ? 'in' : 'out',
                        'amount'=>number_format($amount,2,'.',''),
                    ];
                } catch (\Throwable $exception) {
                    $invalid[] = 'Baris '.$index.': '.$exception->getMessage();
                    if (count($invalid) >= 5) break;
                }
            }

            if ($invalid) {
                throw ValidationException::withMessages(['ledgerFile'=>implode(' | ',$invalid).' (tidak diimpor)']);
            }
            if (count($rows) === 0) {
                throw ValidationException::withMessages(['ledgerFile'=>'Sheet BCA tidak memiliki transaksi dalam rentang tanggal PDF bank.']);
            }
            if (count($rows) > 10000) {
                throw ValidationException::withMessages(['ledgerFile'=>'Maksimal 10.000 transaksi per periode.']);
            }

            return $rows;
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    private function money(mixed $value): float
    {
        if ($value === null || trim((string)$value) === '' || $value === '-') return 0.0;
        if (is_numeric($value)) return round((float)$value,2);
        $text = str_replace(['Rp','rp',' '], '', trim((string)$value));
        if (preg_match('/^-?\d{1,3}(\.\d{3})+(,\d{1,2})?$/',$text)) {
            $text = str_replace(',','.',str_replace('.','',$text));
        } elseif (preg_match('/^-?\d{1,3}(,\d{3})+(\.\d{1,2})?$/',$text)) {
            $text = str_replace(',','',$text);
        } else {
            $text = str_replace(',','.',$text);
        }
        if (!is_numeric($text)) throw new \InvalidArgumentException('Nominal bukan angka');
        return round((float)$text,2);
    }
}
