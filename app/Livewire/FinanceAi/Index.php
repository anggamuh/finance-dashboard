<?php

namespace App\Livewire\FinanceAi;

use App\Services\FinanceAiContext;
use App\Services\FinanceAiAudit;
use App\Models\FinanceAiReport;
use App\Models\FinanceAiChat;
use App\Services\GeminiFinanceAssistant;
use Livewire\Component;

class Index extends Component
{
    public string $month = '';
    public string $question = '';
    public array $messages = [];
    public ?string $selectedAccount = null;
    public int $accountPage = 1;
    public string $investigatorAccount = '';
    public string $investigatorMin = '';
    public string $activeTool = 'investigator';
    public ?int $openReportId = null;
    public ?int $openChatId = null;

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

    public function updatedMonth(): void
    {
        // History must never silently mix data for different accounting months.
        $this->clearChat();
        $this->selectedAccount = null;
        $this->accountPage = 1;
        $this->investigatorAccount = '';
        $this->investigatorMin = '';
        $this->openReportId = null;
    }

    public function showAccount(string $code): void
    {
        if (!preg_match('/^[0-9A-Za-z._-]{1,30}$/D', $code)) {
            return;
        }
        $this->selectedAccount = $code;
        $this->accountPage = 1;
    }

    public function closeAccount(): void
    {
        $this->selectedAccount = null;
        $this->accountPage = 1;
    }

    public function nextAccountPage(): void
    {
        $this->accountPage = min(100, $this->accountPage + 1);
    }

    public function previousAccountPage(): void
    {
        $this->accountPage = max(1, $this->accountPage - 1);
    }

    public function saveChat(): void
    {
        if (!$this->messages) {
            $this->addError('chat','Belum ada percakapan untuk disimpan.');
            return;
        }
        if (FinanceAiChat::where('user_id',auth()->id())->count() >= 25) {
            $this->addError('chat','Batas 25 percakapan tersimpan tercapai. Hapus percakapan lama terlebih dulu.');
            return;
        }
        $messages = collect(array_slice($this->messages,-12))->filter(fn ($row) =>
            is_array($row)
            && in_array($row['role'] ?? null,['user','assistant'],true)
            && is_string($row['text'] ?? null)
            && ($row['month'] ?? null) === $this->month
        )->map(fn ($row) => [
            'role'=>$row['role'],
            'month'=>$this->month,
            'text'=>mb_substr($row['text'],0,7000),
        ])->values()->all();
        if (!$messages) return;
        $firstUser = collect($messages)->firstWhere('role','user');
        $chat = FinanceAiChat::create([
            'user_id'=>auth()->id(),
            'month'=>$this->month,
            'title'=>mb_substr($firstUser['text'] ?? 'Finance AI chat',0,90),
            'messages'=>$messages,
        ]);
        $this->openChatId = $chat->id;
        session()->flash('success','Percakapan disimpan terenkripsi.');
    }

    public function loadChat(int $id): void
    {
        $chat = FinanceAiChat::where('user_id',auth()->id())->findOrFail($id);
        $this->month = $chat->month;
        $this->messages = array_slice((array)$chat->messages,-12);
        $this->openChatId = $chat->id;
        $this->selectedAccount = null;
        $this->accountPage = 1;
    }

    public function deleteChat(int $id): void
    {
        FinanceAiChat::where('user_id',auth()->id())->findOrFail($id)->delete();
        if ($this->openChatId === $id) $this->openChatId = null;
        session()->flash('success','Riwayat percakapan dihapus.');
    }

    public function switchTool(string $tool): void
    {
        if (in_array($tool, ['investigator','anomalies','reconciliation','reports'], true)) {
            $this->activeTool = $tool;
        }
    }

    public function saveMonthlyReport(FinanceAiAudit $audit): void
    {
        $this->validate(['month'=>['required','date_format:Y-m']]);
        if (FinanceAiReport::where('user_id',auth()->id())->count() >= 36) {
            $this->addError('report','Maksimal 36 laporan tersimpan. Hapus laporan lama untuk menyimpan baru.');
            return;
        }
        $snapshot = $audit->snapshot((int)auth()->id(), $this->month);
        $report = FinanceAiReport::create([
            'user_id'=>auth()->id(),
            'month'=>$this->month,
            'title'=>'Ringkasan Keuangan '.$this->month,
            'snapshot'=>$snapshot,
        ]);
        $this->openReportId = $report->id;
        $this->activeTool = 'reports';
        session()->flash('success','Laporan bulanan disimpan. Gunakan tombol Cetak untuk mencetak atau Save as PDF.');
    }

    public function selectReport(int $id): void
    {
        $report = FinanceAiReport::where('user_id',auth()->id())->findOrFail($id);
        $this->openReportId = $report->id;
        $this->activeTool = 'reports';
    }

    public function deleteReport(int $id): void
    {
        FinanceAiReport::where('user_id',auth()->id())->findOrFail($id)->delete();
        if ($this->openReportId === $id) $this->openReportId = null;
        session()->flash('success','Laporan tersimpan dihapus.');
    }

    public function quickQuestion(string $type): void
    {
        $this->question = match ($type) {
            'expense' => 'Berapa beban operasional bulan ini dibanding bulan sebelumnya?',
            'accounts' => 'Apa tiga akun beban operasional terbesar dan nominalnya?',
            'reconciliation' => 'Bagaimana status rekonsiliasi bank di bulan ini?',
            'revenue' => 'Berapa total omzet yang tercatat pada periode ini, dan apa keterbatasan datanya?',
            'why' => 'Berdasarkan data akun dan perbandingan bulan ini, faktor tercatat apa yang menjelaskan perubahan beban? Jika belum cukup data, jelaskan keterbatasannya.',
            default => '',
        };
    }

    public function clearChat(): void
    {
        $this->messages = [];
        $this->resetErrorBag();
    }

    public function render(FinanceAiContext $context)
    {
        // The dashboard is server-calculated, not model-generated.
        $dashboard = $context->dashboard($this->month);
        $accountTransactions = null;
        $accountName = null;
        if ($this->selectedAccount !== null) {
            // Only expense-class rows from the selected period can be drilled into.
            $from = $this->month.'-01';
            $end = \Illuminate\Support\Carbon::parse($from)->endOfMonth()->toDateString();
            $matched = collect($dashboard['accounts'])->firstWhere('code', $this->selectedAccount);
            if ($matched) {
                $accountName = $matched['name'];
                $accountTransactions = $context->expenseQuery($from, $end)
                    ->where('account_code', $this->selectedAccount)
                    ->orderByDesc('transaction_date')
                    ->orderByDesc('id')
                    ->paginate(12, ['id','transaction_date','description','debit','account_name'], 'accountPage', $this->accountPage);
            } else {
                $this->selectedAccount = null;
            }
        }

        $audit = app(FinanceAiAudit::class);
        $investigation = null;
        if ($this->activeTool === 'investigator') {
            $minimum = trim($this->investigatorMin);
            if ($minimum === '' || (is_numeric($minimum) && (float)$minimum >= 0 && (float)$minimum <= 100000000000)) {
                $investigation = $audit->investigate($this->month,
                    $this->investigatorAccount === '' ? null : $this->investigatorAccount,
                    $minimum === '' ? null : (float)$minimum);
            }
        }
        $anomalies = $this->activeTool === 'anomalies' ? $audit->anomalies($this->month) : [];
        $reconciliationAudit = $this->activeTool === 'reconciliation' ? $audit->reconciliation((int)auth()->id(),$this->month) : [];
        $savedReports = FinanceAiReport::where('user_id',auth()->id())->latest()->limit(36)->get(['id','month','title','created_at']);
        $savedChats = FinanceAiChat::where('user_id',auth()->id())->latest()->limit(25)->get(['id','month','title','created_at']);
        $selectedReport = $this->openReportId
            ? FinanceAiReport::where('user_id',auth()->id())->find($this->openReportId) : null;
        return view('livewire.finance-ai.index', compact(
            'dashboard','accountTransactions','accountName','investigation',
            'anomalies','reconciliationAudit','savedReports','selectedReport','savedChats'
        ))
            ->layout('layouts.app');
    }
}
