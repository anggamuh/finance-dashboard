<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_operational_costs', function (Blueprint $table) {

            $table->id();

            $table->date('transaction_date');

            $table->string('invoice_number')->nullable(); // nomor bon

            $table->string('supplier')->nullable();

            $table->string('item_name');

            $table->decimal('qty', 12, 2)->default(1);

            $table->decimal('price', 15, 2);

            $table->decimal('total', 15, 2);

            $table->text('notes')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_operational_costs');
    }
};
