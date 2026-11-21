<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Services\FinanceAiContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceAiInsightsTest extends TestCase
{
    use RefreshDatabase;

    private function expense(string $date, string $amount, string $code = '531104'): Transaction
    {
        return Transaction::create([
            'transaction_date'=>$date,
            'account_code'=>$code,
            'account_name'=>$code === '531104' ? 'Jasa DTF' : 'Gaji Karyawan',
            'transaction_type'=>'expense',
            'transaction_number'=>'AI-TEST',
            'description'=>'Testing biaya operasional',
            'debit'=>$amount,
            'credit'=>'0',
        ]);
    }

    public function test_charts_use_real_monthly_aggregates_and_month_difference(): void
    {
        $this->expense('2026-08-17', '100000.00');
        $this->expense('2026-09-17', '300000.00');
        $this->expense('2026-09-19', '50000.00', '531101');
        $data = app(FinanceAiContext::class)->dashboard('2026-09');

        $this->assertSame(350000.0, $data['total']);
        $this->assertSame(100000.0, $data['previous_total']);
        $this->assertSame(250000.0, $data['delta']);
        $this->assertCount(6, $data['chart']);
        $this->assertSame('2026-09', $data['chart'][5]['month']);
        $this->assertSame(350000.0, $data['chart'][5]['total']);
        $this->assertSame('Jasa DTF', $data['accounts'][0]['name']);
        $this->assertNotEmpty($data['insights']);
    }

    public function test_drilldown_is_filtered_to_selected_account_and_month(): void
    {
        $user = User::factory()->create();
        $this->expense('2026-09-05','123000.00');
        $this->expense('2026-10-05','555000.00');
        $this->expense('2026-09-06','999000.00','531101');

        Livewire::actingAs($user)
            ->test(\App\Livewire\FinanceAi\Index::class)
            ->set('month','2026-09')
            ->call('showAccount','531104')
            ->assertSee('Rincian: Jasa DTF')
            ->assertSee('123.000')
            ->assertDontSee('555.000')
            ->call('closeAccount')
            ->assertDontSee('Rincian: Jasa DTF');
    }

    public function test_switching_month_resets_chat_history(): void
    {
        $user = User::factory()->create();
        Livewire::actingAs($user)
            ->test(\App\Livewire\FinanceAi\Index::class)
            ->set('messages',[['role'=>'user','text'=>'Old month','month'=>'2026-09']])
            ->set('month','2026-10')
            ->assertSet('messages',[]);
    }
}
