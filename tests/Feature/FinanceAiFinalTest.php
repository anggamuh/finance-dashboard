<?php

namespace Tests\Feature;

use App\Models\FinanceAiChat;
use App\Models\FinanceAiReport;
use App\Models\ReconciliationBatch;
use App\Models\ReconciliationEntry;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinanceAiAudit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceAiFinalTest extends TestCase
{
    use RefreshDatabase;

    private function expense(string $date, string $amount, string $number): Transaction
    {
        return Transaction::create([
            'transaction_date'=>$date,
            'account_code'=>'531104',
            'account_name'=>'Jasa DTF',
            'transaction_type'=>'expense',
            'transaction_number'=>$number,
            'description'=>'Biaya produksi',
            'debit'=>$amount,
            'credit'=>'0',
        ]);
    }

    public function test_investigator_can_filter_by_amount_without_mutating_transactions(): void
    {
        $this->expense('2026-09-10','50000','a');
        $this->expense('2026-09-11','7000000','b');
        $result = app(FinanceAiAudit::class)->investigate('2026-09','531104',5000000);
        $this->assertSame(1,$result['count']);
        $this->assertSame(7000000.0,$result['total']);
        $this->assertCount(2,Transaction::all());
    }

    public function test_duplicate_signal_is_only_a_review_indicator(): void
    {
        $this->expense('2026-09-10','500000','a');
        $this->expense('2026-09-10','500000','b');
        $result = app(FinanceAiAudit::class)->anomalies('2026-09');
        $this->assertSame('potential_duplicate',$result[0]['kind']);
        $this->assertSame(2,$result[0]['count']);
    }

    public function test_report_is_encrypted_and_only_owner_can_print(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $report = FinanceAiReport::create([
            'user_id'=>$owner->id,
            'month'=>'2026-09',
            'title'=>'September 2026',
            'snapshot'=>[
                'expenses'=>100000,'previous_expenses'=>90000,'change'=>10000,
                'accounts'=>[],'insights'=>[],'anomaly_signals'=>[],'reconciliation'=>[],
                'disclaimer'=>'Test',
            ],
        ]);

        $this->assertNotSame('100000', (string)$report->getRawOriginal('snapshot'));
        $this->actingAs($other)->get(route('finance-ai.report',$report))->assertNotFound();
        $this->actingAs($owner)->get(route('finance-ai.report',$report))
            ->assertOk()->assertSee('September 2026');
    }

    public function test_users_cannot_load_or_delete_another_users_chat(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $chat = FinanceAiChat::create([
            'user_id'=>$owner->id,
            'month'=>'2026-09',
            'title'=>'Rahasia',
            'messages'=>[['role'=>'user','month'=>'2026-09','text'=>'Berapa beban?']],
        ]);
        Livewire::actingAs($other)->test(\App\Livewire\FinanceAi\Index::class)
            ->call('loadChat',$chat->id)->assertNotFound();
        Livewire::actingAs($other)->test(\App\Livewire\FinanceAi\Index::class)
            ->call('deleteChat',$chat->id)->assertNotFound();
        $this->assertDatabaseHas('finance_ai_chats',['id'=>$chat->id]);
    }

    public function test_saved_report_summary_uses_owner_only_reconciliation(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $ours = ReconciliationBatch::create(['user_id'=>$owner->id,'name'=>'BCA']);
        $theirs = ReconciliationBatch::create(['user_id'=>$other->id,'name'=>'Rahasia']);
        foreach ([$ours,$theirs] as $batch) {
            ReconciliationEntry::create([
                'batch_id'=>$batch->id,'source'=>'bank','posted_at'=>'2026-09-10',
                'reference'=>'', 'description'=>'Sample', 'direction'=>'out',
                'amount'=>'100000.00','fingerprint'=>hash('sha256','r'.$batch->id),
                'status'=>'unmatched',
            ]);
        }
        $data = app(FinanceAiAudit::class)->reconciliation($owner->id,'2026-09');
        $this->assertCount(1,$data);
        $this->assertSame('BCA',$data[0]['label']);
    }
}
