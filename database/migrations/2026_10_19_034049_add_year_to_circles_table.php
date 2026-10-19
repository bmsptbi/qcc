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
        Schema::table('circles', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->nullable()->after('assessment_period_id')->index();
        });

        // Backfill data tahun dari tabel assessment_periods
        DB::statement('UPDATE circles JOIN assessment_periods ON circles.assessment_period_id = assessment_periods.id SET circles.year = assessment_periods.year WHERE circles.year IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circles', function (Blueprint $table) {
            $table->dropColumn('year');
        });
    }
};
