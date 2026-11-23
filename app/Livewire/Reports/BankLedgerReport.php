<?php

namespace App\Livewire\Reports;

use App\Models\BankEntry;
use Livewire\Component;

class BankLedgerReport extends Component
{
    public string $dateFrom;
    public string $dateTo;
    public string $ledger = 'ALL';
    public string $search = '';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->endOfMonth()->toDateString();
    }

    private function rows()
    {
        return BankEntry::query()
            ->whereBetween('entry_date', [$this->dateFrom, $this->dateTo])
            ->when($this->ledger !== 'ALL', fn($q) => $q->where('ledger', $this->ledger))
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($w) use ($term) {
                    $w->where('description', 'like', $term)
                      ->orWhere('account_code', 'like', $term)
                      ->orWhere('account_name', 'like', $term)
                      ->orWhere('document_number', 'like', $term)
                      ->orWhere('invoice', 'like', $term);
                });
            });
    }

    public function exportCsv()
    {
        $rows = $this->rows()->orderBy('entry_date')->orderBy('id')->get();
        return response()->streamDownload(function () use ($rows) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Tanggal', 'Rekening', 'No Transaksi', 'Kode Akun', 'Nama Akun', 'Invoice', 'Uraian', 'Masuk (Debit)', 'Keluar (Kredit)']);
            foreach ($rows as $row) {
                fputcsv($file, [
                    $row->entry_date->format('d/m/Y'), $row->ledger, $row->document_number,
                    $row->account_code, $row->account_name, $row->invoice, $row->description,
                    $row->debit, $row->credit
                ]);
            }
            fclose($file);
        }, 'laporan-kas-bank-'.$this->dateFrom.'-'.$this->dateTo.'.csv');
    }

    public function render()
    {
        $query = $this->rows();
        $totalDebit = (clone $query)->sum('debit');
        $totalCredit = (clone $query)->sum('credit');
        $byLedger = (clone $query)->selectRaw('ledger, COUNT(*) AS total, SUM(debit) AS masuk, SUM(credit) AS keluar')
            ->groupBy('ledger')->orderBy('ledger')->get();
        $byAccount = (clone $query)->selectRaw('account_code, account_name, COUNT(*) AS total, SUM(debit) AS masuk, SUM(credit) AS keluar')
            ->groupBy('account_code', 'account_name')->orderBy('account_code')->get();
        $rows = $query->orderBy('entry_date')->orderBy('id')->limit(300)->get();
        return view('livewire.reports.bank-ledger', compact('rows', 'byLedger', 'byAccount', 'totalDebit', 'totalCredit'))
            ->layout('layouts.app');
    }
}
