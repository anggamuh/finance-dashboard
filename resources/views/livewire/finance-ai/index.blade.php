
<div class="fai">
    <header class="fai-top">
        <div>
            <div class="fai-kicker"><span class="fai-mark">✦</span> SECOND STAR · FINANCE INTELLIGENCE</div>
            <h1>Finance AI <span style="color:var(--fa-accent)">Workspace</span></h1>
            <p class="fai-intro">Tanya, analisis, dan telusuri laporan keuangan di satu tempat. Data dihitung langsung dari pembukuan.</p>
        </div>
        <button type="button" class="fai-button" wire:click="clearChat">↻ &nbsp; Percakapan baru</button>
    </header>

    <section class="fai-stats" aria-label="Ringkasan keuangan">
        <div class="fai-stat is-accent">
            <span class="fai-label">Beban operasional</span>
            <div class="fai-stat-value">Rp {{ number_format($dashboard['total'],0,',','.') }}</div>
            <div class="fai-stat-desc">Periode {{ $dashboard['month'] }}</div>
        </div>
        <div class="fai-stat">
            <span class="fai-label">Bulan sebelumnya</span>
            <div class="fai-stat-value">Rp {{ number_format($dashboard['previous_total'],0,',','.') }}</div>
            <div class="fai-stat-desc">Pembanding beban operasional</div>
        </div>
        <div class="fai-stat">
            <span class="fai-label">Selisih antarbulan</span>
            <div class="fai-stat-value" style="color:{{ $dashboard['delta'] > 0 ? '#d75e67' : 'var(--fa-accent)' }}">{{ $dashboard['delta'] > 0 ? '+' : ($dashboard['delta'] < 0 ? '−' : '') }}Rp {{ number_format(abs($dashboard['delta']),0,',','.') }}</div>
            <div class="fai-stat-desc">{{ $dashboard['delta'] > 0 ? 'Beban meningkat' : ($dashboard['delta'] < 0 ? 'Beban menurun' : 'Tidak ada perubahan') }}</div>
        </div>
    </section>

    <div class="fai-overview">
        <section class="fai-panel fai-panel-pad">
            <h2 class="fai-panel-title">Tren pengeluaran</h2>
            <p class="fai-panel-hint">Enam bulan terakhir · angka aktual Accurate</p>
            @php($chartMax = max(1, collect($dashboard['chart'])->max('total')))
            <div class="fai-chart" role="img" aria-label="Grafik beban operasional enam bulan terakhir">
                @foreach($dashboard['chart'] as $point)
                    <div class="fai-chart-col" title="{{ $point['month'] }}: Rp {{ number_format($point['total'],0,',','.') }}">
                        <span class="fai-chart-amount">{{ number_format($point['total']/1000000,1,',','.') }} jt</span>
                        <div class="fai-chart-track"><div class="fai-chart-bar" style="height:{{ max(0,min(100,$point['total']/$chartMax*100)) }}%"></div></div>
                        <span class="fai-chart-month">{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
        <section class="fai-panel fai-panel-pad">
            <h2 class="fai-panel-title">Insight keuangan</h2>
            <p class="fai-panel-hint">Pemeriksaan otomatis dari database, tanpa request Gemini tambahan</p>
            <div class="fai-insights">
                @foreach($dashboard['insights'] as $insight)
                    <div class="fai-insight"><span class="fai-insight-dot"></span><div>
                        <p class="fai-insight-title">{{ $insight['title'] }}</p>
                        <p class="fai-insight-desc">{{ $insight['detail'] }}</p>
                    </div></div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="fai-workspace">
        <section class="fai-panel fai-chat" aria-label="Percakapan Finance AI">
            <div class="fai-chat-header">
                <div class="fai-chat-brand">
                    <div class="fai-logo">✦</div>
                    <div>
                        <p class="fai-name">Finance Copilot <span style="color:var(--fa-muted);font-size:11px;font-weight:500">· Gemini</span></p>
                        <p class="fai-subname"><span class="fai-online"></span>Read-only · Data agregat · Tidak mengubah transaksi</p>
                    </div>
                </div>
                <label class="fai-month-label">Periode <input class="fai-month" aria-label="Pilih periode" type="month" wire:model.live="month"></label>
            </div>
            <div class="fai-dialog" aria-live="polite">
                @if(count($messages) === 0)
                    <div class="fai-empty">
                        <div class="fai-empty-icon">✦</div>
                        <h2>Halo, mau analisis apa hari ini?</h2>
                        <p>Tanyakan beban, omzet, tren pengeluaran, atau progres rekonsiliasi bank. Pilih pertanyaan cepat atau tulis pertanyaan sendiri.</p>
                    </div>
                @endif
                @foreach($messages as $i => $message)
                    <div class="fai-message {{ $message['role'] === 'user' ? 'is-user' : 'is-ai' }}" wire:key="fai-message-{{ $i }}">
                        <div class="fai-bubble">
                            <p class="fai-message-role">{{ $message['role'] === 'user' ? 'Kamu' : 'Finance AI' }} · {{ $message['month'] }}</p>
                            @if($message['role'] === 'assistant')
                                <div class="fai-markdown">{!! \Illuminate\Support\Str::markdown($message['text'], ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
                            @else
                                <div class="fai-plain">{{ $message['text'] }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="fai-input-area">
                @error('question') <p class="fai-error">{{ $message }}</p> @enderror
                @error('month') <p class="fai-error">{{ $message }}</p> @enderror
                <form class="fai-composer" wire:submit="ask">
                    <textarea wire:model="question" rows="2" maxlength="500" required aria-label="Tanyakan tentang keuangan" placeholder="Tanyakan sesuatu tentang laporan keuangan..."></textarea>
                    <button type="submit" class="fai-send" wire:loading.attr="disabled" wire:target="ask" aria-label="Kirim pertanyaan"><x-heroicon-o-paper-airplane /></button>
                </form>
                <div class="fai-footnote"><span>AI bisa salah. Periksa kembali angka sebelum dipakai untuk laporan.</span><span wire:loading wire:target="ask">Sedang menganalisis...</span></div>
            </div>
        </section>
        <aside class="fai-side">
            <section class="fai-panel fai-panel-pad">
                <h2 class="fai-panel-title">Pertanyaan cepat</h2>
                <p class="fai-panel-hint">Pilih untuk mengisi kolom chat</p>
                <div class="fai-quick">
                    @foreach(['expense'=>'Beban bulan ini vs sebelumnya','accounts'=>'Tiga akun beban terbesar','reconciliation'=>'Status rekonsiliasi bank','revenue'=>'Total omzet periode ini','why'=>'Apa penyebab perubahan biaya?'] as $type => $label)
                        <button type="button" class="fai-quick-btn" wire:click="quickQuestion('{{ $type }}')"><span>{{ $label }}</span><span class="fai-arrow">↗</span></button>
                    @endforeach
                </div>
            </section>
            <section class="fai-panel fai-panel-pad">
                <h2 class="fai-panel-title">Akun beban terbesar</h2>
                <p class="fai-panel-hint">Klik untuk lihat rincian transaksi</p>
                <div class="fai-account-list">
                    @forelse($dashboard['accounts'] as $row)
                        <button class="fai-account" type="button" wire:click="showAccount(@js($row['code']))">
                            <span><span class="fai-account-name">{{ $row['name'] }}</span><span class="fai-account-meta">Rp {{ number_format($row['amount'],0,',','.') }} · {{ $row['count'] }} transaksi</span></span><span class="fai-account-chevron">›</span>
                        </button>
                    @empty
                        <p class="fai-panel-hint" style="margin-top:14px">Belum ada akun beban untuk periode ini.</p>
                    @endforelse
                </div>
            </section>
            <section class="fai-panel fai-panel-pad">
                <h2 class="fai-panel-title">Riwayat percakapan</h2>
                <p class="fai-panel-hint">Opsional · tersimpan terenkripsi · maksimal 25 chat</p>
                @error('chat')<p class="fai-error">{{ $message }}</p>@enderror
                <button type="button" class="fai-suite-primary" style="margin-top:12px" wire:click="saveChat">+ Simpan percakapan</button>
                <div class="fai-account-list">
                    @forelse($savedChats as $chat)
                        <div class="fai-chat-history" wire:key="fai-chat-save-{{ $chat->id }}">
                            <button type="button" wire:click="loadChat({{ $chat->id }})" title="{{ $chat->title }}">
                                <strong>{{ $chat->title }}</strong><small>{{ $chat->month }} · {{ $chat->created_at->format('d/m/Y') }}</small>
                            </button>
                            <button type="button" class="fai-chat-remove" wire:click="deleteChat({{ $chat->id }})" wire:confirm="Hapus percakapan tersimpan?">×</button>
                        </div>
                    @empty
                        <p class="fai-panel-hint" style="margin-top:12px">Belum ada chat yang disimpan.</p>
                    @endforelse
                </div>
            </section>
            <section class="fai-panel fai-panel-pad fai-secure">
                <h2 class="fai-panel-title"><span class="fai-secure-icon">♧</span> Privasi & keamanan</h2>
                <p>Gemini menerima ringkasan data keuangan, bukan nomor rekening, referensi transfer, atau keterangan transaksi individual.</p>
                <p>15 pertanyaan per jam per akun · Chat hanya disimpan jika Anda memilih Simpan percakapan. Riwayat bisa dihapus.</p>
            </section>
        </aside>
    </div>

    @if($accountTransactions)
        <section class="fai-panel fai-detail" aria-label="Rincian transaksi akun">
            <div class="fai-detail-head">
                <div><h2 class="fai-panel-title">Rincian akun · {{ $accountName }}</h2><p class="fai-panel-hint">Periode {{ $month }} · Detail hanya muncul di dashboard, tidak dikirim ke Gemini.</p></div>
                <button class="fai-button" type="button" wire:click="closeAccount">Tutup</button>
            </div>
            <div class="fai-table-wrap">
                <table class="fai-table">
                    <thead><tr><th>Tanggal</th><th>Keterangan</th><th class="num">Nominal beban</th></tr></thead>
                    <tbody>
                        @foreach($accountTransactions as $transaction)
                            <tr wire:key="fai-transaction-{{ $transaction->id }}">
                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->description ?: '—' }}</td>
                                <td class="num">Rp {{ number_format((float)$transaction->debit,0,',','.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="fai-pagination">
                <button type="button" class="fai-button" wire:click="previousAccountPage" @disabled($accountPage <= 1)>‹ Sebelumnya</button>
                <span>Halaman {{ $accountPage }} / {{ max(1,$accountTransactions->lastPage()) }} · {{ $accountTransactions->total() }} transaksi</span>
                <button type="button" class="fai-button" wire:click="nextAccountPage" @disabled(!$accountTransactions->hasMorePages())>Selanjutnya ›</button>
            </div>
        </section>
    @endif
    

<section class="fai-panel fai-panel-pad fai-suite">
    <div class="fai-suite-heading">
        <div><div class="fai-kicker">✦ FINANCE AI · FINAL</div><h2 class="fai-panel-title" style="font-size:20px;margin-top:8px">Investigation & Reporting</h2>
            <p class="fai-panel-hint">Pemeriksaan langsung dari database tanpa request Gemini tambahan</p></div>
        <span class="fai-suite-tag">Read-only</span>
    </div>
    @if(session('success'))<p class="fai-suite-success">{{ session('success') }}</p>@endif
    <div class="fai-suite-tabs">
        @foreach(['investigator'=>'Investigasi Transaksi','anomalies'=>'Anomali','reconciliation'=>'Rekonsiliasi','reports'=>'Laporan Bulanan'] as $tool => $label)
            <button type="button" wire:click="switchTool('{{ $tool }}')" class="fai-suite-tab {{ $activeTool === $tool ? 'active' : '' }}">{{ $label }}</button>
        @endforeach
    </div>

    @if($activeTool === 'investigator')
        <div class="fai-suite-filters">
            <label>Akun
                <select class="fai-suite-input" wire:model.live="investigatorAccount">
                    <option value="">Semua akun</option>
                    @foreach($dashboard['accounts'] as $account)
                        <option value="{{ $account['code'] }}">{{ $account['code'] }} · {{ $account['name'] }}</option>
                    @endforeach
                </select>
            </label>
            <label>Nominal minimum (Rp)
                <input class="fai-suite-input" type="number" min="0" max="100000000000" wire:model.live.debounce.400ms="investigatorMin" placeholder="5000000">
            </label>
        </div>
        @if($investigation)
            <div class="fai-suite-info">{{ $investigation['count'] }} transaksi · Total Rp {{ number_format($investigation['total'],0,',','.') }} · Menampilkan maksimal 50 baris</div>
            <div class="fai-table-wrap"><table class="fai-table">
                <thead><tr><th>Tanggal</th><th>Akun</th><th>Keterangan</th><th class="num">Nominal</th></tr></thead>
                <tbody>
                @forelse($investigation['rows'] as $row)
                    <tr wire:key="ai-audit-{{ $row['id'] }}"><td>{{ $row['date'] }}</td><td>{{ $row['code'] }} · {{ $row['account'] }}</td><td>{{ $row['description'] }}</td><td class="num">Rp {{ number_format($row['amount'],0,',','.') }}</td></tr>
                @empty<tr><td colspan="4">Tidak ada transaksi sesuai filter.</td></tr>@endforelse
                </tbody>
            </table></div>
        @else
            <p class="fai-panel-hint">Masukkan nominal minimum yang valid.</p>
        @endif
    @elseif($activeTool === 'anomalies')
        <p class="fai-panel-hint">Sinyal duplikat dari tanggal, akun, dan nominal yang sama; atau transaksi 3× rata-rata akun dengan minimum Rp1 juta dan minimal 5 transaksi. Ini belum membuktikan kesalahan.</p>
        <div class="fai-suite-list">
            @forelse($anomalies as $i => $finding)
                <div class="fai-suite-item" wire:key="ai-anomaly-{{ $i }}"><strong>{{ $finding['label'] }}</strong><span>{{ $finding['message'] }}</span></div>
            @empty<p class="fai-panel-hint">Tidak ditemukan indikasi berdasarkan aturan saat ini.</p>@endforelse
        </div>
    @elseif($activeTool === 'reconciliation')
        <p class="fai-panel-hint">Hanya rekonsiliasi milik akun login. Saran belum dihitung sebagai cocok disetujui. Nominal belum cocok tidak sama dengan saldo selisih bersih.</p>
        <div class="fai-table-wrap"><table class="fai-table">
            <thead><tr><th>Periode / Bank</th><th class="num">Matched</th><th class="num">Saran</th><th class="num">Belum cocok Bank / Excel</th><th class="num">Nominal belum cocok</th></tr></thead>
            <tbody>
            @forelse($reconciliationAudit as $row)
                <tr><td>{{ $row['label'] }} · {{ $row['bank'] }}</td><td class="num">{{ $row['matched'] }}</td><td class="num">{{ $row['suggested'] }}</td><td class="num">{{ $row['unmatched_bank'] }} / {{ $row['unmatched_ledger'] }}</td><td class="num">BCA: Rp {{ number_format($row['unmatched_bank_total'],0,',','.') }}<br>Excel: Rp {{ number_format($row['unmatched_ledger_total'],0,',','.') }}</td></tr>
            @empty<tr><td colspan="5">Belum ada rekonsiliasi periode ini.</td></tr>@endforelse
            </tbody>
        </table></div>
    @elseif($activeTool === 'reports')
        @error('report')<p class="fai-error">{{ $message }}</p>@enderror
        <div class="fai-suite-save">
            <div><strong>Laporan {{ $month }}</strong><p class="fai-panel-hint">Snapshot angka terenkripsi · maksimal 36 laporan per pengguna · cetak tanpa npm build</p></div>
            <button type="button" class="fai-suite-primary" wire:click="saveMonthlyReport" wire:loading.attr="disabled" wire:target="saveMonthlyReport">+ Simpan Laporan</button>
        </div>
        <div class="fai-suite-list">
            @forelse($savedReports as $report)
                <div class="fai-suite-item fai-suite-report" wire:key="ai-report-{{ $report->id }}">
                    <button type="button" wire:click="selectReport({{ $report->id }})"><strong>{{ $report->title }}</strong><small>{{ $report->created_at->format('d/m/Y H:i') }}</small></button>
                    <a class="fai-button" href="{{ route('finance-ai.report',$report->id) }}" target="_blank" rel="noopener noreferrer">Cetak / PDF</a>
                    <button type="button" class="fai-button" wire:click="deleteReport({{ $report->id }})" wire:confirm="Hapus laporan tersimpan?">Hapus</button>
                </div>
            @empty<p class="fai-panel-hint">Belum ada laporan tersimpan.</p>@endforelse
        </div>
        @if($selectedReport)
            <p class="fai-suite-info">Terpilih: {{ $selectedReport->title }} · Beban saat snapshot Rp {{ number_format($selectedReport->snapshot['expenses'] ?? 0,0,',','.') }}</p>
        @endif
    @endif
</section>

<style>

.fai-suite{margin-top:20px}.fai-suite-heading{display:flex;justify-content:space-between;gap:15px;align-items:center}
.fai-suite-tag{border-radius:20px;padding:7px 12px;background:var(--fa-soft);color:var(--fa-accent);font-size:11px;font-weight:800}
.fai-suite-tabs{display:flex;overflow-x:auto;gap:8px;margin:22px 0 18px;padding-bottom:13px;border-bottom:1px solid var(--fa-line)}
.fai-suite-tab{border:1px solid var(--fa-line);border-radius:10px;background:var(--fa-bg);color:var(--fa-text);white-space:nowrap;padding:10px 13px;font-size:11px;font-weight:750}
.fai-suite-tab.active{background:var(--fa-accent);color:white;border-color:var(--fa-accent)}
.fai-suite-filters{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:18px}
.fai-suite-filters label{display:grid;gap:6px;min-width:210px;font-size:11px;color:var(--fa-muted);font-weight:700}
.fai-suite-input{border:1px solid var(--fa-line);border-radius:9px;background:var(--fa-card);color:var(--fa-text);padding:10px;max-width:100%}
.fai-suite-info,.fai-suite-success{background:var(--fa-soft);border-radius:10px;padding:12px 14px;margin:10px 0;font-size:12px}
.fai-suite-list{display:grid;gap:9px;margin-top:16px}
.fai-suite-item{display:flex;align-items:center;gap:12px;background:var(--fa-bg);border:1px solid var(--fa-line);border-radius:11px;padding:12px}
.fai-suite-item strong{font-size:12px;flex-shrink:0}.fai-suite-item span{font-size:11px;color:var(--fa-muted);line-height:1.5}
.fai-suite-save{display:flex;justify-content:space-between;align-items:center;gap:14px}
.fai-suite-save strong{font-size:13px}
.fai-suite-primary{border:0;background:var(--fa-accent);color:#fff;font-weight:750;border-radius:10px;padding:11px 14px;font-size:12px}
.fai-suite-report button:first-child{border:0;text-align:left;flex:1;min-width:0;background:none;color:var(--fa-text)}
.fai-suite-report small{display:block;color:var(--fa-muted);font-size:10px;margin-top:4px}
.fai-suite-report a{text-decoration:none}
@media(max-width:610px){.fai-suite-filters,.fai-suite-save{display:grid}.fai-suite-report{flex-wrap:wrap}.fai-suite-report button:first-child{flex-basis:100%}}

/* Standalone styles: no Tailwind build, external CSS or JS dependencies. */
.fai{--fa-bg:#f3f7fa;--fa-card:#fff;--fa-text:#16253a;--fa-muted:#708198;--fa-line:#e4ebf1;--fa-accent:#059b78;--fa-soft:#e5f8f1;--fa-shadow:0 14px 42px rgba(23,50,75,.055);color:var(--fa-text);max-width:1500px;margin:0 auto;font-family:inherit}
.dark .fai{--fa-bg:#101922;--fa-card:#15222e;--fa-text:#eef5f8;--fa-muted:#99aabc;--fa-line:#304250;--fa-soft:#153f37;--fa-shadow:0 14px 42px rgba(0,0,0,.15)}
.fai *{box-sizing:border-box}.fai button,.fai textarea,.fai input{font:inherit}.fai button{cursor:pointer}.fai button:disabled{opacity:.5;cursor:not-allowed}
.fai button:focus-visible,.fai input:focus-visible,.fai textarea:focus-visible{outline:3px solid #42d7b4;outline-offset:2px}
.fai-top{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:24px}
.fai-kicker{display:flex;align-items:center;gap:9px;font-size:11px;font-weight:800;letter-spacing:.15em;text-transform:uppercase;color:var(--fa-accent)}
.fai-mark{width:27px;height:27px;display:grid;place-items:center;border-radius:9px;background:var(--fa-soft);font-size:15px}
.fai h1{font-weight:800;letter-spacing:-.035em;font-size:clamp(26px,3vw,35px);margin:8px 0 5px;line-height:1.15}
.fai-intro{color:var(--fa-muted);font-size:13px;line-height:1.6;margin:0}
.fai-button{border:1px solid var(--fa-line);background:var(--fa-card);color:var(--fa-text);border-radius:12px;padding:11px 15px;font-size:12px;font-weight:750;box-shadow:var(--fa-shadow);white-space:nowrap}
.fai-button:hover{border-color:#10b88d}
.fai-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:15px;margin-bottom:17px}
.fai-panel,.fai-stat{background:var(--fa-card);border:1px solid var(--fa-line);border-radius:19px;box-shadow:var(--fa-shadow)}
.fai-stat{padding:21px 22px;position:relative;overflow:hidden}
.fai-stat:before{content:"";position:absolute;width:82px;height:82px;border-radius:50%;background:var(--fa-soft);right:-33px;top:-42px}
.fai-stat .fai-label{font-size:12px;color:var(--fa-muted);font-weight:600}
.fai-stat-value{font-weight:800;font-variant-numeric:tabular-nums;letter-spacing:-.025em;font-size:clamp(18px,2vw,27px);line-height:1.25;margin:9px 0 6px;overflow-wrap:anywhere}
.fai-stat-desc{font-size:11px;color:var(--fa-muted)}
.fai-stat.is-accent .fai-stat-value{color:var(--fa-accent)}
.fai-overview{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(0,1fr);gap:17px;margin-bottom:17px}
.fai-panel-pad{padding:23px}
.fai-panel-title{font-size:15px;font-weight:800;letter-spacing:-.01em;margin:0 0 4px}
.fai-panel-hint{font-size:11px;color:var(--fa-muted);line-height:1.5;margin:0}
.fai-chart{height:180px;display:flex;align-items:stretch;gap:13px;margin-top:18px;padding:0 4px}
.fai-chart-col{flex:1;min-width:0;display:flex;align-items:center;flex-direction:column;justify-content:flex-end;gap:7px}
.fai-chart-amount{font-size:10px;font-weight:650;color:var(--fa-muted);white-space:nowrap}
.fai-chart-track{height:120px;width:100%;background:var(--fa-bg);border:1px solid var(--fa-line);border-radius:8px 8px 4px 4px;display:flex;align-items:flex-end;overflow:hidden;max-width:100px}
.fai-chart-bar{width:100%;min-height:2px;background:linear-gradient(180deg,#37c9a5,#059b78);border-radius:7px 7px 0 0}
.fai-chart-col:last-child .fai-chart-bar{background:linear-gradient(180deg,#14dab0,#047e63)}
.fai-chart-month{font-size:10px;color:var(--fa-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%}
.fai-insights{display:grid;gap:10px;margin-top:15px}
.fai-insight{padding:12px 14px;border:1px solid var(--fa-line);border-radius:12px;background:var(--fa-bg);display:flex;gap:11px}
.fai-insight-dot{width:8px;height:8px;flex:0 0 8px;margin-top:5px;border-radius:50%;background:#14b890;box-shadow:0 0 0 4px var(--fa-soft)}
.fai-insight-title{font-size:12px;font-weight:800;margin:0 0 4px}
.fai-insight-desc{font-size:11px;color:var(--fa-muted);line-height:1.55;margin:0}
.fai-workspace{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:17px;align-items:start}
.fai-chat{min-height:610px;display:flex;flex-direction:column;overflow:hidden}
.fai-chat-header{padding:17px 20px;border-bottom:1px solid var(--fa-line);display:flex;align-items:center;justify-content:space-between;gap:15px}
.fai-chat-brand{display:flex;align-items:center;gap:11px;min-width:0}
.fai-logo{width:39px;height:39px;display:grid;place-items:center;border-radius:13px;background:linear-gradient(145deg,#16bf97,#036b59);color:white;font-size:22px;flex:0 0 auto}
.fai-name{font-size:14px;font-weight:800;margin:0}
.fai-subname{font-size:10px;margin:4px 0 0;color:var(--fa-muted)}
.fai-online{display:inline-block;width:7px;height:7px;border-radius:50%;background:#14bb88;margin-right:5px}
.fai-month-label{display:flex;align-items:center;gap:8px;color:var(--fa-muted);font-size:11px;white-space:nowrap}
.fai-month{border:1px solid var(--fa-line);background:var(--fa-card);color:var(--fa-text);padding:8px 10px;border-radius:10px;max-width:175px;font-size:12px}
.fai-dialog{flex:1;min-height:365px;padding:23px;display:flex;flex-direction:column;gap:19px}
.fai-empty{margin:auto;text-align:center;max-width:340px;padding:20px 0}
.fai-empty-icon{width:64px;height:64px;display:grid;place-items:center;margin:0 auto 18px;border-radius:19px;background:var(--fa-soft);color:var(--fa-accent);font-size:29px}
.fai-empty h2{font-size:19px;letter-spacing:-.02em;margin:0 0 9px;font-weight:800}
.fai-empty p{font-size:12px;line-height:1.8;color:var(--fa-muted);margin:0}
.fai-message{display:flex}.fai-message.is-user{justify-content:flex-end}.fai-message.is-ai{justify-content:flex-start}
.fai-bubble{max-width:85%;min-width:0;padding:13px 17px;border-radius:16px;font-size:12px;line-height:1.85;overflow-wrap:anywhere}
.fai-message.is-user .fai-bubble{background:linear-gradient(140deg,#0da07d,#03735c);color:#fff;border-bottom-right-radius:5px}
.fai-message.is-ai .fai-bubble{border:1px solid var(--fa-line);background:var(--fa-bg);border-bottom-left-radius:5px;color:var(--fa-text)}
.fai-message-role{font-size:10px;text-transform:uppercase;letter-spacing:.09em;font-weight:850;opacity:.65;margin:0 0 7px}
.fai-plain{white-space:pre-wrap}
.fai-input-area{border-top:1px solid var(--fa-line);padding:16px 19px 18px}
.fai-error{background:#fff0f0;color:#b42328;border:1px solid #ffc6c6;padding:10px 12px;border-radius:10px;font-size:12px;margin:0 0 10px}
.dark .fai-error{background:#4b2525;color:#ffcdcd;border-color:#754242}
.fai-composer{display:flex;align-items:flex-end;gap:10px;background:var(--fa-bg);border:1px solid var(--fa-line);border-radius:15px;padding:8px}
.fai-composer:focus-within{border-color:#25ba93;box-shadow:0 0 0 3px rgba(17,178,133,.1)}
.fai-composer textarea{flex:1;min-width:0;min-height:55px;padding:10px 7px;resize:vertical;border:0;outline:0!important;box-shadow:none!important;background:transparent;color:var(--fa-text);font-size:12px;line-height:1.5}
.fai-composer textarea::placeholder{color:var(--fa-muted)}
.fai-send{width:39px;height:39px;flex:0 0 39px;display:grid;place-items:center;background:var(--fa-accent);color:#fff;border:0;border-radius:11px;margin-bottom:2px}
.fai-send:hover{background:#008568}.fai-send svg{height:18px;width:18px}
.fai-footnote{margin-top:9px;color:var(--fa-muted);font-size:10px;display:flex;justify-content:space-between;gap:8px}
.fai-side{display:grid;gap:16px}
.fai-quick{display:grid;gap:7px;margin-top:15px}
.fai-quick-btn{width:100%;border:1px solid var(--fa-line);background:var(--fa-bg);border-radius:11px;padding:12px 12px;text-align:left;color:var(--fa-text);font-size:11px;font-weight:650;display:flex;justify-content:space-between;gap:8px;align-items:center;line-height:1.45}
.fai-quick-btn:hover{border-color:#26b995;background:var(--fa-soft)}
.fai-arrow{color:var(--fa-accent);font-weight:900}
.fai-account-list{display:grid;gap:4px;margin-top:11px}
.fai-account{display:flex;justify-content:space-between;align-items:center;gap:10px;width:100%;border:0;border-radius:10px;background:transparent;text-align:left;padding:10px 8px;color:var(--fa-text)}
.fai-account:hover{background:var(--fa-soft)}
.fai-account-name{font-size:11px;font-weight:700;line-height:1.4;display:block}
.fai-account-meta{font-size:10px;color:var(--fa-muted);margin-top:4px;display:block}
.fai-account-chevron{font-size:20px;color:var(--fa-accent)}
.fai-secure{background:var(--fa-soft);border-color:transparent}
.fai-secure-icon{font-size:17px;margin-right:5px}.fai-secure p{font-size:11px;color:var(--fa-muted);line-height:1.7;margin:10px 0 0}
.fai-detail{margin-top:17px;padding:22px}
.fai-detail-head{display:flex;justify-content:space-between;align-items:center;gap:15px}
.fai-table-wrap{overflow-x:auto;margin-top:15px}
.fai-table{border-collapse:collapse;width:100%;font-size:12px;min-width:580px}
.fai-table th{text-align:left;color:var(--fa-muted);background:var(--fa-bg);font-size:11px}
.fai-table th,.fai-table td{padding:12px;border-bottom:1px solid var(--fa-line)}
.fai-table .num{text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums}
.fai-pagination{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:16px;color:var(--fa-muted);font-size:11px}
.fai-markdown p{margin:0 0 9px}.fai-markdown p:last-child{margin-bottom:0}
.fai-markdown ul{list-style:disc;padding-left:22px;margin:8px 0}.fai-markdown ol{list-style:decimal;padding-left:22px;margin:8px 0}
.fai-markdown li{margin:3px 0}.fai-markdown strong{font-weight:850}
.fai-markdown table{display:block;overflow-x:auto;border-collapse:collapse;margin:10px 0;max-width:100%}
.fai-markdown th,.fai-markdown td{border:1px solid var(--fa-line);padding:7px 9px;text-align:left}
.fai-markdown th{font-weight:800}.fai-markdown code{background:var(--fa-soft);padding:1px 4px;border-radius:4px;white-space:break-spaces}
.fai-markdown a{color:var(--fa-accent);text-decoration:underline}
@media(max-width:1120px){.fai-workspace{grid-template-columns:minmax(0,1fr) 260px}}
@media(max-width:900px){.fai-overview,.fai-workspace{grid-template-columns:1fr}.fai-side{grid-template-columns:repeat(2,minmax(0,1fr))}.fai-secure{grid-column:1/-1}}
@media(max-width:610px){.fai-top{align-items:flex-start;flex-direction:column}.fai-stats{grid-template-columns:1fr}.fai-overview{gap:12px}.fai-side{grid-template-columns:1fr}.fai-panel-pad{padding:17px}.fai-stat{padding:17px}.fai-chat-header{align-items:flex-start;flex-direction:column}.fai-dialog{padding:15px;min-height:300px}.fai-chat{min-height:520px}.fai-bubble{max-width:96%}.fai-chart{gap:7px}.fai-chart-amount{font-size:9px}.fai-footnote{flex-direction:column}}
@media(prefers-reduced-motion:reduce){.fai *{scroll-behavior:auto!important;transition:none!important}}

.fai-chat-history{display:flex;align-items:center;gap:6px;padding:8px;border-radius:9px;background:var(--fa-bg)}
.fai-chat-history>button:first-child{flex:1;min-width:0;text-align:left;background:none;border:0;color:var(--fa-text)}
.fai-chat-history strong{display:block;font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.fai-chat-history small{display:block;font-size:10px;color:var(--fa-muted)}
.fai-chat-remove{border:1px solid var(--fa-line);border-radius:7px;background:var(--fa-card);padding:5px 9px;color:var(--fa-muted)}

</style>
</div>