<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $code }} - SecondStar</title>
    <style>
        /* CSS MANDIRI: tidak perlu npm run build, Tailwind, atau font eksternal. */
        :root {
            --navy: #152b39;
            --navy-deep: #0e202b;
            --green: #158666;
            --green-bright: #23b38a;
            --mint: #e9f7f1;
            --ink: #21343f;
            --muted: #758994;
            --line: #e5ecee;
            --page: #f0f3f5;
        }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            margin: 0;
            padding: 34px 16px 54px;
            background: var(--page);
            color: var(--ink);
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            line-height: 1.55;
        }
        .toolbar {
            width: 100%;
            max-width: 860px;
            margin: 0 auto 17px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .toolbar-caption { font-size: 12px; color: #647884; }
        .toolbar-actions { display: flex; gap: 9px; }
        .action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 15px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            border-radius: 9px;
            text-decoration: none;
            border: 1px solid #d9e3e6;
            background: #fff;
            color: var(--navy);
            cursor: pointer;
        }
        .action-primary { background: var(--green); border-color: var(--green); color: #fff; }
        .action:hover { filter: brightness(.96); }
        .action svg { width: 15px; height: 15px; flex: none; }
        .sheet {
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 14px 45px rgba(17, 39, 50, .10);
        }
        .stripe { height: 6px; background: linear-gradient(90deg, var(--green), #50ceb2 50%, #b0ead6); }
        .masthead {
            position: relative;
            overflow: hidden;
            background: var(--navy);
            color: #fff;
            padding: 37px 40px 33px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 26px;
        }
        .masthead::after {
            content: "";
            position: absolute;
            right: -75px;
            bottom: -150px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            border: 48px solid rgba(87, 207, 162, .075);
            pointer-events: none;
        }
        .brand, .head-right { position: relative; z-index: 1; }
        .brand-line { display: flex; align-items: center; gap: 13px; }
        .mark {
            width: 48px; height: 48px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 12px;
            background: rgba(255,255,255,.095);
            font-weight: 900;
            letter-spacing: -2px;
            font-size: 21px;
            color: #79efcb;
        }
        .brand-name { font-size: 22px; font-weight: 800; letter-spacing: -.75px; line-height: 1.2; }
        .brand-sub { color: #adc1c9; font-size: 11px; margin-top: 5px; letter-spacing: .2px; }
        .head-right { text-align: right; }
        .eyebrow { text-transform: uppercase; font-size: 10px; letter-spacing: 2px; font-weight: 700; color: #a5dccc; }
        .invoice-heading { font-weight: 800; font-size: 32px; letter-spacing: -1px; line-height: 1.12; margin-top: 8px; }
        .invoice-pill {
            display: inline-block;
            margin-top: 11px;
            padding: 5px 12px;
            border: 1px solid rgba(105, 231, 185, .34);
            border-radius: 30px;
            background: rgba(61, 173, 132, .15);
            color: #b1f4da;
            max-width: 100%;
            overflow-wrap: anywhere;
            font-size: 12px;
            font-weight: 700;
        }
        .body-content { padding: 30px 40px 32px; }
        .info-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }
        .info-card {
            min-height: 107px;
            padding: 17px 19px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fbfcfc;
        }
        .info-card.accent { border-color: #d9eee5; background: #f5fbf8; }
        .field-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            color: #7e919b;
        }
        .field-value { margin-top: 7px; font-size: 16px; font-weight: 750; color: var(--ink); overflow-wrap: anywhere; }
        .field-description { margin-top: 3px; font-size: 11px; color: #8b9ca5; }
        .section-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }
        .section-title { font-size: 16px; font-weight: 800; letter-spacing: -.2px; color: var(--navy); }
        .section-subtitle { font-size: 11px; color: #8c9da5; margin-top: 2px; }
        .count-pill {
            border-radius: 999px;
            background: var(--mint);
            color: #177658;
            font-size: 11px;
            padding: 6px 12px;
            font-weight: 800;
        }
        .table-wrap { width: 100%; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        thead th {
            padding: 13px 10px;
            border-bottom: 1px solid #d5e7df;
            background: #eff8f4;
            text-align: left;
            color: #28785b;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .65px;
        }
        thead th:first-child { border-radius: 8px 0 0 0; }
        thead th:last-child { border-radius: 0 8px 0 0; }
        tbody td { padding: 16px 10px; border-bottom: 1px solid var(--line); vertical-align: top; }
        tbody tr:last-child td { border-bottom: 1px solid #dce7e7; }
        .c-number { width: 7%; color: #93a4ae; }
        .c-item { width: 35%; overflow-wrap: anywhere; }
        .c-qty { width: 11%; }
        .c-price { width: 22%; }
        .c-total { width: 25%; }
        .num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
        .item-name { font-weight: 750; color: #263c45; }
        .line-amount { font-weight: 800; color: #15775a; }
        .soft { color: #71838c; }
        .below-table {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 25px;
            padding-top: 22px;
        }
        .notes {
            min-width: 0;
            flex: 1;
            padding: 15px 17px;
            background: #f8fafb;
            border: 1px solid var(--line);
            border-radius: 9px;
        }
        .notes-title { font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; color: #8196a0; }
        .notes-body { font-size: 12px; margin-top: 7px; color: #526772; white-space: pre-wrap; overflow-wrap: anywhere; }
        .summary { flex: 0 0 293px; }
        .summary-row { display: flex; justify-content: space-between; gap: 15px; font-size: 12px; color: #748994; margin: 0 0 11px; }
        .summary-row strong { color: var(--ink); font-weight: 750; }
        .grand-total {
            border-radius: 10px;
            padding: 16px 17px;
            background: var(--navy);
            color: #fff;
        }
        .grand-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1.1px; color: #b9ccc9; font-weight: 800; }
        .grand-value { margin-top: 7px; font-size: 23px; font-weight: 850; letter-spacing: -.5px; color: #8cf0c7; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .sheet-footer {
            border-top: 1px solid var(--line);
            background: #fbfcfc;
            padding: 22px 40px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-end;
        }
        .footer-label { color: #8797a2; font-size: 10px; text-transform: uppercase; font-weight: 800; letter-spacing: 1px; }
        .footer-code { font-weight: 800; color: var(--navy); margin-top: 4px; overflow-wrap: anywhere; }
        .footer-copy { color: #7b909b; font-size: 11px; margin-top: 5px; }
        .footer-brand { font-size: 16px; font-weight: 900; color: var(--green); letter-spacing: -.5px; white-space: nowrap; }
        @media (max-width: 620px) {
            body { padding: 13px 10px 30px; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-actions { display: grid; grid-template-columns: 1fr 1fr; }
            .masthead { padding: 26px 22px; gap: 16px; }
            .brand-line { gap: 9px; }
            .mark { width: 39px; height: 39px; font-size: 17px; }
            .brand-name { font-size: 18px; }
            .brand-sub { font-size: 10px; }
            .invoice-heading { font-size: 23px; }
            .eyebrow { font-size: 9px; letter-spacing: 1px; }
            .invoice-pill { padding: 4px 9px; font-size: 10px; }
            .body-content { padding: 22px 16px 24px; }
            .info-grid { grid-template-columns: 1fr 1fr; gap: 9px; margin-bottom: 24px; }
            .info-card { padding: 12px 11px; min-height: 95px; }
            .field-value { font-size: 13px; }
            .table-wrap { overflow-x: auto; }
            table { min-width: 540px; }
            .below-table { flex-direction: column; gap: 16px; }
            .summary, .notes { width: 100%; flex: auto; }
            .sheet-footer { padding: 20px 16px; }
        }
        @page { size: A4 portrait; margin: 12mm; }
        @media print {
            html, body { padding: 0 !important; margin: 0 !important; background: #fff !important; font-size: 11px; }
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .toolbar { display: none !important; }
            .sheet { max-width: none; width: 100%; border-radius: 0; box-shadow: none; overflow: visible; }
            .stripe { height: 4px; }
            .masthead { padding: 23px 27px 22px; }
            .body-content { padding: 23px 27px; }
            .info-grid { margin-bottom: 22px; }
            .info-card { min-height: 89px; padding: 12px 14px; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; page-break-inside: avoid; }
            .below-table, .grand-total, .notes { break-inside: avoid; page-break-inside: avoid; }
            .sheet-footer { padding: 16px 27px; }
            .grand-value { font-size: 19px; }
            .table-wrap { overflow: visible; }
            table { min-width: 0; }
        }
    </style>
</head>
<body>
@php
    // Jika rincian baru tersedia, tampilkan SEMUA item dari relasi.
    // Hanya gunakan data kolom lama jika bon memang belum memiliki rincian.
    $detailItems = $cost->items;
    $hasDetails = $detailItems->isNotEmpty();

    $lines = $hasDetails
        ? $detailItems
        : collect([(object) [
            'item_name' => $cost->item_name ?: 'Biaya pembelian barang',
            'qty' => $cost->qty ?: 1,
            'price' => $cost->price ?: 0,
            'subtotal' => $cost->total,
        ]]);

    $qtyTotal = $lines->sum(fn ($line) => (float) $line->qty);
    $subtotal = $lines->sum(fn ($line) => (float) (
        $line->subtotal ?? ((float) $line->qty * (float) $line->price)
    ));

    // Nilai yang disimpan di header bon tetap menjadi total resmi invoice.
    $grandTotal = (float) $cost->total;

    $formatQty = function ($value) {
        return rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',');
    };
@endphp

    <div class="toolbar">
        <div class="toolbar-caption">SecondStar <span style="color:#a0acb1;">/</span> Dokumen biaya pembelian</div>
        <div class="toolbar-actions">
            <a class="action" href="{{ url('/internal-operational-costs') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                Kembali
            </a>
            <button type="button" class="action action-primary" onclick="window.print()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z"/><path d="M18 12h.01"/></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <main class="sheet">
        <div class="stripe"></div>

        <header class="masthead">
            <div class="brand">
                <div class="brand-line">
                    <div class="mark">SS</div>
                    <div>
                        <div class="brand-name">SecondStar</div>
                        <div class="brand-sub">Biaya Operasional Internal</div>
                    </div>
                </div>
            </div>
            <div class="head-right">
                <div class="eyebrow">Dokumen pembelian</div>
                <div class="invoice-heading">INVOICE</div>
                <div class="invoice-pill">No. {{ $code }}</div>
            </div>
        </header>

        <div class="body-content">
            <section class="info-grid" aria-label="Informasi bon">
                <div class="info-card">
                    <div class="field-label">Supplier / Penjual</div>
                    <div class="field-value">{{ $cost->supplier ?: '-' }}</div>
                    <div class="field-description">Sumber pembelian barang</div>
                </div>
                <div class="info-card accent">
                    <div class="field-label">Tanggal Bon</div>
                    <div class="field-value">{{ $cost->transaction_date->locale('id')->translatedFormat('d F Y') }}</div>
                    <div class="field-description">Referensi: {{ $code }}</div>
                </div>
            </section>

            <section aria-label="Rincian barang">
                <div class="section-top">
                    <div>
                        <div class="section-title">Rincian Pembelian</div>
                        <div class="section-subtitle">Daftar barang, kuantitas, dan harga satuan pada bon ini</div>
                    </div>
                    <div class="count-pill">{{ $lines->count() }} item</div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="c-number">No.</th>
                                <th class="c-item">Nama Item / Deskripsi</th>
                                <th class="c-qty num">Qty</th>
                                <th class="c-price num">Harga Satuan</th>
                                <th class="c-total num">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lines as $line)
                                <tr>
                                    <td class="c-number">{{ $loop->iteration }}</td>
                                    <td class="c-item"><span class="item-name">{{ $line->item_name }}</span></td>
                                    <td class="c-qty num soft">{{ $formatQty($line->qty) }}</td>
                                    <td class="c-price num soft">Rp {{ number_format((float) $line->price, 0, ',', '.') }}</td>
                                    <td class="c-total num line-amount">Rp {{ number_format((float) ($line->subtotal ?? ((float) $line->qty * (float) $line->price)), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="below-table">
                @if(filled($cost->notes))
                    <section class="notes" aria-label="Catatan transaksi">
                        <div class="notes-title">Catatan</div>
                        <div class="notes-body">{{ $cost->notes }}</div>
                    </section>
                @else
                    <div class="notes">
                        <div class="notes-title">Keterangan</div>
                        <div class="notes-body">Dokumen rincian biaya pembelian barang untuk pencatatan operasional internal.</div>
                    </div>
                @endif

                <section class="summary" aria-label="Ringkasan total">
                    <div class="summary-row"><span>Jumlah item</span><strong>{{ $lines->count() }}</strong></div>
                    <div class="summary-row"><span>Jumlah Qty</span><strong>{{ $formatQty($qtyTotal) }}</strong></div>
                    <div class="summary-row"><span>Subtotal</span><strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                    <div class="grand-total">
                        <div class="grand-label">Total Biaya Pembelian</div>
                        <div class="grand-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                    </div>
                </section>
            </div>
        </div>

        <footer class="sheet-footer">
            <div>
                <div class="footer-label">Nomor Dokumen</div>
                <div class="footer-code">{{ $code }}</div>
                <div class="footer-copy">Dicetak dari sistem Finance Dashboard SecondStar</div>
            </div>
            <div class="footer-brand">SecondStar<span style="color:var(--green-bright)">.</span></div>
        </footer>
    </main>
</body>
</html>
