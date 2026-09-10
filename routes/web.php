<?php

use App\Http\Controllers\ReportPdfController;
use App\Livewire\Accounts\Index as AccountIndex;
use App\Livewire\Accurate\Index as AccurateIndex;
use App\Livewire\Closing\Index as ClosingIndex;
use App\Livewire\Dashboard\Index;
use App\Livewire\InternalOperationalCosts\Index as InternalOperationalCostIndex;
use App\Livewire\Reports\OperatingExpense;
use App\Livewire\Reports\OperatingExpenseDetail;
use App\Livewire\Revenues\Index as RevenueIndex;
use App\Livewire\TestUpload;
use App\Livewire\Transactions\Create as CreateTransaction;
use App\Livewire\Transactions\Edit as EditTransaction;
use App\Livewire\Transactions\Index as TransactionIndex;
use App\Livewire\Upload\Index as UploadIndex;
use App\Livewire\CashBook\Index as CashBookIndex;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::middleware([Authenticate::class])->group(function () {

    Route::get('/dashboard', Index::class)
        ->name('dashboard');
    Route::get('/upload', UploadIndex::class)
        ->name('upload');
    Route::get('/test-upload', TestUpload::class);
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
    Route::get('/buku-besar', App\Livewire\Ledger\Index::class)->name('ledger.index');
    Route::get('/buku-kas', CashBookIndex::class)
    ->name('cash-book');
    Route::get('/internal-operational-costs', InternalOperationalCostIndex::class)
        ->name('internal-operational-costs');
    Route::get('/revenues', RevenueIndex::class)
        ->name('revenues');
    Route::get('/closing', ClosingIndex::class)
        ->name('closing');
    Route::get('/proofs/{path}', [\App\Http\Controllers\ProofFileController::class, 'show'])
    ->where('path', '.*')
    ->name('proofs.show');
        

});

require __DIR__.'/auth.php';
