<?php

namespace Tests\Feature;

use App\Models\ReconciliationBatch;
use App\Models\User;
use App\Services\ReconciliationExcelImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ReconciliationExcelImporterTest extends TestCase
{
    use RefreshDatabase;

    private function makeSpreadsheet(array $rows): string
    {
        $sheet = new Spreadsheet();
        $sheet->getActiveSheet()->fromArray($rows);
        $path = tempnam(sys_get_temp_dir(), 'recon-excel-').'.xlsx';
        (new Xlsx($sheet))->save($path);
        $sheet->disconnectWorksheets();
        return $path;
    }

    public function test_imports_excel_with_indonesian_headers_and_thousand_separator(): void
    {
        $path = $this->makeSpreadsheet([
            ['LAPORAN BANK'],
            ['Tanggal', 'Keterangan', 'Penerimaan', 'Pengeluaran', 'No Bukti'],
            ['01/11/2026', 'Pembayaran customer', '1.250.000', '', 'BNK-1'],
            ['02/11/2026', 'Biaya admin', '', '7.500', 'BNK-2'],
        ]);

        try {
            $importer = app(ReconciliationExcelImporter::class);
            $entries = $importer->read($path, 2, 'separated');
            $this->assertCount(2, $entries);
            $this->assertSame('1250000.00', $entries[0]['amount']);
            $this->assertSame('in', $entries[0]['direction']);
            $this->assertSame('out', $entries[1]['direction']);
            $batch = ReconciliationBatch::create(['user_id'=>User::factory()->create()->id,'name'=>'November']);
            $this->assertSame(2, $importer->replace($batch, 'bank', $entries));
            $this->assertSame(2, $batch->entries()->count());
        } finally {
            @unlink($path);
        }
    }

    public function test_signed_amounts_are_supported(): void
    {
        $path = $this->makeSpreadsheet([
            ['Tgl','Uraian','Nominal'],
            ['01/11/2026','Keluar','-20000'],
            ['02/11/2026','Masuk','30000'],
        ]);
        try {
            $entries = app(ReconciliationExcelImporter::class)->read($path,1,'signed');
            $this->assertSame('out',$entries[0]['direction']);
            $this->assertSame('in',$entries[1]['direction']);
        } finally {
            @unlink($path);
        }
    }

    public function test_invalid_rows_are_rejected_atomically(): void
    {
        $path = $this->makeSpreadsheet([
            ['Tanggal','Keterangan','Masuk','Keluar'],
            ['01/11/2026','Valid',10000,''],
            ['invalid','Wrong',1000,''],
        ]);
        try {
            $this->expectException(ValidationException::class);
            app(ReconciliationExcelImporter::class)->read($path);
        } finally {
            @unlink($path);
        }
    }
}
