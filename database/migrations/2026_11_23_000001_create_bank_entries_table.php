<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_entries', function (Blueprint $table) {
            $table->id();
            $table->string('ledger', 12)->index(); // KAS, BCA, MANDIRI
            $table->date('entry_date')->index();
            $table->string('document_number', 100)->nullable();
            $table->string('account_code', 50)->nullable()->index();
            $table->string('account_name')->nullable();
            $table->string('invoice')->nullable();
            $table->string('payment_channel', 100)->nullable();
            $table->string('cheque_number', 100)->nullable();
            $table->text('description')->nullable();
            $table->decimal('debit', 20, 2)->default(0);
            $table->decimal('credit', 20, 2)->default(0);
            $table->decimal('source_balance', 20, 2)->nullable();
            $table->string('source', 20)->default('manual');
            $table->string('fingerprint', 64)->unique();
            $table->timestamps();
            $table->index(['ledger', 'entry_date', 'id']);
        });
        Schema::create('bank_ledger_openings', function (Blueprint $table) {
            $table->id();
            $table->string('ledger', 12);
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('amount', 20, 2)->default(0);
            $table->timestamps();
            $table->unique(['ledger', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_ledger_openings');
        Schema::dropIfExists('bank_entries');
    }
};
