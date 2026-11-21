<?php

namespace App\Livewire\Reconciliation;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationEntry;
use App\Services\ReconciliationMatcher;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class Index extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $bankName = '';
    public ?int $batchId = null;
    public $bankFile;
    public $ledgerFile;

    public function createBatch(): void
    {
        $data = $this->validate([
            'name'=>'required|string|max:150',
            'bankName'=>'nullable|string|max:100',
        ]);
        $batch = ReconciliationBatch::create([
            'user_id'=>auth()->id(),
            'name'=>$data['name'],
            'bank_name'=>$data['bankName'],
        ]);
        $this->batchId = $batch->id;
        $this->reset('name','bankName');
        session()->flash('success','Periode rekonsiliasi dibuat.');
    }

    public function selectBatch(int $id): void
    {
        $this->ownedBatch($id);
        $this->batchId = $id;
    }

    private function ownedBatch(?int $id = null): ReconciliationBatch
    {
        return ReconciliationBatch::where('user_id', auth()->id())->findOrFail($id ?? $this->batchId);
    }

    public function import(string $source): void
    {
        abort_unless(in_array($source, ['bank','ledger'], true), 404);
        $batch = $this->ownedBatch();
        $key = $source === 'bank' ? 'bankFile' : 'ledgerFile';
        $this->validate([$key => 'required|file|mimes:csv,txt,xlsx,xls|max:5120']);
        $file = $this->{$key};
        $spreadsheet = IOFactory::load($file->getRealPath());
        try {
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
            if (count($rows) > 10001) {
                $this->addError($key, 'Maksimal 10.000 baris per upload.');
                return;
            }
            $headers = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($rows) ?? []);
            $required = ['tanggal','keterangan','masuk','keluar'];
            foreach ($required as $column) {
                if (!in_array($column, $headers, true)) {
                    $this->addError($key, 'Kolom wajib: tanggal, keterangan, masuk, keluar. Opsional: referensi.');
                    return;
                }
            }
            $columns = array_flip($headers);
            $insert = [];
            $errors = [];
            foreach ($rows as $index => $row) {
                if (count(array_filter($row, fn ($v) => $v !== null && trim((string) $v) !== '')) === 0) continue;
                try {
                    $rawDate = $row[$columns['tanggal']] ?? null;
                    $date = is_numeric($rawDate)
                        ? \Carbon\Carbon::instance(ExcelDate::excelToDateTimeObject((float) $rawDate))->toDateString()
                        : \Carbon\Carbon::createFromFormat('!d/m/Y', trim((string) $rawDate))->toDateString();
                    $money = function ($value) {
                        $v = trim((string) ($value ?? '0'));
                        $v = str_replace(['Rp','rp',' '], '', $v);
                        if (str_contains($v, ',') && str_contains($v, '.')) {
                            $v = str_replace('.', '', $v);
                            $v = str_replace(',', '.', $v);
                        } elseif (str_contains($v, ',')) {
                            $v = str_replace(',', '.', $v);
                        }
                        if (!is_numeric($v)) throw new \InvalidArgumentException('Nominal tidak valid');
                        return round((float) $v, 2);
                    };
                    $in = $money($row[$columns['masuk']] ?? 0);
                    $out = $money($row[$columns['keluar']] ?? 0);
                    if ($in < 0 || $out < 0 || ($in > 0 && $out > 0) || ($in == 0 && $out == 0)) {
                        throw new \InvalidArgumentException('Isi salah satu kolom masuk atau keluar dengan nominal positif');
                    }
                    $description = trim((string) ($row[$columns['keterangan']] ?? ''));
                    $reference = isset($columns['referensi']) ? trim((string) ($row[$columns['referensi']] ?? '')) : '';
                    $direction = $in > 0 ? 'in' : 'out';
                    $amount = number_format($in > 0 ? $in : $out, 2, '.', '');
                    $insert[] = [
                        'batch_id'=>$batch->id,'source'=>$source,'posted_at'=>$date,
                        'reference'=>substr($reference,0,255),'description'=>$description,
                        'direction'=>$direction,'amount'=>$amount,
                        'fingerprint'=>hash('sha256',implode('|',[$source,$date,$reference,$description,$direction,$amount])),
                        'status'=>'unmatched','created_at'=>now(),'updated_at'=>now(),
                    ];
                } catch (\Throwable $e) {
                    $errors[] = 'Baris '.($index+2).': '.$e->getMessage();
                    if (count($errors) >= 5) break;
                }
            }
            if ($errors) {
                $this->addError($key, implode(' | ', $errors).' (tidak ada data yang disimpan)');
                return;
            }
            if (!$insert) {
                $this->addError($key,'Tidak ada transaksi valid dalam file.');
                return;
            }
            DB::transaction(function () use ($batch,$source,$insert) {
                // Replace one source atomically; clearing all suggestions to avoid stale links.
                $batch->entries()->where('status','!=','matched')->update(['matched_entry_id'=>null,'status'=>'unmatched','confidence'=>null]);
                if ($batch->entries()->where('source',$source)->where('status','matched')->exists()) {
                    throw new \DomainException('Data sudah memiliki transaksi yang disetujui. Buat periode baru untuk upload ulang.');
                }
                $batch->entries()->where('source',$source)->delete();
                foreach (array_chunk($insert,500) as $chunk) ReconciliationEntry::insert($chunk);
            });
            $this->reset($key);
            session()->flash('success',count($insert).' transaksi '.($source === 'bank' ? 'bank' : 'pembukuan').' diimpor.');
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    public function runMatching(ReconciliationMatcher $matcher): void
    {
        $matcher->run($this->ownedBatch());
        session()->flash('success','Pencocokan selesai. Konfirmasi hasil yang disarankan.');
    }

    public function decide(int $entryId, bool $approve, ReconciliationMatcher $matcher): void
    {
        $entry = $this->ownedBatch()->entries()->findOrFail($entryId);
        $matcher->decide($entry, $approve);
        session()->flash('success',$approve ? 'Transaksi disetujui.' : 'Saran ditolak.');
    }

    public function render()
    {
        $batches = ReconciliationBatch::where('user_id',auth()->id())->latest()->get();
        $batch = $this->batchId ? $this->ownedBatch() : null;
        $entries = $batch ? $batch->entries()->with('match')->where('source','bank')->orderByDesc('posted_at')->paginate(25) : null;
        $counts = $batch ? $batch->entries()->where('source','bank')->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total','status') : collect();
        return view('livewire.reconciliation.index',compact('batches','batch','entries','counts'))->layout('layouts.app');
    }
}