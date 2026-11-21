<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceAiStandaloneDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_ai_ui_uses_standalone_css_and_keeps_existing_controls(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('finance-ai.index'))
            ->assertOk()
            ->assertSee('Finance AI')
            ->assertSee('fai-workspace')
            ->assertSee('fai-composer')
            ->assertSee('fai-chart')
            ->assertSee('fai-account-list')
            ->assertSee('wire:submit="ask"', false)
            ->assertSee('wire:click="clearChat"', false)
            ->assertSee('wire:click="previousAccountPage"', false)
            ->assertSee('wire:click="nextAccountPage"', false);
    }

    public function test_finance_ai_standalone_css_has_no_new_asset_build_dependency(): void
    {
        $view = file_get_contents(resource_path('views/livewire/finance-ai/index.blade.php'));
        $this->assertStringContainsString('<style>', $view);
        $this->assertStringContainsString('.dark .fai', $view);
        $this->assertStringContainsString('@media(max-width:610px)', $view);
        $this->assertStringNotContainsString('@vite(', $view);
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $view);
    }
}
