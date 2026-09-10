<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Superseded by the following migration, which safely renames the
        // existing `date` column and backfills `date_to` in one operation.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
