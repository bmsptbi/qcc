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
        if (Schema::hasTable('circles')) {
            try {
                DB::statement("ALTER TABLE circles MODIFY status VARCHAR(50) NOT NULL DEFAULT 'submitted'");
            } catch (Throwable $e) {
                try {
                    Schema::table('circles', function (Blueprint $table) {
                        $table->string('status', 50)->default('submitted')->change();
                    });
                } catch (Throwable $ex) {
                    // Ignore if already string
                }
            }

            // Normalisasi status lama ke 4 status utama baru:
            // submitted, verified, evaluating, evaluated
            try {
                DB::table('circles')
                    ->whereIn('status', ['draft', 'scheduled'])
                    ->whereNotIn('id', function ($query) {
                        $query->select('circle_id')->from('assessments');
                    })
                    ->update(['status' => 'submitted']);

                DB::table('circles')
                    ->where('status', 'under_assessment')
                    ->update(['status' => 'evaluating']);
            } catch (Throwable $e) {
                // Ignore if query fails
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
