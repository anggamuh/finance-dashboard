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

td{
    border-bottom:1px solid #eee;
}

.total{
    font-weight:bold;
    border-top:2px solid #000;
}

.total td{
    border-bottom:none;
}

.text-right{
    text-align:right;
}

.text-center{
    text-align:center;
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
LAPORAN BEBAN OPERASIONAL
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

@foreach($expenses as $expense)

<tr>

<td class="col-code">
{{ $expense->account_code }}
</td>

<td>
{{ $expense->account_name }}
</td>

<td class="col-total text-right">
Rp {{ number_format($expense->total,0,',','.') }}
</td>

</tr>

@endforeach

<tr class="total">

<td colspan="2">

TOTAL BEBAN OPERASIONAL

</td>

<td class="text-right">

Rp {{ number_format($grandTotal,0,',','.') }}

</td>

</tr>

</tbody>

</table>

<br><br>

<table>

<tr>

<td>

Dicetak :
{{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }}

</td>

{{-- <td class="text-right">

Bandung,
{{ now()->format('d M Y') }}

<br><br><br><br>

____________________

<br>

Owner

</td> --}}

</tr>

</table>

</body>

</html>