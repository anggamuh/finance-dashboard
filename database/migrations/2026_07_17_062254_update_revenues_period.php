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
    Schema::table('revenues', function (Blueprint $table) {

        $table->date('date_from')->after('id');

        $table->date('date_to')->after('date_from');

    });

    DB::statement("
        UPDATE revenues
        SET
            date_from = date,
            date_to = date
    ");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
