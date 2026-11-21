<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Conservative text-PDF parser. Never imports directly: always requires preview confirmation.
 * Requires poppler's pdftotext executable and proc_open on the PHP host.
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
        if (!function_exists('proc_open')) {
            throw ValidationException::withMessages(['bankPdf'=>'Ekstraksi PDF belum didukung di hosting ini (proc_open dinonaktifkan).']);
        }

        $out = tempnam(sys_get_temp_dir(), 'bank-text-');
        try {
            // Array-form command avoids the shell, and the input path is never interpolated.
            $command = ['pdftotext', '-layout', '-nopgbrk', '-enc', 'UTF-8', $path, $out];
            $descriptors = [1 => ['pipe','w'], 2 => ['pipe','w']];
            $process = @proc_open($command, $descriptors, $pipes);
            if (!is_resource($process)) {
                throw ValidationException::withMessages(['bankPdf'=>'pdftotext belum tersedia di server.']);
            }
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            if (proc_close($process) !== 0) {
                throw ValidationException::withMessages(['bankPdf'=>'PDF tidak dapat dibaca (mungkin terenkripsi atau bermasalah).']);
            }
            $text = (string) file_get_contents($out);
            if (trim($text) === '') {
                throw ValidationException::withMessages(['bankPdf'=>'PDF tidak memiliki teks yang bisa diekstrak. PDF hasil scan memerlukan OCR.']);
            }
            return $this->parse($text);
        } finally {
            @unlink($out);
        }
    }

    public function parse(string $text): array
    {
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