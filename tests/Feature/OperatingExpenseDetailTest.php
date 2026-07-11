<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;

test('operating expense detail component can render', function () {
    $user = User::factory()->create();

    $parentAccount = Account::create([
        'code' => '5311',
        'name' => 'Gaji & Tunjangan',
        'type' => 'expense',
    ]);

    $childAccount = Account::create([
        'code' => '531101',
        'name' => 'Gaji Karyawan',
        'type' => 'expense',
        'parent_id' => $parentAccount->id,
    ]);

    Transaction::create([
        'account_code' => '531101',
        'account_name' => 'Gaji Karyawan',
        'transaction_number' => 'TRX001',
        'transaction_date' => now(),
        'transaction_type' => 'Debit',
        'description' => 'Gaji',
        'debit' => 44145000,
        'credit' => 0,
    ]);

    $response = $this->actingAs($user)->get(route('reports.operating-expense-detail'));
    $response->assertStatus(200);
    $response->assertSee('Laporan Beban Operasional Detail');
});
