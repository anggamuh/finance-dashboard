<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authenticate;
use App\Livewire\Dashboard\Index;
use App\Livewire\Upload\Index as UploadIndex;
use App\Livewire\Accounts\Index as AccountIndex;
use App\Livewire\Reports\OperatingExpense;
use App\Livewire\Reports\OperatingExpenseDetail;
use App\Http\Controllers\ReportPdfController;
use App\Livewire\Transactions\Index as TransactionIndex;
use App\Livewire\Transactions\Create as CreateTransaction;
use App\Livewire\Transactions\Edit as EditTransaction;
use App\Livewire\Accurate\Index as AccurateIndex;
use app\Livewire\Ledger\Index as LedgerIndex;



Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware([Authenticate::class])->group(function () {

    Route::get('/dashboard', Index::class)
        ->name('dashboard');
    Route::get('/upload', UploadIndex::class)
        ->name('upload');
    Route::get('/test-upload', \App\Livewire\TestUpload::class);
    Route::get('/accounts', AccountIndex::class)
        ->name('accounts');
    Route::get('/reports/operating-expense', OperatingExpense::class)
        ->name('reports.operating-expense');
    Route::get('/reports/operating-expense/pdf', [ReportPdfController::class, 'operatingExpense'])
        ->name('reports.operating-expense.pdf');
    Route::get('/reports/operating-expense-detail', OperatingExpenseDetail::class)
        ->name('reports.operating-expense-detail');
    Route::get('/reports/operating-expense-detail/pdf', [ReportPdfController::class, 'operatingExpenseDetail'])
        ->name('reports.operating-expense-detail.pdf');
    Route::get('/transactions', TransactionIndex::class)
        ->name('transactions');
    Route::get('/transactions/create', CreateTransaction::class)
        ->name('transactions.create');
    Route::get('/transactions/{transaction}/edit', EditTransaction::class)
    ->name('transactions.edit');
    Route::view('profile', 'profile')
        ->name('profile');
    Route::get('/accurate', AccurateIndex::class)
    ->name('accurate');
    Route::get('/buku-besar', \App\Livewire\Ledger\Index::class)->name('ledger.index');
    

});

require __DIR__.'/auth.php';


