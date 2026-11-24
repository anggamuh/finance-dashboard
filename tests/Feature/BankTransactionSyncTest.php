<?php

namespace Tests\Feature;

use App\Models\BankEntry;
use App\Models\Transaction;
use App\Services\BankTransactionSync;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankTransactionSyncTest extends TestCase
{
    use RefreshDatabase;

    private function createEntry(string $ledger, string $date, string $document): BankEntry
    {
        $data = [
            'ledger' => $ledger,
            'entry_date' => $date,
            'document_number' => $document,
            'account_code' => '81114',
            'account_name' => 'BIAYA ADM BANK',
            'description' => 'Admin transfer',
            'debit' => 0,
            'credit' => 2500,
            'source' => 'excel',
        ];
        return BankEntry::create($data + ['fingerprint' => BankEntry::fingerprintFor($data)]);
    }

    public function test_bank_import_becomes_normal_transaction_without_changing_cash_direction(): void
    {
        $entry = $this->createEntry('MANDIRI', '2026-10-07', 'M26K-010');
        $sync = app(BankTransactionSync::class);

        $first = $sync->sync($entry);
        $second = $sync->sync($entry);

        $this->assertSame($first->id, $second->id);
        $this->assertSame($entry->id, $first->bank_entry_id);
        $this->assertSame('expense', $first->transaction_type);
        $this->assertEquals(2500, $first->debit);
        $this->assertEquals(0, $first->credit);
        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_missing_dbacc_is_preserved_in_bank_source_and_does_not_break_transaction_sync(): void
    {
        $data = [
            'ledger' => 'MANDIRI',
            'entry_date' => '2026-11-01',
            'document_number' => 'M26M-001',
            'account_code' => null,
            'account_name' => 'PENDAPATAN BUNGA',
            'description' => 'MANDIRI',
            'debit' => 71805.87,
            'credit' => 0,
            'source' => 'excel',
        ];
        $entry = BankEntry::create($data + [
            'fingerprint' => BankEntry::fingerprintFor($data),
        ]);

        $transaction = app(BankTransactionSync::class)->sync($entry);

        $this->assertSame('BANK-UNMAPPED', $transaction->account_code);
        $this->assertSame('PENDAPATAN BUNGA', $transaction->account_name);
        $this->assertEquals(71805.87, $transaction->credit);
        $this->assertNull($entry->fresh()->account_code);
        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_same_document_number_in_another_month_keeps_separate_transactions(): void
    {
        $first = $this->createEntry('MANDIRI', '2026-09-07', 'M26K-010');
        $second = $this->createEntry('MANDIRI', '2026-10-07', 'M26K-010');

        $sync = app(BankTransactionSync::class);
        $sync->sync($first);
        $sync->sync($second);

        $this->assertDatabaseCount('transactions', 2);
    }

    public function test_deleting_imported_period_removes_only_linked_transactions(): void
    {
        $sync = app(BankTransactionSync::class);
        $old = $this->createEntry('BCA', '2026-10-04', 'BCA-OCT');
        $later = $this->createEntry('BCA', '2026-11-04', 'BCA-NOV');
        $manualData = [
            'ledger' => 'BCA', 'entry_date' => '2026-10-05', 'document_number' => 'MANUAL',
            'description' => 'Manual entry', 'debit' => 5000, 'credit' => 0,
            'source' => 'manual',
        ];
        $manual = BankEntry::create($manualData + [
            'fingerprint' => BankEntry::fingerprintFor($manualData),
        ]);
        foreach ([$old, $later, $manual] as $entry) {
            $sync->sync($entry);
        }

        $deleted = $sync->deleteImportedPeriod('BCA', 10, 2026);

        $this->assertSame(1, $deleted);
        $this->assertDatabaseMissing('bank_entries', ['id' => $old->id]);
        $this->assertDatabaseHas('bank_entries', ['id' => $later->id]);
        $this->assertDatabaseHas('bank_entries', ['id' => $manual->id]);
        $this->assertDatabaseMissing('transactions', ['bank_entry_id' => $old->id]);
        $this->assertDatabaseHas('transactions', ['bank_entry_id' => $later->id]);
        $this->assertDatabaseHas('transactions', ['bank_entry_id' => $manual->id]);
    }
}
