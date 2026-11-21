<?php

namespace App\Livewire\Reconciliation;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationEntry;
use App\Services\ReconciliationMatcher;
use App\Services\ReconciliationExcelImporter;
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