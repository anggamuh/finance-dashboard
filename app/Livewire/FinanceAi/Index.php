<?php

namespace App\Livewire\FinanceAi;

use App\Services\FinanceAiContext;
use App\Services\GeminiFinanceAssistant;
use Livewire\Component;

class Index extends Component
{
    public string $month = '';
    public string $question = '';
    public array $messages = [];

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function ask(FinanceAiContext $context, GeminiFinanceAssistant $gemini): void
    {
        $this->validate([
            'month' => ['required','date_format:Y-m'],
            'question' => ['required','string','min:3','max:500'],
        ]);
        // Limit per-user requests on shared hosting and API free quotas.
        $key = 'finance-ai:'.auth()->id();
        $limiter = app(\Illuminate\Cache\RateLimiter::class);
        if ($limiter->tooManyAttempts($key, 15)) {
            $this->addError('question', 'Batas 15 pertanyaan per jam tercapai. Coba lagi nanti.');
            return;
        }
        $limiter->hit($key, 3600);

        $query = trim($this->question);
        $previous = collect($this->messages)->map(fn ($m) => [
            'role' => $m['role'] === 'assistant' ? 'model' : 'user',
            'text' => $m['text'],
        ])->all();
        $this->messages[] = ['role'=>'user','text'=>$query,'month'=>$this->month];
        $this->question = '';

        try {
            $data = $context->forMonth((int)auth()->id(), $this->month);
            $answer = $gemini->reply($query, $data, $previous);
            $this->messages[] = ['role'=>'assistant','text'=>$answer,'month'=>$this->month];
        } catch (\Throwable $e) {
            $this->messages = array_slice($this->messages, 0, -1);
            $this->question = $query;
            if ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException) {
                $this->addError('question', $e->getMessage());
            } else {
                report($e);
                $this->addError('question', 'Finance AI belum bisa menjawab. Coba lagi nanti.');
            }
        }
        // Keep history bounded to prevent oversized Livewire payloads.
        $this->messages = array_slice($this->messages, -12);
    }

    public function quickQuestion(string $type): void
    {
        $this->question = match ($type) {
            'expense' => 'Berapa beban operasional bulan ini dibanding bulan sebelumnya?',
            'accounts' => 'Apa tiga akun beban operasional terbesar dan nominalnya?',
            'reconciliation' => 'Bagaimana status rekonsiliasi bank di bulan ini?',
            'revenue' => 'Berapa total omzet yang tercatat pada periode ini, dan apa keterbatasan datanya?',
            default => '',
        };
    }

    public function clearChat(): void
    {
        $this->messages = [];
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.finance-ai.index')->layout('layouts.app');
    }
}
