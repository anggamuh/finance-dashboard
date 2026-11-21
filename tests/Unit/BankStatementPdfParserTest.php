<?php

namespace Tests\Unit;

use App\Services\BankStatementPdfParser;
use PHPUnit\Framework\TestCase;

class BankStatementPdfParserTest extends TestCase
{
    public function test_extracts_clear_debit_credit_lines(): void
    {
        $result = (new BankStatementPdfParser)->parse(
            "MUTASI REKENING\n01/11/2026 TRANSFER DARI CUSTOMER 1.250.000,00 CR\n02/11/2026 BIAYA ADMIN 7.500,00 DB"
        );
        $this->assertTrue($result['ready']);
        $this->assertCount(2,$result['entries']);
        $this->assertSame('1250000.00',$result['entries'][0]['amount']);
        $this->assertSame('in',$result['entries'][0]['direction']);
        $this->assertSame('out',$result['entries'][1]['direction']);
    }

    public function test_rejects_partial_date_bearing_rows(): void
    {
        $result = (new BankStatementPdfParser)->parse(
            "01/11/2026 TRANSFER 100.000 CR\n02/11/2026 BARIS FORMAT BANK TIDAK DIKENALI"
        );
        $this->assertFalse($result['ready']);
        $this->assertCount(1,$result['entries']);
        $this->assertCount(1,$result['unrecognized']);
    }

    public function test_does_not_guess_multi_column_balance_tables(): void
    {
        $result = (new BankStatementPdfParser)->parse(
            "01/11/2026 TRANSFER CUSTOMER 100.000 0 1.500.000"
        );
        $this->assertFalse($result['ready']);
        $this->assertCount(0,$result['entries']);
    }
}