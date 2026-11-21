<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reconciliation_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('bank_name')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamps();
        });
        Schema::create('reconciliation_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('reconciliation_batches')->cascadeOnDelete();
            $table->string('source', 12); // bank or ledger
            $table->date('posted_at');
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->string('direction', 6); // in or out
            $table->decimal('amount', 18, 2);
            $table->string('fingerprint', 64);
            $table->foreignId('matched_entry_id')->nullable()->constrained('reconciliation_entries')->nullOnDelete();
            $table->string('status', 16)->default('unmatched'); // unmatched, suggested, matched
            $table->unsignedTinyInteger('confidence')->nullable();
            $table->timestamps();
            $table->index(['batch_id','source','status']);
            $table->index(['batch_id','direction','amount']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_entries');
        Schema::dropIfExists('reconciliation_batches');
    }
};