<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accurate_imports', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->string('period')->nullable();

            $table->string('file');

            $table->string('original_name');

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accurate_imports');
    }
};