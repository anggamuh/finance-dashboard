<?php

use App\Livewire\Upload\Index;
use App\Models\Import;
use App\Models\Transaction;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

it('imports transactions from a csv file', function () {
    if (!extension_loaded('pdo_sqlite')) {
        $this->markTestSkipped('PDO SQLite extension is required for this test.');
    }

    $csv = <<<'CSV'
account_code,account_name,transaction_number,transaction_date,transaction_type,description,debit,credit
ACC001,Rekening Utama,TRX001,01/06/2026,Debit,Contoh transaksi,1000000,0
ACC002,Rekening Cadangan,TRX002,02/06/2026,Kredit,Contoh transaksi,0,500000
CSV;

    $tempPath = tempnam(sys_get_temp_dir(), 'upload');
    file_put_contents($tempPath, $csv);

    $file = new UploadedFile(
        $tempPath,
        'sample.csv',
        'text/csv',
        null,
        true
    );

    Livewire::test(Index::class)
        ->set('file', $file)
        ->call('upload')
        ->assertHasNoErrors();

    expect(Import::count())->toBe(1)
        ->and(Transaction::count())->toBe(2)
        ->and(Transaction::first()->account_code)->toBe('ACC001');
});
