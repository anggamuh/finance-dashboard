<?php

namespace Tests\Feature;

use App\Models\ReconciliationBatch;
use App\Models\User;
use App\Services\BcaLedgerWorkbookImporter;
use App\Services\ReconciliationExcelImporter;
use App\Services\ReconciliationMatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class BcaLedgerWorkbookImporterTest extends TestCase
{
    use RefreshDatabase;

    private function workbook(): string
    {
        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('GABUNGAN');
        $sheet = $book->createSheet();
        $sheet->setTitle('BCA');
        foreach (['E'=>'TGL','F'=>'No. Bank','I'=>'URAIAN','J'=>'DEBIT','K'=>'KREDIT','L'=>'SALDO'] as $column => $name) {
            $sheet->setCellValue($column.'2', $name);
        }
        // Excel date serial numbers, as in the actual company workbook.
        $sheet->setCellValue('E3', 46302);
        $sheet->setCellValue('F3', 'B26M-001');
        $sheet->setCellValue('I3', 'Saldo Masuk');
        $sheet->setCellValue('J3', 89200);
        $sheet->setCellValue('E4', 46302);
        $sheet->setCellValue('F4', 'B26M-002');
        $sheet->setCellValue('I4', 'Saldo Masuk');
        $sheet->setCellValue('J4', 57454275);
        $sheet->setCellValue('E5', 46302);
        $sheet->setCellValue('I5', 'Jasa DTF');
        $sheet->setCellValue('K5', 6626250);
        $sheet->setCellValue('E6', 46328); // November, must be excluded.
        $sheet->setCellValue('J6', 5885000);
        $path = tempnam(sys_get_temp_dir(),'bca-test-').'.xlsx';
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();

        return $path;
    }

    public function test_imports_only_bca_sheet_within_pdf_dates_and_matches_cash_flow_direction(): void
    {
        $path = $this->workbook();
        try {
            $rows = app(BcaLedgerWorkbookImporter::class)->read($path, '2026-10-03','2026-10-30');
            $this->assertCount(3,$rows);
            $this->assertSame('57454275.00',$rows[1]['amount']);
            $this->assertSame('in',$rows[1]['direction']);
            $this->assertSame('out',$rows[2]['direction']);
            $this->assertSame('2026-10-07',$rows[2]['posted_at']);

            $batch = ReconciliationBatch::create(['user_id'=>User::factory()->create()->id,'name'=>'Oct']);
            $importer = app(ReconciliationExcelImporter::class);
            $importer->replace($batch,'ledger',$rows);
            $importer->replace($batch,'bank',[
                ['posted_at'=>'2026-10-07','reference'=>'516031434','description'=>'BI-FAST CR','direction'=>'in','amount'=>'57454275.00'],
                ['posted_at'=>'2026-10-07','reference'=>'32001733242','description'=>'Transfer','direction'=>'out','amount'=>'6626250.00'],
            ]);
            app(ReconciliationMatcher::class)->run($batch);
            $this->assertSame(2,$batch->entries()->where('source','bank')->where('status','suggested')->count());
            $this->assertSame(1,$batch->entries()->where('source','ledger')->where('status','unmatched')->count());
        } finally {
            @unlink($path);
        }
    }
}
