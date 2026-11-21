<?php

namespace Tests\Feature;

use App\Models\ReconciliationBatch;
use App\Models\ReconciliationEntry;
use App\Models\User;
use App\Services\ReconciliationMatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReconciliationTest extends TestCase
{
    use RefreshDatabase;

    private function row(ReconciliationBatch $batch, string $source, string $date, string $amount, string $direction = 'out'): ReconciliationEntry
    {
        return $batch->entries()->create([
            'source' => $source,
            'posted_at' => $date,
            'reference' => '',
            'description' => 'Test payment',
            'direction' => $direction,
            'amount' => $amount,
            'fingerprint' => hash('sha256', $source.$date.$amount),
        ]);
    }

    public function test_exact_unique_pair_is_suggested_until_approved(): void
    {
        $batch = ReconciliationBatch::create(['user_id'=>User::factory()->create()->id,'name'=>'November']);
        $bank = $this->row($batch,'bank','2026-11-01','100000.00');
        $ledger = $this->row($batch,'ledger','2026-11-01','100000.00');
        $matcher = app(ReconciliationMatcher::class);
        $matcher->run($batch);
        $this->assertSame('suggested',$bank->fresh()->status);
        $this->assertSame($ledger->id,$bank->fresh()->matched_entry_id);
        $matcher->decide($bank, true);
        $this->assertSame('matched',$bank->fresh()->status);
        $this->assertSame($bank->id,$ledger->fresh()->matched_entry_id);
    }

    public function test_ambiguous_or_opposite_direction_is_never_auto_matched(): void
    {
        $batch = ReconciliationBatch::create(['user_id'=>User::factory()->create()->id,'name'=>'November']);
        $bank = $this->row($batch,'bank','2026-11-01','100000.00');
        $this->row($batch,'ledger','2026-11-01','100000.00');
        $this->row($batch,'ledger','2026-11-01','100000.00');
        $this->row($batch,'ledger','2026-11-01','90000.00', 'in');
        app(ReconciliationMatcher::class)->run($batch);
        $this->assertSame('unmatched',$bank->fresh()->status);
    }

    public function test_reconciliation_page_requires_login(): void
    {
        $this->get(route('reconciliation.index'))->assertRedirect();
    }
}