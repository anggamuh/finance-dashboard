<?php

namespace Tests\Unit;

use App\Services\BankStatementPdfParser;
use PHPUnit\Framework\TestCase;

class BcaStatementLayoutTest extends TestCase
{
    private function statement(string $rows, string $closing = '1,300.00'): string
    {
        return "Mutasi Rekening\nDetail Mutasi Total Transaksi : 3\n"
            ."No. Tanggal Efektif Tanggal Transaksi Keterangan Cabang No Referensi Debit Kredit Saldo\n"
            .$rows."\nSaldo Awal 1,000.00\nMutasi Kredit 500.00\nMutasi Debit 200.00\nSaldo Akhir {$closing}\n";
    }

    public function test_bca_multiline_statement_requires_correct_balances(): void
    {
        $rows = "1 03/10/2026 03/10/2026 TRSF E-BANKING DB 0000 27098112101 100.00 - 900.00\n"
            ." 0310/FTFVA/WS95321\n 80777/MERCHANT\n"
            ."2 04/10/2026 04/10/2026 BI-FAST CR 0000 516031434 - 500.00 1,400.00\n"
            ." BIF TRANSFER DR\n RECIPIENT\n"
            ."3 05/10/2026 05/10/2026 BIAYA ADM 0998 000000000 100.00 - 1,300.00\n";
        $result = (new BankStatementPdfParser)->parse($this->statement($rows));
        $this->assertTrue($result['ready'], json_encode($result['unrecognized']));
        $this->assertCount(3, $result['entries']);
        $this->assertSame('in',$result['entries'][1]['direction']);
        $this->assertSame('500.00',$result['entries'][1]['amount']);
        $this->assertSame('516031434',$result['entries'][1]['reference']);
    }

    public function test_bca_text_with_one_element_per_line_is_supported(): void
    {
        $rows = "1\n03/10/2026\n03/10/2026\nTRSF E-BANKING DB\nTRANSFER DETAIL\n0000\n27098112101\n100.00\n-\n900.00\n"
            ."2\n04/10/2026\n04/10/2026\nBI-FAST CR\nSENDER\n0000\n516031434\n-\n500.00\n1,400.00\n"
            ."3\n05/10/2026\n05/10/2026\nBIAYA ADM\n0998\n000000000\n100.00\n-\n1,300.00\n";
        $result = (new BankStatementPdfParser)->parse($this->statement($rows));
        $this->assertTrue($result['ready'], json_encode($result['unrecognized']));
        $this->assertSame('TRSF E-BANKING DB TRANSFER DETAIL',$result['entries'][0]['description']);
    }

    public function test_does_not_allow_import_when_statement_totals_mismatch(): void
    {
        $rows = "1 03/10/2026 03/10/2026 TRANSFER 0000 27098112101 100.00 - 900.00\n"
            ."2 04/10/2026 04/10/2026 BI-FAST CR 0000 516031434 - 500.00 1,400.00\n"
            ."3 05/10/2026 05/10/2026 BIAYA ADM 0998 000000000 100.00 - 1,300.00\n";
        $result = (new BankStatementPdfParser)->parse($this->statement($rows,'1,500.00'));
        $this->assertFalse($result['ready']);
        $this->assertNotEmpty($result['unrecognized']);
    }
}
