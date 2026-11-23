<?php

namespace Tests\Feature;

use App\Models\BankEntry;
use App\Services\BankWorkbookImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class BankLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_document_number_in_different_months_is_allowed(): void
    {
        $base = [
            'ledger' => 'MANDIRI',
            'document_number' => 'M26K-010',
            'account_code' => '81114',
            'invoice' => '',
            'description' => 'Admin Transfer',
            'debit' => 0,
            'credit' => 2500,
        ];
        $first = $base + ['entry_date' => '2026-09-07'];
        $second = $base + ['entry_date' => '2026-10-07'];
        $this->assertNotSame(BankEntry::fingerprintFor($first), BankEntry::fingerprintFor($second));
        BankEntry::create($first + ['fingerprint' => BankEntry::fingerprintFor($first)]);
        BankEntry::create($second + ['fingerprint' => BankEntry::fingerprintFor($second)]);
        $this->assertDatabaseCount('bank_entries', 2);
    }

    public function test_workbook_import_is_idempotent_and_ignores_summary_sheet(): void
    {
        $book = new Spreadsheet();
        $book->getActiveSheet()->setTitle('GABUNGAN');
        $book->getActiveSheet()->setCellValue('J3', 999999);
        $bca = $book->createSheet();
        $bca->setTitle('BCA');
        $bca->fromArray(['LINE','DBACC','NAMA','INVOICE','TGL','No. Bank','BANK','NO. CK/ BG','URAIAN','DEBIT','KREDIT','SALDO'], null, 'A2');
        $bca->fromArray([null, 113, 'POS SILANG', 'PINDAH DANA', 46302, 'B26M-001', 'TRANSFER', null, 'Saldo Masuk', 89200, null, 89200], null, 'A3');

        $mandiri = $book->createSheet();
        $mandiri->setTitle('MANDIRI');
        $mandiri->fromArray(['LINE','DBACC','NAMA','INVOICE','TGL','No. Bank','BANK','NO. CK/ BG','URAIAN','DEBIT','KREDIT','SALDO'], null, 'A1');
        $mandiri->fromArray([null, 81114, 'BIAYA ADM BANK', null, 46302, 'M26K-001', 'TRANSFER', null, 'Admin', null, 2500, 100000], null, 'A2');

        $kas = $book->createSheet();
        $kas->setTitle('KAS');
        $kas->fromArray(['DBACC','NAMA','INVOICE','TGL','No. Bank','BANK','NO. CK/ BG','URAIAN','DEBIT','KREDIT','SALDO'], null, 'A2');
        $kas->fromArray([51,'MODAL',null,46266,'2026-IX-I','TUNAI',null,'PENERIMAAN',500000000,null,500000000], null, 'A3');

        $path = tempnam(sys_get_temp_dir(), 'bank-import-').'.xlsx';
        (new Xlsx($book))->save($path);
        try {
            $importer = app(BankWorkbookImporter::class);
            $first = $importer->import($path);
            $second = $importer->import($path);
            $this->assertSame(3, $first['inserted']);
            $this->assertSame(0, $second['inserted']);
            $this->assertSame(3, $second['duplicates']);
            $this->assertDatabaseCount('bank_entries', 3);
        } finally {
            @unlink($path);
            $book->disconnectWorksheets();
        }
    }
}
