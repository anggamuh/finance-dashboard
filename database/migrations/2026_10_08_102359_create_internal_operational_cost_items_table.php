
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buat tabel hanya jika belum tersedia
        if (!Schema::hasTable('internal_operational_cost_items')) {

            Schema::create('internal_operational_cost_items', function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger(
                    'internal_operational_cost_id'
                );

                $table->string('item_name');

                $table->decimal('qty', 12, 2)->default(1);

                $table->decimal('price', 15, 2)->default(0);

                $table->decimal('subtotal', 17, 2)->default(0);

                $table->timestamps();
            });
        }

        // Gunakan nama foreign key pendek
        Schema::table('internal_operational_cost_items', function (Blueprint $table) {

            $table->foreign(
                'internal_operational_cost_id',
                'ioc_items_cost_fk'
            )
            ->references('id')
            ->on('internal_operational_costs')
            ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_operational_cost_items');
    }
};
