<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_ai_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('month',7);
            $table->string('title',100);
            $table->longText('messages');
            $table->timestamps();
            $table->index(['user_id','month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_ai_chats');
    }
};
