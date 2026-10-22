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
        if (Schema::hasTable('criteria_categories') && ! Schema::hasColumn('criteria_categories', 'year')) {
            Schema::table('criteria_categories', function (Blueprint $table) {
                $table->unsignedSmallInteger('year')->default(2026)->index()->after('id');
            });

            // Pastikan data kriteria yang ada saat ini ditandai sebagai tahun 2026
            try {
                DB::table('criteria_categories')
                    ->whereNull('year')
                    ->orWhere('year', 0)
                    ->update(['year' => 2026]);
            } catch (Throwable $e) {
                // Ignore if empty
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('criteria_categories') && Schema::hasColumn('criteria_categories', 'year')) {
            Schema::table('criteria_categories', function (Blueprint $table) {
                $table->dropColumn('year');
            });
        }
    }
};
