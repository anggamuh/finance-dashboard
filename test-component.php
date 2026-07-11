<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use App\Models\Transaction;

$transactions = Transaction::count();
echo "Total transactions: " . $transactions . "\n";

$thisMonth = Transaction::whereBetween('transaction_date', [
    now()->startOfMonth()->format('Y-m-d'),
    now()->endOfMonth()->format('Y-m-d')
])->count();
echo "This month transactions: " . $thisMonth . "\n";

$expenses = Transaction::query()
    ->whereNotIn('account_name', ['Bank BCA', 'Petty Cash'])
    ->where(function ($q) {
        $q->whereNull('description')
            ->orWhere('description', 'not like', 'Saldo per%');
    })
    ->whereBetween('transaction_date', [
        now()->startOfMonth()->format('Y-m-d'),
        now()->endOfMonth()->format('Y-m-d')
    ])
    ->where('debit', '>', 0)
    ->selectRaw('LEFT(account_code,4) as account_code, SUM(debit) as total')
    ->groupByRaw('LEFT(account_code,4)')
    ->orderBy('account_code')
    ->get();

echo "Expense groups: " . $expenses->count() . "\n";
foreach ($expenses as $expense) {
    echo "- Code: " . $expense->account_code . ", Total: " . $expense->total . "\n";
}
