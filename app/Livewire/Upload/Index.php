<?php

namespace App\Livewire\Upload;

use App\Models\Import;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Models\Account;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $file;

    public $uploadMessage = '';

    public $uploadError = '';

    public $isUploading = false;

    protected $rules = [
        'file' => 'required|file|mimes:csv,txt|max:10240',
    ];

    public function importCsv()
    {
        Log::info('Upload action started', ['file' => optional($this->file)->getClientOriginalName()]);

        $this->uploadMessage = '';
        $this->uploadError = '';
        $this->isUploading = true;

        try {
            // Validasi file
            $this->validate();

            if (! Schema::hasTable('imports') || ! Schema::hasTable('transactions')) {
                throw new \Exception('Tabel database belum siap. Jalankan php artisan migrate terlebih dahulu.');
            }

            if (! $this->file) {
                throw new \Exception('File tidak ditemukan');
            }

            $path = $this->file->store('imports', 'public');

            $filePath = Storage::disk('public')->path($path);

            Log::info('STEP 1');

            if (! file_exists($filePath)) {
                throw new \Exception('File tidak tersimpan dengan benar');
            }

            Log::info('CSV file stored successfully', ['path' => $filePath]);

            $csv = fopen($filePath, 'r');
            if (! $csv) {
                throw new \Exception('Tidak bisa membuka file CSV');
            }

            // Skip header
            fgetcsv($csv);

            $transactions = [];
            $rowCount = 0;

            while (($row = fgetcsv($csv, 10000, ',')) !== false) {
                $rowCount++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    if (count($row) < 8) {
                        continue;
                    }

                    // Parse tanggal - coba format d/m/Y dulu, kalau gagal coba format lain
                    $dateString = trim($row[3]);
                    $transactionDate = null;

                    try {
                        $transactionDate = \DateTime::createFromFormat('d/m/Y', $dateString);
                        if (! $transactionDate) {
                            $transactionDate = new \DateTime($dateString);
                        }
                    } catch (\Exception $e) {
                        $transactionDate = new \DateTime($dateString);
                    }

                    // Parse decimal
                    $debit = $this->parseDecimal($row[6]);
                    $credit = $this->parseDecimal($row[7]);

                    // Simpan akun jika belum ada
                    Account::firstOrCreate(
                        [
                            'code' => trim($row[0]),
                        ],
                        [
                            'name' => trim($row[1]),
                            'type' => $this->detectAccountType(trim($row[0])),
                            'category' => null,
                        ]
                    );

                    $transactions[] = [
                        'account_code' => trim($row[0]),
                        'account_name' => trim($row[1]),
                        'transaction_number' => trim($row[2]),
                        'transaction_date' => $transactionDate->format('Y-m-d'),
                        'transaction_type' => trim($row[4] ?? ''),
                        'description' => trim($row[5] ?? ''),
                        'debit' => $debit,
                        'credit' => $credit,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } catch (\Exception $e) {
                    // Skip baris yang error
                    continue;
                }
            }

            fclose($csv);

            if (empty($transactions)) {
                throw new \Exception('Tidak ada data yang valid untuk diimport');
            }

            // Batch insert dengan transaction
            DB::beginTransaction();
            $import = null;

            try {
               $import = Import::create([
    'file_name'  => $this->file->getClientOriginalName(),
    'file_path'  => $path,
    'total_rows' => count($transactions),
    'status'     => 'processing',
]);

                // Add import_id ke semua transactions
                $transactions = array_map(function ($transaction) use ($import) {
                    $transaction['import_id'] = $import->id;

                    return $transaction;
                }, $transactions);

                // Insert dalam batch untuk performa
                foreach (array_chunk($transactions, 500) as $chunk) {
                    Transaction::insert($chunk);
                }

                $import->update(['status' => 'completed']);
                DB::commit();


                $this->uploadMessage = 'Import berhasil: '.count($transactions).' transaksi ditambahkan.';
                $this->file = null;
                $this->isUploading = false;

            } catch (\Exception $e) {
                DB::rollBack();

                if ($import) {
                    $import->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }

                Log::error('Upload import gagal: '.$e->getMessage(), [
                    'file' => optional($this->file)->getClientOriginalName(),
                    'rows' => count($transactions),
                ]);

                throw new \Exception('Gagal menyimpan data: '.$e->getMessage());
            }

        } catch (ValidationException $e) {
            $this->isUploading = false;
            $this->uploadError = $e->errors()['file'][0] ?? 'Validasi file gagal';
        } catch (\Exception $e) {
            $this->isUploading = false;
            Log::error('Upload import exception: '.$e->getMessage(), [
                'file' => optional($this->file)->getClientOriginalName(),
            ]);
            $this->uploadError = $e->getMessage();
        }
    }

    private function parseDecimal($value)
    {
        if (empty($value)) {
            return 0;
        }

        $value = trim($value);
        // Kalau ada titik dan koma, yang belakang adalah decimal separator
        if (strpos($value, '.') !== false && strpos($value, ',') !== false) {
            // 1.000.000,50 format
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            // 1000,50 format
            $value = str_replace(',', '.', $value);
        }

        return (float) $value;
    }

    private function detectAccountType(string $code): string
    {
        $firstDigit = substr(trim($code), 0, 1);

        return match ($firstDigit) {
            '1' => 'asset',
            '2' => 'liability',
            '3' => 'equity',
            '4' => 'income',
            '5', '6', '7', '8', '9' => 'expense',
            default => 'expense',
        };
    }

    public function downloadTemplate()
    {
        $headers = ['account_code', 'account_name', 'transaction_number', 'transaction_date', 'transaction_type', 'description', 'debit', 'credit'];

        $filename = 'template_import_'.date('Ymd_His').'.csv';
        $handle = fopen('php://memory', 'w');

        fputcsv($handle, $headers);
        fputcsv($handle, ['ACC001', 'Rekening Utama', 'TRX001', '01/06/2026', 'Debit', 'Contoh transaksi', '1000000', '0']);

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function render()
    {
        return view('livewire.upload.index')
            ->layout('layouts.app');
    }
}
