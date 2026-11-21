<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\FinanceAiContext;
use App\Services\GeminiFinanceAssistant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FinanceAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_ai_requires_authentication(): void
    {
        $this->get(route('finance-ai.index'))->assertRedirect();
    }

    public function test_finance_context_uses_read_only_aggregate_fields(): void
    {
        $user = User::factory()->create();
        $result = app(FinanceAiContext::class)->forMonth($user->id, '2026-10');

        $this->assertSame('2026-10',$result['periode']);
        $this->assertSame(0,$result['beban_operasional']['jumlah_transaksi']);
        $this->assertSame(0,$result['omzet_entri']['jumlah']);
        $this->assertSame([], $result['rekonsiliasi_milik_pengguna']);
        $this->assertArrayNotHasKey('nomor_rekening',$result);
    }

    public function test_gemini_request_uses_key_as_header_and_returns_text(): void
    {
        config()->set('services.gemini.api_key','fake-test-key');
        config()->set('services.gemini.model','gemini-2.5-flash');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates'=>[['content'=>['parts'=>[['text'=>'Total pengeluaran Rp 0.']]]]],
            ],200),
        ]);

        $result = app(GeminiFinanceAssistant::class)->reply('Total pengeluaran?',[
            'periode'=>'2026-10',
            'beban_operasional'=>['total_rp'=>0],
        ]);
        $this->assertSame('Total pengeluaran Rp 0.',$result);
        Http::assertSent(fn ($request) =>
            $request->hasHeader('x-goog-api-key','fake-test-key') &&
            !str_contains($request->url(),'fake-test-key') &&
            $request->method() === 'POST'
        );
    }

    public function test_gemini_quota_error_is_user_friendly(): void
    {
        config()->set('services.gemini.api_key','fake-test-key');
        Http::fake(['generativelanguage.googleapis.com/*'=>Http::response(['error'=>['message'=>'Quota exceeded']],429)]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Kuota gratis Gemini');
        app(GeminiFinanceAssistant::class)->reply('Halo',['periode'=>'2026-10']);
    }
}
