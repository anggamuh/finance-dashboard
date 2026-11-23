<?php

namespace Tests\Feature;

use App\Livewire\BankLedger\Index;
use App\Models\BankEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BankLedgerDeleteImportTest extends TestCase
{
    use RefreshDatabase;

    private function entry(string $ledger, string $date, string $source, string $number): BankEntry
    {
        $attributes = [
            'ledger' => $ledger,
            'entry_date' => $date,
            'document_number' => $number,
            'description' => 'Pembayaran test '.$number,
            'debit' => 10000,
            'credit' => 0,
            'source' => $source,
        ];

        return BankEntry::create($attributes + [
            'fingerprint' => BankEntry::fingerprintFor($attributes),
        ]);
    }

    public function test_delete_import_only_affects_excel_rows_in_selected_ledger_and_month(): void
    {
        $target = $this->entry('BCA', '2026-10-03', 'excel', 'EXCEL-BCA-OCT');
        $manual = $this->entry('BCA', '2026-10-04', 'manual', 'MANUAL-BCA-OCT');
        $otherMonth = $this->entry('BCA', '2026-11-03', 'excel', 'EXCEL-BCA-NOV');
        $otherLedger = $this->entry('MANDIRI', '2026-10-03', 'excel', 'EXCEL-MANDIRI-OCT');

        $this->actingAs(User::factory()->create());

        Livewire::test(Index::class)
            ->set('ledger', 'BCA')
            ->set('month', 10)
            ->set('year', 2026)
            ->call('deleteImportedPeriod')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('bank_entries', ['id' => $target->id]);
        $this->assertDatabaseHas('bank_entries', ['id' => $manual->id]);
        $this->assertDatabaseHas('bank_entries', ['id' => $otherMonth->id]);
        $this->assertDatabaseHas('bank_entries', ['id' => $otherLedger->id]);
    }

    public function test_invalid_ledger_does_not_delete_anything(): void
    {
        $entry = $this->entry('BCA', '2026-10-03', 'excel', 'KEEP-ME');
        $this->actingAs(User::factory()->create());

        Livewire::test(Index::class)
            ->set('ledger', 'WRONG')
            ->set('month', 10)
            ->set('year', 2026)
            ->call('deleteImportedPeriod')
            ->assertHasErrors(['ledger']);

        $this->assertDatabaseHas('bank_entries', ['id' => $entry->id]);
    }
}
