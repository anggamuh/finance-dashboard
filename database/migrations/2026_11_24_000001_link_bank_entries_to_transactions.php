<?php

use App\Models\BankEntry;
use App\Services\BankTransactionSync;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('bank_entry_id')->nullable()->unique();
        });

        // Make imports performed before this upgrade visible in the existing Transactions menu.
        BankEntry::query()->orderBy('id')->chunkById(250, function ($entries) {
            $sync = app(BankTransactionSync::class);
            foreach ($entries as $entry) {
                $sync->sync($entry);
            }
        });
    }

    public function down(): void
    {
        // Remove only linked copies; the original Kas/Bank records remain intact.
        \App\Models\Transaction::withTrashed()->whereNotNull('bank_entry_id')->forceDelete();
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['bank_entry_id']);
            $table->dropColumn('bank_entry_id');
        });
    }
};
