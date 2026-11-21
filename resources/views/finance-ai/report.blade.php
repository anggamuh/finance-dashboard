<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $report->title }}</title>
<style>
@page{size:A4;margin:14mm}*{box-sizing:border-box}body{font:13px/1.55 Arial,Helvetica,sans-serif;color:#183042;margin:0 auto;max-width:1020px;padding:20px;background:#fff}
header{border-bottom:3px solid #0b9274;padding-bottom:16px;display:flex;justify-content:space-between;align-items:flex-start}
h1{font-size:26px;letter-spacing:-.5px;margin:0}h2{font-size:15px;color:#0a795f;margin:23px 0 10px}.muted{color:#607482;font-size:11px}.stamp{background:#eaf8f2;padding:9px 12px;border-radius:8px}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:20px}.stat{border:1px solid #dbe8e9;padding:14px;border-radius:10px}.stat strong{display:block;font-size:18px;margin-top:6px}
table{width:100%;border-collapse:collapse;font-size:12px}th,td{padding:8px;border-bottom:1px solid #e0e8ec;text-align:left}th{background:#edf6f2}.right{text-align:right}.note{background:#f6f9fa;padding:10px 13px;border-left:3px solid #0b9274;margin:9px 0}button{padding:10px 15px;background:#0b9274;color:white;border:none;border-radius:7px;cursor:pointer}
@media print{body{padding:0}.no-print{display:none!important}tr{break-inside:avoid}.stats{break-inside:avoid}}@media(max-width:600px){.stats{grid-template-columns:1fr}header{display:block}}
</style>
</head>
<body>
<header>
<div><div class="muted">SECOND STAR · FINANCE INTELLIGENCE</div><h1>{{ $report->title }}</h1><div class="muted">Snapshot tersimpan {{ $report->created_at->format('d/m/Y H:i') }} · Data ringkasan, bukan audit resmi</div></div>
<div class="no-print"><button type="button" onclick="window.print()">Cetak / Simpan PDF</button></div>
</header>
<div class="stats">
<div class="stat"><span class="muted">Beban operasional</span><strong>Rp {{ number_format($data['expenses'],0,',','.') }}</strong></div>
<div class="stat"><span class="muted">Bulan sebelumnya</span><strong>Rp {{ number_format($data['previous_expenses'],0,',','.') }}</strong></div>
<div class="stat"><span class="muted">Perubahan</span><strong>Rp {{ number_format($data['change'],0,',','.') }}</strong></div>
</div>
<h2>Rincian akun beban</h2>
<table><thead><tr><th>Kode</th><th>Akun</th><th class="right">Transaksi</th><th class="right">Total</th></tr></thead><tbody>
@forelse($data['accounts'] as $account)
<tr><td>{{ $account['code'] }}</td><td>{{ $account['name'] }}</td><td class="right">{{ $account['count'] }}</td><td class="right">Rp {{ number_format($account['amount'],0,',','.') }}</td></tr>
@empty<tr><td colspan="4">Belum ada data akun.</td></tr>@endforelse
</tbody></table>
<h2>Catatan keuangan</h2>
@foreach($data['insights'] as $insight)<div class="note"><strong>{{ $insight['title'] }}</strong><div>{{ $insight['detail'] }}</div></div>@endforeach
<h2>Indikasi anomali (perlu verifikasi)</h2>
@if(empty($data['anomaly_signals']))<p class="muted">Tidak ada sinyal terdeteksi dengan aturan pemeriksaan saat ini.</p>@else
<table><thead><tr><th>Jenis</th><th>Temuan</th></tr></thead><tbody>
@foreach($data['anomaly_signals'] as $item)<tr><td>{{ $item['label'] }}</td><td>{{ $item['message'] }}</td></tr>@endforeach
</tbody></table>@endif
<h2>Ringkasan rekonsiliasi</h2>
@if(empty($data['reconciliation']))<p class="muted">Tidak ada periode rekonsiliasi yang sesuai.</p>@else
<table><thead><tr><th>Periode</th><th>Bank</th><th class="right">Matched</th><th class="right">Saran</th><th class="right">Belum cocok</th></tr></thead><tbody>
@foreach($data['reconciliation'] as $row)<tr><td>{{ $row['label'] }}</td><td>{{ $row['bank'] }}</td><td class="right">{{ $row['matched'] }}</td><td class="right">{{ $row['suggested'] }}</td><td class="right">{{ $row['unmatched_bank'] }}</td></tr>@endforeach
</tbody></table>@endif
<p class="muted" style="margin-top:28px">{{ $data['disclaimer'] }}</p>
</body>
</html>
