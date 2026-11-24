<?php

namespace Tests\Feature;

use App\Livewire\Transactions\Neraca;
use App\Models\BankEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionsNeracaTest extends TestCase
{
    use RefreshDatabase;

    public function test_neraca_cash_position_uses_opening_plus_in_minus_out_as_of_date(): void
    {
        DB::table('bank_ledger_openings')->insert([
            'ledger' => 'BCA', 'year' => 2026, 'month' => 11,
            'amount' => 100000, 'created_at' => now(), 'updated_at' => now(),
        ]);
        foreach ([
            ['date' => '2026-11-05', 'debit' => 50000, 'credit' => 0, 'number' => 'BCA-IN'],
            ['date' => '2026-11-08', 'debit' => 0, 'credit' => 20000, 'number' => 'BCA-OUT'],
            ['date' => '2026-11-20', 'debit' => 900000, 'credit' => 0, 'number' => 'NEXT'],
        ] as $row) {
            $data = [
                'ledger' => 'BCA', 'entry_date' => $row['date'],
                'document_number' => $row['number'], 'description' => $row['number'],
                'debit' => $row['debit'], 'credit' => $row['credit'], 'source' => 'excel',
            ];
            BankEntry::create($data + ['fingerprint' => BankEntry::fingerprintFor($data)]);
        }

        Livewire::test(Neraca::class)
            ->set('asOf', '2026-11-10')
            ->assertSee('GABUNGAN')
            ->assertSee('121')
            ->assertSee('130.000,00')
            ->assertSee('BCA-IN')
            ->assertDontSee('NEXT')
            ->set('ledger', 'MANDIRI')
            ->assertDontSee('BCA-IN');
    }

    public function test_excel_sheet_ledger_codes_and_independent_balances(): void
    {
        foreach ([
            ['KAS', '2026-11-01', 'KAS-001', 500000, 0],
            ['BCA', '2026-11-01', 'BCA-001', 75000, 0],
            ['MANDIRI', '2026-11-01', 'MANDIRI-001', 0, 20000],
        ] as [$ledger, $date, $number, $debit, $credit]) {
            $data = [
                'ledger' => $ledger,
                'entry_date' => $date,
                'document_number' => $number,
                'description' => $number,
                'debit' => $debit,
                'credit' => $credit,
                'source' => 'excel',
            ];
            BankEntry::create($data + ['fingerprint' => BankEntry::fingerprintFor($data)]);
        }

        Livewire::test(Neraca::class)->set('asOf', '2026-11-10')
            ->assertSee('111')->assertSee('121')->assertSee('122')
            ->assertSee('KAS-001')->assertSee('BCA-001')->assertSee('MANDIRI-001')
            ->assertSee('500.000,00')->assertSee('75.000,00')
            ->assertSee('-20.000,00')
            ->set('ledger', 'KAS')
            ->assertSee('KAS-001')
            ->assertDontSee('BCA-001')
            ->assertDontSee('MANDIRI-001');
    }
}
