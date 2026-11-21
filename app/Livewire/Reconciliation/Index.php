<?php

namespace App\Livewire\Reconciliation;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationEntry;
use App\Services\ReconciliationMatcher;
use App\Services\ReconciliationExcelImporter;
use App\Services\BcaLedgerWorkbookImporter;
use App\Services\BankStatementPdfParser;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $name = '';
    public string $bankName = '';
    public ?int $batchId = null;
    public $bankFile;
    public $ledgerFile;
    public $bankPdf;
    public array $manualLedger = [];
    public array $pdfPreview = [];
    public array $pdfUnrecognized = [];
    public int $headerRow = 1;
    public string $importMode = 'separated';
    public string $sheetName = '';

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
        $this->reset('bankPdf','pdfPreview','pdfUnrecognized');
        $this->resetPage();
    }

    private function ownedBatch(?int $id = null): ReconciliationBatch
    {
        return ReconciliationBatch::where('user_id', auth()->id())->findOrFail($id ?? $this->batchId);
    }

    public function import(string $source, ReconciliationExcelImporter $importer): void
    {
        abort_unless(in_array($source, ['bank','ledger'], true), 404);
        $batch = $this->ownedBatch();
        $key = $source === 'bank' ? 'bankFile' : 'ledgerFile';
        $this->validate([
            $key => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
            'headerRow' => 'required|integer|between:1,30',
            'importMode' => 'required|in:separated,signed,typed',
            'sheetName' => 'nullable|string|max:100',
        ]);
        try {
            $entries = $importer->read(
                $this->{$key}->getRealPath(),
                $this->headerRow,
                $this->importMode,
                trim($this->sheetName) ?: null,
            );
            $count = $importer->replace($batch, $source, $entries);
            $this->reset($key);
            session()->flash('success', $count.' transaksi '.($source === 'bank' ? 'bank' : 'pembukuan').' diimpor dari Excel.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->implode(' | ');
            $this->addError($key, $message);
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError($key, 'File tidak bisa diproses. Periksa format Excel dan coba lagi.');
        }
    }

    public function importBcaWorkbook(BcaLedgerWorkbookImporter $reader, ReconciliationExcelImporter $importer): void
    {
        $batch = $this->ownedBatch();
        $this->validate(['ledgerFile'=>'required|file|mimes:xlsx,xls|max:5120']);
        $bank = $batch->entries()->where('source','bank');
        if (!(clone $bank)->exists()) {
            $this->addError('ledgerFile','Upload dan simpan PDF mutasi BCA terlebih dahulu agar periode Excel bisa ditentukan.');
            return;
        }

        $from = (clone $bank)->min('posted_at');
        $to = (clone $bank)->max('posted_at');
        try {
            $rows = $reader->read($this->ledgerFile->getRealPath(), $from, $to);
            $count = $importer->replace($batch, 'ledger', $rows);
            $this->reset('ledgerFile');
            session()->flash('success', $count.' transaksi dari sheet BCA berhasil diimpor. Periode: '.$from.' sampai '.$to.'. Jalankan Auto Matching.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->addError('ledgerFile', collect($exception->errors())->flatten()->implode(' | '));
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('ledgerFile','Sheet BCA gagal diproses. Pastikan kolom dan tanggal sesuai format buku bank.');
        }
    }

    public function previewBankPdf(BankStatementPdfParser $parser): void
    {
        $batch = $this->ownedBatch();
        session()->forget('reconciliation_pdf_preview_'.auth()->id().'_'.$batch->id);
        $this->reset('pdfPreview','pdfUnrecognized');
        $this->validate(['bankPdf'=>'required|file|mimes:pdf|max:10240']);
        try {
            $result = $parser->extract($this->bankPdf->getRealPath());
            $this->pdfUnrecognized = array_slice($result['unrecognized'],0,20);
            // Avoid storing a large financial statement inside Livewire's browser state.
            if (count($result['entries']) > 300) {
                $this->addError('bankPdf','Lebih dari 300 transaksi. Untuk keamanan, ekspor sebagai Excel/CSV atau pecah PDF per periode.');
                return;
            }
            $this->pdfPreview = $result['entries'];
            if (!$result['ready']) {
                $this->pdfPreview = [];
                $this->addError('bankPdf','Sebagian baris transaksi tidak dikenali, atau format PDF belum didukung. Data tidak akan diimpor. Gunakan Excel/CSV atau sesuaikan parser bank.');
            } else {
                // Store the verified payload server-side rather than trusting editable Livewire state.
                session()->put('reconciliation_pdf_preview_'.auth()->id().'_'.$batch->id, $result['entries']);
            }
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->addError('bankPdf', collect($exception->errors())->flatten()->implode(' | '));
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('bankPdf','PDF tidak bisa diproses. Pastikan format teks bank didukung.');
        }
    }

    public function confirmBankPdf(ReconciliationExcelImporter $importer): void
    {
        $batch = $this->ownedBatch();
        $previewKey = 'reconciliation_pdf_preview_'.auth()->id().'_'.$batch->id;
        $verifiedEntries = session()->get($previewKey, []);
        if (count($verifiedEntries) === 0 || count($verifiedEntries) > 300 || $this->pdfUnrecognized) {
            $this->addError('bankPdf','Tidak ada preview valid untuk disimpan.');
            return;
        }
        // Validate preview data supplied by the Livewire client before committing.
        foreach ($verifiedEntries as $row) {
            if (!is_array($row) ||
                !isset($row['posted_at'],$row['description'],$row['reference'],$row['direction'],$row['amount']) ||
                !in_array($row['direction'], ['in','out'], true) ||
                !preg_match('/^\d{4}-\d{2}-\d{2}$/',(string)$row['posted_at']) ||
                !is_numeric($row['amount']) || $row['amount'] <= 0) {
                $this->addError('bankPdf','Data preview tidak valid, upload ulang PDF.');
                return;
            }
        }
        try {
            $total = $importer->replace($batch,'bank',$verifiedEntries);
            session()->forget($previewKey);
            $this->reset('bankPdf','pdfPreview','pdfUnrecognized');
            session()->flash('success',"$total transaksi PDF disimpan. Lanjutkan auto matching dan review.");
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->addError('bankPdf', collect($exception->errors())->flatten()->implode(' | '));
        }
    }

    public function runMatching(ReconciliationMatcher $matcher): void
    {
        $matcher->run($this->ownedBatch());
        session()->flash('success','Pencocokan selesai. Konfirmasi hasil yang disarankan.');
    }

    public function proposeManual(int $bankId, ReconciliationMatcher $matcher): void
    {
        $batch = $this->ownedBatch();
        $ledgerId = filter_var($this->manualLedger[$bankId] ?? null, FILTER_VALIDATE_INT);
        if (!$ledgerId) {
            $this->addError('manualLedger.'.$bankId, 'Pilih pasangan Excel dulu.');
            return;
        }

        try {
            $bank = $batch->entries()->where('source','bank')->findOrFail($bankId);
            // Service validates the selected ledger row belongs to the same batch.
            $matcher->proposeManual($bank, $ledgerId);
            unset($this->manualLedger[$bankId]);
            session()->flash('success', 'Pasangan manual diajukan. Klik Setujui setelah diperiksa.');
        } catch (\DomainException $exception) {
            $this->addError('manualLedger.'.$bankId, $exception->getMessage());
        }
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
        $bankCount = $batch ? $batch->entries()->where('source','bank')->count() : 0;
        $ledgerCount = $batch ? $batch->entries()->where('source','ledger')->count() : 0;
        $ledgerRows = collect();
        $diagnostics = [];
        $manualOptions = [];
        if ($entries && $ledgerCount > 0) {
            // Diagnostics and candidate options are computed only for the current page.
            $bankPage = $entries->getCollection();
            $amounts = $bankPage->pluck('amount')->unique()->values()->all();
            $ledgerRows = $batch->entries()->where('source','ledger')
                ->whereIn('amount',$amounts)->get();
            foreach ($bankPage as $bankRow) {
                $diagnostics[$bankRow->id] = app(ReconciliationMatcher::class)->diagnose($bankRow, $ledgerRows);
                $manualOptions[$bankRow->id] = $ledgerRows->filter(fn ($row) =>
                    $row->status === 'unmatched' &&
                    $row->direction === $bankRow->direction &&
                    (string)$row->amount === (string)$bankRow->amount
                )->take(30)->map(fn ($row) => [
                    'id'=>$row->id,
                    'label'=>$row->posted_at->format('d/m/Y').' | '.mb_strimwidth($row->description ?? '-',0,45,'…').' | Rp '.number_format((float)$row->amount,0,',','.'),
                ])->values()->all();
            }
        } elseif ($entries) {
            foreach ($entries as $bankRow) {
                $diagnostics[$bankRow->id] = app(ReconciliationMatcher::class)->diagnose($bankRow, collect());
            }
        }
        return view('livewire.reconciliation.index',compact(
            'batches','batch','entries','counts','bankCount','ledgerCount','diagnostics','manualOptions'
        ))->layout('layouts.app');
    }
}