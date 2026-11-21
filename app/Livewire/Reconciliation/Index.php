<?php

namespace App\Livewire\Reconciliation;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationEntry;
use App\Services\ReconciliationMatcher;
use App\Services\ReconciliationExcelImporter;
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