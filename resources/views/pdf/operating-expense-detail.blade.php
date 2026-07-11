<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans;
    font-size:12px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:8px;
}

th{
    border-top:1px solid #000;
    border-bottom:1px solid #000;
}

.total{
    font-weight:bold;
    border-top:2px solid #000;
}

.text-right{
    text-align:right;
}

.text-center{
    text-align:center;
}

.parent-row {
    font-weight:bold;
    background-color:#f0f0f0;
}

.parent-row td{
    border-bottom:1px solid #ddd;
}

.child-row {
    background-color:#fafafa;
    font-size:11px;
    color:#333;
}

.child-row td{
    border-bottom:1px solid #eee;
}

.child-name{
    padding-left:8px;
}

.col-code{
    width:80px;
}

.col-total{
    width:180px;
}

</style>

</head>

<body>

<h2 class="text-center">
SECOND STAR
</h2>

<h3 class="text-center">
LAPORAN BEBAN OPERASIONAL DETAIL
</h3>

<p class="text-center">
Periode
{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
-
{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
</p>

<br>

<table>

<thead>

<tr>

<th class="col-code">
Kode
</th>

<th>
Nama Akun
</th>

<th class="col-total text-right">
Total
</th>

</tr>

</thead>

<tbody>

@forelse($expenses as $expense)

    <tr class="parent-row">
        <td class="col-code">
            {{ $expense->code }}
        </td>
        <td>
            {{ $expense->name }}
        </td>
        <td class="col-total text-right">
            Rp{{ number_format($expense->total, 0, ',', '.') }}
        </td>
    </tr>

    @forelse($expense->children as $child)
        <tr class="child-row">
            <td class="col-code"></td>
            <td class="child-name">
                {{ $child->name }}
            </td>
            <td class="col-total text-right">
                Rp{{ number_format($child->total, 0, ',', '.') }}
            </td>
        </tr>
    @empty
    @endforelse

@empty

    <tr>
        <td colspan="3" class="text-center">
            Tidak ada data
        </td>
    </tr>

@endforelse

<tr class="total">
    <td colspan="2">
        Total Beban Operasional
    </td>
    <td class="text-right">
        Rp{{ number_format($grandTotal, 0, ',', '.') }}
    </td>
</tr>

</tbody>

</table>

</body>

</html>
