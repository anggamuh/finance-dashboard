<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('revenues', function (Blueprint $table) {
            $table->renameColumn('date', 'date_from');
            $table->date('date_to')->nullable()->after('date_from');
        });

        // Backfill date_to for any existing rows so old data still shows a valid range.
        DB::table('revenues')->whereNull('date_to')->update([
            'date_to' => DB::raw('date_from'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revenues', function (Blueprint $table) {
            $table->dropColumn('date_to');
            $table->renameColumn('date_from', 'date');
        });
    }
};
