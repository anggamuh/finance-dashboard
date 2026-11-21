<?php

namespace App\Services;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ReconciliationExcelImporter
{
    private const HEADERS = [
        'date' => ['tanggal','tgl','date','transaction date','tanggal transaksi','posting date','tanggal mutasi'],
        'description' => ['keterangan','deskripsi','description','uraian','remarks','memo','detail','berita','keterangan transaksi'],
        'reference' => ['referensi','reference','ref','no bukti','nomor bukti','no transaksi','transaction number','nomor transaksi'],
        'in' => ['masuk','penerimaan','debit','debet','uang masuk','kas masuk','mutasi masuk','credit bank'],
        'out' => ['keluar','pengeluaran','kredit','credit','uang keluar','kas keluar','mutasi keluar','debit bank'],
        'amount' => ['nominal','jumlah','amount','nilai','total','mutasi'],
        'direction' => ['jenis','tipe','type','arah','jenis transaksi','dr cr','dc','debit kredit'],
    ];

    /**
     * User confirms source direction for the chosen Excel file.
     * - separated: in/out columns
     * - signed: positive amount=in, negative=out
     * - typed: amount + direction column
     */
    public function read(string $path, int $headerRow = 1, string $mode = 'separated', ?string $sheetName = null): array
    {
        if (!in_array($mode, ['separated','signed','typed'], true)) {
            throw ValidationException::withMessages(['importMode' => 'Mode kolom tidak valid.']);
        }
        if ($headerRow < 1 || $headerRow > 30) {
            throw ValidationException::withMessages(['headerRow' => 'Baris judul harus antara 1 sampai 30.']);
        }

        $spreadsheet = IOFactory::load($path);
        try {
            if ($sheetName !== null && trim($sheetName) !== '') {
                if (!$spreadsheet->sheetNameExists($sheetName)) {
                    throw ValidationException::withMessages(['sheetName'=>'Sheet tidak ditemukan.']);
                }
                $sheet = $spreadsheet->getSheetByName($sheetName);
            } else {
                $sheet = $spreadsheet->getActiveSheet();
            }

            $rawRows = $sheet->toArray(null, true, true, false);
            if (count($rawRows) > 10030) {
                throw ValidationException::withMessages(['excelFile'=>'Maksimal 10.000 transaksi per file.']);
            }
            $header = $rawRows[$headerRow - 1] ?? [];
            $normalized = array_map(fn ($v) => mb_strtolower(trim(preg_replace('/\s+/u',' ',(string)$v))), $header);
            $columns = [];
            foreach (self::HEADERS as $field => $aliases) {
                foreach ($normalized as $i => $value) {
                    if (in_array($value, $aliases, true)) {
                        $columns[$field] = $i;
                        break;
                    }
                }
            }
            $required = ['date','description'];
            $required = array_merge($required, match ($mode) {
                'separated' => ['in','out'],
                'signed' => ['amount'],
                'typed' => ['amount','direction'],
            });
            $missing = array_filter($required, fn ($field) => !array_key_exists($field, $columns));
            if ($missing) {
                throw ValidationException::withMessages([
                    'excelFile' => 'Kolom tidak dikenali: '.implode(', ', $missing).'. Ganti nama header atau mode import.',
                ]);
            }

            $entries = [];
            $errors = [];
            foreach (array_slice($rawRows, $headerRow) as $i => $row) {
                $line = $headerRow + $i + 1;
                if (!array_filter($row, fn ($v) => $v !== null && trim((string)$v) !== '')) continue;
                try {
                    $value = fn ($field) => isset($columns[$field]) ? ($row[$columns[$field]] ?? null) : null;
                    $date = $this->date($value('date'));
                    $description = trim((string)$value('description'));
                    $reference = trim((string)$value('reference'));
                    if ($description === '') throw new \InvalidArgumentException('Keterangan kosong');

                    if ($mode === 'separated') {
                        $in = $this->money($value('in'));
                        $out = $this->money($value('out'));
                        if ($in < 0 || $out < 0 || ($in > 0 && $out > 0) || ($in == 0 && $out == 0)) {
                            throw new \InvalidArgumentException('Isi salah satu kolom masuk/keluar dengan nominal positif');
                        }
                        $direction = $in > 0 ? 'in' : 'out';
                        $amount = $in > 0 ? $in : $out;
                    } elseif ($mode === 'signed') {
                        $signed = $this->money($value('amount'));
                        if ($signed == 0) throw new \InvalidArgumentException('Nominal tidak boleh nol');
                        $direction = $signed > 0 ? 'in' : 'out';
                        $amount = abs($signed);
                    } else {
                        $amount = $this->money($value('amount'));
                        $type = mb_strtolower(trim((string)$value('direction')));
                        $direction = match ($type) {
                            'in','masuk','penerimaan','cr','credit','kredit' => 'in',
                            'out','keluar','pengeluaran','dr','debit','debet' => 'out',
                            default => throw new \InvalidArgumentException('Jenis transaksi tidak dikenali'),
                        };
                        if ($amount <= 0) throw new \InvalidArgumentException('Nominal harus positif');
                    }

                    $entries[] = [
                        'posted_at' => $date,
                        'reference' => mb_substr($reference,0,255),
                        'description' => $description,
                        'direction' => $direction,
                        'amount' => number_format($amount,2,'.',''),
                    ];
                } catch (\Throwable $e) {
                    $errors[] = "Baris {$line}: ".$e->getMessage();
                    if (count($errors) >= 5) break;
                }
            }
            if ($errors) {
                throw ValidationException::withMessages(['excelFile'=>implode(' | ',$errors).' (tidak ada transaksi disimpan)']);
            }
            if (!$entries) {
                throw ValidationException::withMessages(['excelFile'=>'Tidak ada baris transaksi yang bisa diimpor.']);
            }
            return $entries;
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    public function replace(ReconciliationBatch $batch, string $source, array $entries): int
    {
        if (!in_array($source,['bank','ledger'],true)) {
            throw new \InvalidArgumentException('Sumber tidak valid');
        }
        return DB::transaction(function () use ($batch, $source, $entries) {
            $batch = ReconciliationBatch::whereKey($batch->id)->lockForUpdate()->firstOrFail();
            if ($batch->entries()->where('status','matched')->exists()) {
                throw ValidationException::withMessages(['excelFile'=>
                    'Periode ini sudah memiliki transaksi yang disetujui. Buat periode baru untuk mengganti data.'
                ]);
            }
            $batch->entries()->where('status','suggested')
                ->update(['status'=>'unmatched','matched_entry_id'=>null,'confidence'=>null]);
            $batch->entries()->where('source',$source)->delete();
            $now = now();
            foreach (array_chunk($entries,500) as $chunk) {
                $rows = [];
                foreach ($chunk as $entry) {
                    $rows[] = $entry + [
                        'batch_id'=>$batch->id,
                        'source'=>$source,
                        'fingerprint'=>hash('sha256',implode('|',[$source,$entry['posted_at'],$entry['reference'],$entry['description'],$entry['direction'],$entry['amount']])),
                        'status'=>'unmatched',
                        'created_at'=>$now,'updated_at'=>$now,
                    ];
                }
                ReconciliationEntry::insert($rows);
            }
            return count($entries);
        });
    }

    private function date(mixed $raw): string
    {
        if (is_numeric($raw)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float)$raw))->toDateString();
        }
        $date = trim((string)$raw);
        foreach (['d/m/Y','d-m-Y','Y-m-d','d.m.Y','m/d/Y'] as $format) {
            try {
                $parsed = Carbon::createFromFormat('!'.$format,$date);
                if ($parsed && $parsed->format($format) === $date) return $parsed->toDateString();
            } catch (\Throwable) {}
        }
        throw new \InvalidArgumentException('Format tanggal tidak dikenali');
    }

    private function money(mixed $raw): float
    {
        $value = preg_replace('/[^\d.,\-]/u','',trim((string)($raw ?? '')));
        if ($value === '') return 0.0;
        if (str_contains($value,',') && str_contains($value,'.')) {
            $value = strrpos($value,',') > strrpos($value,'.')
                ? str_replace(',','.',str_replace('.','',$value))
                : str_replace(',','',$value);
        } elseif (preg_match('/^-?\d{1,3}(\.\d{3})+$/',$value)) {
            $value = str_replace('.','',$value);
        } elseif (preg_match('/^-?\d{1,3}(,\d{3})+$/',$value)) {
            $value = str_replace(',','',$value);
        } else {
            $value = str_replace(',','.',$value);
        }
        if (!is_numeric($value)) throw new \InvalidArgumentException('Nominal tidak valid');
        return round((float)$value,2);
    }
}
