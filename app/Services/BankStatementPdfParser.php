<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Conservative text-PDF parser. Never imports directly: always requires preview confirmation.
 * Uses a pure-PHP library: no shell execution, pdftotext, or proc_open required.
 * Image-only PDFs need OCR and are intentionally rejected.
 */
class BankStatementPdfParser
{
    public function extract(string $path): array
    {
        if (!is_file($path) || filesize($path) > 10 * 1024 * 1024) {
            throw ValidationException::withMessages(['bankPdf'=>'File PDF tidak valid atau melebihi 10 MB.']);
        }
        $handle = fopen($path, 'rb');
        $signature = $handle ? fread($handle, 5) : '';
        if ($handle) fclose($handle);
        if ($signature !== '%PDF-') {
            throw ValidationException::withMessages(['bankPdf'=>'File bukan PDF yang valid.']);
        }
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            throw ValidationException::withMessages(['bankPdf' =>
                'Library PDF belum terpasang. Jalankan composer require smalot/pdfparser:^2.13 di lokal, lalu deploy vendor/lock yang diperbarui.'
            ]);
        }

        try {
            // Cap decompression memory for PDFs from untrusted sources.
            $config = new \Smalot\PdfParser\Config();
            $config->setDecodeMemoryLimit(64 * 1024 * 1024);
            $parser = new \Smalot\PdfParser\Parser([], $config);
            $text = $parser->parseFile($path)->getText();
        } catch (\Throwable $exception) {
            report($exception);
            throw ValidationException::withMessages(['bankPdf' =>
                'Isi PDF tidak bisa dibaca. Coba PDF teks tanpa password, bukan hasil scan.'
            ]);
        }

        if (trim((string) $text) === '') {
            throw ValidationException::withMessages(['bankPdf' =>
                'PDF ini tidak berisi teks yang bisa diekstrak. PDF hasil scan perlu OCR.'
            ]);
        }

        return $this->parse($text);
    }

    public function parse(string $text): array
    {
        if (preg_match('/Detail\s+Mutasi/u', $text) && preg_match('/No\s+Referensi/u', $text)) {
            return $this->parseBcaStatement($text);
        }

        $entries = [];
        $unrecognized = [];
        // Conservative format: DD/MM/YYYY Description Amount D|C or DR|CR.
        // Common complex layouts are deliberately flagged for review, not guessed.
        foreach (preg_split('/\R/u', $text) as $lineNo => $line) {
            $line = trim(preg_replace('/\s+/u', ' ', $line));
            if ($line === '') continue;
            if (!preg_match('/^(?<date>\d{2}[\/-]\d{2}[\/-]\d{4})\s+(?<description>.+?)\s+(?<amount>\d[\d.,]*)\s+(?<dc>DB|DR|DEBIT|CR|CREDIT|KREDIT)$/iu', $line, $m)) {
                // Flag date-bearing lines that might contain transactions; never silently drop them.
                if (preg_match('/^\d{2}[\/-]\d{2}[\/-]\d{4}\b/u',$line)) {
                    $unrecognized[] = ['line'=>$lineNo+1,'text'=>mb_substr($line,0,250)];
                }
                continue;
            }
            $date = $this->date($m['date']);
            $amount = $this->amount($m['amount']);
            if ($amount <= 0) {
                $unrecognized[] = ['line'=>$lineNo+1,'text'=>mb_substr($line,0,250)];
                continue;
            }
            $entries[] = [
                'posted_at'=>$date,
                'description'=>mb_substr($m['description'],0,2000),
                'reference'=>'',
                'direction'=>in_array(strtoupper($m['dc']), ['CR','CREDIT','KREDIT'],true) ? 'in' : 'out',
                'amount'=>number_format($amount,2,'.',''),
            ];
        }
        if (!$entries || $unrecognized) {
            return ['entries'=>$entries,'unrecognized'=>$unrecognized,'ready'=>false];
        }
        return ['entries'=>$entries,'unrecognized'=>[],'ready'=>true];
    }

    /**
     * BCA e-statement rows have two dates, a multiline description, a branch and
     * reference, debit/credit columns, then the running balance. Extractors may
     * preserve table spacing OR place every PDF text element on a separate line.
     * Validate the whole statement before allowing approval.
     */
    private function parseBcaStatement(string $text): array
    {
        $number = '\d{1,3}(?:,\d{3})*\.\d{2}';
        $date = '\d{2}\/\d{2}\/\d{4}';
        $header = '/^\h*(?<number>\d{1,4})\s+(?<effective>'.$date.')\s+(?<posted>'.$date.')\s*/mu';
        preg_match_all($header, $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $entries = [];
        $errors = [];
        $runningBalance = null;
        $totalIn = 0;
        $totalOut = 0;

        foreach ($matches as $index => $match) {
            $start = $match[0][1] + strlen($match[0][0]);
            $end = isset($matches[$index + 1])
                ? $matches[$index + 1][0][1]
                : strlen($text);
            $body = trim(substr($text, $start, $end - $start));
            // Tail may have extra page headers or summary; recognize only a complete
            // branch/ref/debit/credit/balance sequence at the beginning of its tail.
            $tail = '/(?<branch>\d{4})\s+(?<reference>\d{8,15})\s+(?<debit>-|'.$number.')\s+(?<credit>-|'.$number.')\s+(?<balance>'.$number.')(?=\s|$)/u';
            if (!preg_match($tail, $body, $parts, PREG_OFFSET_CAPTURE)) {
                $errors[] = ['line'=>(int)$match['number'][0], 'text'=>'Kolom transaksi tidak lengkap'];
                continue;
            }

            $debit = $parts['debit'][0] === '-' ? 0 : $this->bcaCents($parts['debit'][0]);
            $credit = $parts['credit'][0] === '-' ? 0 : $this->bcaCents($parts['credit'][0]);
            $balance = $this->bcaCents($parts['balance'][0]);
            if (($debit === 0) === ($credit === 0)) {
                $errors[] = ['line'=>(int)$match['number'][0], 'text'=>'Debit/kredit tidak valid'];
                continue;
            }
            $description = substr($body, 0, $parts[0][1]);
            // Layout-preserving extractors put extra description lines AFTER the
            // monetary columns; token-based extractors put them before the columns.
            $afterBalance = substr($body, $parts[0][1] + strlen($parts[0][0]));
            foreach (preg_split('/\R/u', $afterBalance) as $continuation) {
                $continuation = trim($continuation);
                if ($continuation === '' || $continuation === '-') continue;
                if (preg_match('/^(?:Saldo Awal|Saldo Akhir|Mutasi Debit|Mutasi Kredit|Di-request|Mutasi Rekening|Detail Mutasi|Total Transaksi|No\.\s|Tanggal Efektif|Tanggal Transaksi)/iu', $continuation)) {
                    break;
                }
                // Skip PDF page numbers and repeated table header fragments.
                if (preg_match('/^\d+\/\d+$/', $continuation) ||
                    in_array(mb_strtolower($continuation), ['tanggal','efektif','transaksi','keterangan','cabang','no referensi','debit','kredit','saldo'], true)) {
                    continue;
                }
                $description .= ' '.$continuation;
            }
            $description = trim(preg_replace('/\s+/u', ' ', $description));
            if ($description === '') {
                $errors[] = ['line'=>(int)$match['number'][0], 'text'=>'Keterangan kosong'];
                continue;
            }
            $rowNumber = (int)$match['number'][0];
            if ($rowNumber !== $index + 1) {
                $errors[] = ['line'=>$rowNumber, 'text'=>'Urutan nomor transaksi tidak sesuai'];
            }
            if ($runningBalance !== null && $runningBalance + $credit - $debit !== $balance) {
                $errors[] = ['line'=>$rowNumber, 'text'=>'Saldo berjalan tidak sesuai'];
            }
            $runningBalance = $balance;
            $totalIn += $credit;
            $totalOut += $debit;
            $entries[] = [
                'posted_at'=>$this->date($match['posted'][0]),
                'reference'=>$parts['reference'][0],
                'description'=>mb_substr($description, 0, 2000),
                'direction'=>$credit > 0 ? 'in' : 'out',
                'amount'=>number_format(($credit > 0 ? $credit : $debit) / 100, 2, '.', ''),
            ];
        }

        $expectedCount = $this->bcaSummary($text, '/Total\s+Transaksi\s*:\s*(\d+)/iu', true);
        $opening = $this->bcaSummary($text, '/Saldo\s+Awal\s+('.$number.')/iu');
        $closing = $this->bcaSummary($text, '/Saldo\s+Akhir\s+('.$number.')/iu');
        $reportedDebit = $this->bcaSummary($text, '/Mutasi\s+Debit\s+('.$number.')/iu');
        $reportedCredit = $this->bcaSummary($text, '/Mutasi\s+Kredit\s+('.$number.')/iu');

        if ($expectedCount === null || $expectedCount !== count($entries) || !$entries) {
            $errors[] = ['line'=>0, 'text'=>'Jumlah transaksi PDF tidak cocok dengan tabel'];
        }
        if ($opening === null || $closing === null || $reportedDebit === null || $reportedCredit === null) {
            $errors[] = ['line'=>0, 'text'=>'Ringkasan saldo/debit/kredit tidak lengkap'];
        } elseif ($reportedDebit !== $totalOut || $reportedCredit !== $totalIn ||
                  $opening + $totalIn - $totalOut !== $closing || $runningBalance !== $closing) {
            $errors[] = ['line'=>0, 'text'=>'Total mutasi atau saldo akhir tidak sesuai ringkasan BCA'];
        }

        return ['entries'=>$entries, 'unrecognized'=>$errors, 'ready'=>count($errors) === 0];
    }

    private function bcaCents(string $value): int
    {
        return (int)str_replace([',','.'], '', $value);
    }

    private function bcaSummary(string $text, string $pattern, bool $count = false): ?int
    {
        if (!preg_match($pattern, $text, $match)) {
            return null;
        }
        return $count ? (int)$match[1] : $this->bcaCents($match[1]);
    }

    private function date(string $raw): string
    {
        $format = str_contains($raw,'/') ? '!d/m/Y' : '!d-m-Y';
        $d = Carbon::createFromFormat($format,$raw);
        if (!$d || $d->format(substr($format,1)) !== $raw) {
            throw ValidationException::withMessages(['bankPdf'=>'Tanggal di PDF tidak valid.']);
        }
        return $d->toDateString();
    }

    private function amount(string $raw): float
    {
        if (str_contains($raw,'.') && str_contains($raw,',')) {
            $raw = strrpos($raw,',') > strrpos($raw,'.')
                ? str_replace(',','.',str_replace('.','',$raw))
                : str_replace(',','',$raw);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/',$raw)) {
            $raw = str_replace('.','',$raw);
        } elseif (preg_match('/^\d{1,3}(,\d{3})+$/',$raw)) {
            $raw = str_replace(',','',$raw);
        } else {
            $raw = str_replace(',','.',$raw);
        }
        return round((float)$raw,2);
    }
}