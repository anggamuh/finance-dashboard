<?php

namespace App\Livewire\BankLedger;

use App\Models\BankEntry;
use App\Services\BankWorkbookImporter;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $workbook;
    public string $ledger = 'BCA';
    public int $month;
    public int $year;
    public string $search = '';
    public string $entry_date = '';
    public string $document_number = '';
    public string $account_code = '';
    public string $account_name = '';
    public string $invoice = '';
    public string $payment_channel = 'TRANSFER';
    public string $description = '';
    public $debit = 0;
    public $credit = 0;
    public $opening = 0;
    public ?int $editingId = null;
    public ?array $preview = null;

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->entry_date = now()->toDateString();
        $this->loadOpening();
    }

    public function updatedLedger(): void { $this->loadOpening(); }
    public function updatedMonth(): void { $this->loadOpening(); }
    public function updatedYear(): void { $this->loadOpening(); }

    public function loadOpening(): void
    {
        $this->opening = (float) DB::table('bank_ledger_openings')
            ->where('ledger', $this->ledger)->where('year', $this->year)
            ->where('month', $this->month)->value('amount');
    }

    public function saveOpening(): void
    {
        $this->validate([
            'ledger' => ['required', Rule::in(['KAS', 'BCA', 'MANDIRI'])],
            'year' => 'required|integer|between:2000,2100',
            'month' => 'required|integer|between:1,12',
            'opening' => 'required|numeric',
        ]);
        DB::table('bank_ledger_openings')->updateOrInsert(
            ['ledger' => $this->ledger, 'year' => $this->year, 'month' => $this->month],
            ['amount' => $this->opening, 'updated_at' => now(), 'created_at' => now()]
        );
        session()->flash('success', 'Saldo awal periode tersimpan.');
    }

    public function previewImport(BankWorkbookImporter $importer): void
    {
        $this->validate(['workbook' => 'required|file|mimes:xlsx,xls|max:10240']);
        $parsed = $importer->parse($this->workbook->getRealPath());
        $this->preview = [
            'total' => count($parsed['rows']),
            'by_ledger' => collect($parsed['rows'])->countBy('ledger')->all(),
            'skipped' => $parsed['skipped'],
            'warnings' => $parsed['warnings'],
        ];
    }

    public function import(BankWorkbookImporter $importer): void
    {
        $this->validate(['workbook' => 'required|file|mimes:xlsx,xls|max:10240']);
        $result = $importer->import($this->workbook->getRealPath());
        $this->reset('workbook', 'preview');
        session()->flash('success', "Import selesai: {$result['inserted']} baru, {$result['duplicates']} dilewati (identik), {$result['skipped']} baris kosong/tidak valid.");
    }

    public function edit(int $id): void
    {
        $row = BankEntry::findOrFail($id);
        foreach (['ledger', 'document_number', 'account_code', 'account_name', 'invoice', 'payment_channel', 'description', 'debit', 'credit'] as $field) {
            $this->{$field} = $row->{$field} ?? '';
        }
        $this->entry_date = $row->entry_date->toDateString();
        $this->editingId = $row->id;
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'document_number', 'account_code', 'account_name', 'invoice', 'description', 'debit', 'credit');
        $this->entry_date = now()->toDateString();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'ledger' => ['required', Rule::in(['KAS', 'BCA', 'MANDIRI'])],
            'entry_date' => 'required|date',
            'document_number' => 'nullable|string|max:100',
            'account_code' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:255',
            'invoice' => 'nullable|string|max:255',
            'payment_channel' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'debit' => 'required|numeric|min:0',
            'credit' => 'required|numeric|min:0',
        ]);
        if (((float) $this->debit <= 0 && (float) $this->credit <= 0)
            || ((float) $this->debit > 0 && (float) $this->credit > 0)) {
            $this->addError('debit', 'Isi salah satu: debit (masuk) atau kredit (keluar).');
            return;
        }
        $validated['fingerprint'] = BankEntry::fingerprintFor($validated);
        $existing = BankEntry::query()->where('fingerprint', $validated['fingerprint'])
            ->when($this->editingId, fn ($q) => $q->whereKeyNot($this->editingId))->exists();
        if ($existing) {
            $this->addError('document_number', 'Transaksi identik sudah tersimpan.');
            return;
        }
        $validated['source'] = 'manual';
        if ($this->editingId) {
            BankEntry::findOrFail($this->editingId)->update($validated);
        } else {
            BankEntry::create($validated);
        }
        $this->resetForm();
        session()->flash('success', 'Transaksi berhasil disimpan.');
    }

    public function remove(int $id): void
    {
        BankEntry::findOrFail($id)->delete();
        session()->flash('success', 'Transaksi dihapus.');
    }

    public function exportCsv()
    {
        $ledger = $this->ledger;
        $month = $this->month;
        $year = $this->year;
        $rows = $this->queryRows()->get();
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['TGL','DBACC','NAMA AKUN','INVOICE','NO TRANSAKSI','BANK','URAIAN','DEBIT','KREDIT']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->entry_date->format('d/m/Y'), $row->account_code, $row->account_name,
                    $row->invoice, $row->document_number, $row->payment_channel,
                    $row->description, $row->debit, $row->credit,
                ]);
            }
            fclose($out);
        }, "kas-bank-{$ledger}-{$year}-".str_pad($month, 2, '0', STR_PAD_LEFT).".csv");
    }

    private function queryRows()
    {
        return BankEntry::query()
            ->where('ledger', $this->ledger)
            ->whereYear('entry_date', $this->year)
            ->whereMonth('entry_date', $this->month)
            ->orderBy('entry_date')->orderBy('id');
    }

    public function render()
    {
        $all = $this->queryRows()->get();
        $balance = (float) $this->opening;
        $entries = $all->map(function ($row) use (&$balance) {
            $balance += (float) $row->debit - (float) $row->credit;
            $row->running_balance = $balance;
            return $row;
        });
        $keyword = mb_strtolower(trim($this->search));
        if ($keyword !== '') {
            $entries = $entries->filter(fn ($row) => str_contains(mb_strtolower(
                $row->document_number.' '.$row->account_code.' '.$row->account_name.' '.$row->invoice.' '.$row->description
            ), $keyword));
        }
        return view('livewire.bank-ledger.index', [
            'rows' => $entries,
            'totalDebit' => $all->sum('debit'),
            'totalCredit' => $all->sum('credit'),
            'ending' => $balance,
        ])->layout('layouts.app');
    }
}
