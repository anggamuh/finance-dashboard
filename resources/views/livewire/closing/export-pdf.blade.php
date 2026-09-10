<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Closing Tahunan {{ $year }}</title>
    <style>
        @page { margin: 20px 26px; }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 9.5px;
            color: #1f2937;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0 0 3px 0;
            color: #059669;
        }

        .header p {
            margin: 0;
            font-size: 9.5px;
            color: #6b7280;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        table.main thead th {
            background-color: #059669;
            color: #fff;
            padding: 5px 5px;
            font-size: 8.5px;
            text-transform: uppercase;
        }

        table.main th.text-right,
        table.main td.text-right { text-align: right; }

        table.main th.text-center,
        table.main td.text-center { text-align: center; }

        table.main tbody td {
            padding: 4px 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        table.main tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table.main tfoot td {
            padding: 6px 5px;
            font-weight: bold;
            border-top: 2px solid #059669;
        }

        .cost { color: #b91c1c; }
        .plus { color: #059669; }

        /* ---------- ringkasan keuangan: 3 tahap, sama seperti halaman utama ---------- */
        .summary {
            width: 100%;
            margin-top: 10px;
            border-collapse: separate;
            border-spacing: 14px 0;
        }

        .summary td {
            width: 50%;
            vertical-align: top;
        }

        .summary-box {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 14px;
        }

        .summary-box h3 {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #111827;
        }

        .stage {
            background-color: #f9fafb;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .stage.final {
            background-color: #ecfdf5;
        }

        .stage-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #9ca3af;
            margin-bottom: 5px;
        }

        .stage.final .stage-title { color: #059669; }

        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 2.5px 0;
            font-size: 9.5px;
        }

        .summary-line .val { font-weight: 600; }
        .summary-line .val.neg { color: #b91c1c; }

        .summary-line.total {
            border-top: 1px solid #d1d5db;
            margin-top: 3px;
            padding-top: 4px;
            font-weight: bold;
        }

        .summary-line.grand-total {
            border-top: 1px solid #a7f3d0;
            margin-top: 3px;
            padding-top: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .summary-line.grand-total .val {
            color: #2563eb;
        }

        .stock-box .summary-line {
            font-size: 10px;
            padding: 4px 0;
        }

        .stock-final {
            border-top: 2px solid #059669;
            margin-top: 6px;
            padding-top: 6px;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .stock-final .val { color: #059669; }

        .footer {
            margin-top: 14px;
            text-align: right;
            font-size: 8.5px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Closing Tahunan</h1>
        <p>Tahun {{ $year }} &nbsp;•&nbsp; Dicetak {{ $generatedAt }}</p>
    </div>

    <table class="main">
        <thead>
            <tr>
                <th width="10%">Bulan</th>
                <th width="14%" class="text-right">Omzet</th>
                <th width="14%" class="text-right">Pembelian</th>
                <th width="14%" class="text-right">Biaya Operasional</th>
                <th width="12%" class="text-center">Stok Awal</th>
                <th width="12%" class="text-center">Stok Masuk</th>
                <th width="12%" class="text-center">Stok Keluar</th>
                <th width="12%" class="text-center">Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['month'] }}</td>
                    <td class="text-right plus">Rp {{ number_format($row['omzet'], 0, ',', '.') }}</td>
                    <td class="text-right cost">Rp {{ number_format($row['internal'], 0, ',', '.') }}</td>
                    <td class="text-right cost">Rp {{ number_format($row['operasional'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($row['stock_start'], 0, ',', '.') }}</td>
                    <td class="text-center plus">{{ number_format($row['stock_in'], 0, ',', '.') }}</td>
                    <td class="text-center cost">{{ number_format($row['stock_out'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($row['stock_end'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:16px; color:#9ca3af;">
                        Belum ada data untuk tahun ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($rows->count())
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="text-right">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalInternal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalOperational, 0, ',', '.') }}</td>
                    <td class="text-center">&mdash;</td>
                    <td class="text-center">{{ number_format($rows->sum('stock_in'), 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($rows->sum('stock_out'), 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($stockEnd, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <table class="summary">
        <tr>
            <td>
                <div class="summary-box">
                    <h3>Ringkasan Keuangan</h3>

                    {{-- TAHAP 1: HPP --}}
                    <div class="stage">
                        <div class="stage-title">1. Harga Pokok Penjualan (HPP)</div>
                        <div class="summary-line">
                            <span>Stok Awal ({{ number_format($stockAwal,0,',','.') }} &times; Rp 19.500)</span>
                            <span class="val">Rp {{ number_format($stockAwalRupiah, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-line">
                            <span>+ Pembelian</span>
                            <span class="val">Rp {{ number_format($totalInternal, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-line">
                            <span>- Stok Akhir ({{ number_format($stockEnd,0,',','.') }} &times; Rp 19.500)</span>
                            <span class="val neg">- Rp {{ number_format($stockEndRupiah, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-line total">
                            <span>= HPP (Hasil 1)</span>
                            <span class="val">Rp {{ number_format($hasil1, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- TAHAP 2: LABA KOTOR --}}
                    <div class="stage">
                        <div class="stage-title">2. Laba Kotor</div>
                        <div class="summary-line">
                            <span>Omzet</span>
                            <span class="val">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-line">
                            <span>- HPP (Hasil 1)</span>
                            <span class="val neg">- Rp {{ number_format($hasil1, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-line total">
                            <span>= Laba Kotor (Hasil 2)</span>
                            <span class="val">Rp {{ number_format($hasil2, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- TAHAP 3: LABA BERSIH --}}
                    <div class="stage final">
                        <div class="stage-title">3. Laba Bersih</div>
                        <div class="summary-line">
                            <span>Laba Kotor (Hasil 2)</span>
                            <span class="val">Rp {{ number_format($hasil2, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-line">
                            <span>- Biaya Operasional</span>
                            <span class="val neg">- Rp {{ number_format($totalOperational, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-line grand-total">
                            <span>LABA BERSIH</span>
                            <span class="val">Rp {{ number_format($profit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <div class="summary-box stock-box">
                    <h3>Ringkasan Stok</h3>

                    <div class="summary-line">
                        <span>Stok Awal Tahun</span>
                        <span class="val">{{ number_format($stockAwal, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-line">
                        <span>Total Stok Masuk</span>
                        <span class="val">{{ number_format($rows->sum('stock_in'), 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-line">
                        <span>Total Stok Keluar</span>
                        <span class="val">{{ number_format($rows->sum('stock_out'), 0, ',', '.') }}</span>
                    </div>
                    <div class="stock-final">
                        <span>Stok Akhir Tahun</span>
                        <span class="val">{{ number_format($stockEnd, 0, ',', '.') }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Laporan dibuat otomatis oleh sistem.
    </div>

</body>
</html>