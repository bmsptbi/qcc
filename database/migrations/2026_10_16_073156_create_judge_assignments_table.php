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
        Schema::create('judge_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_period_id')->constrained('assessment_periods')->cascadeOnDelete();
            $table->foreignId('judge_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('circle_category_id')->nullable()->constrained('circle_categories')->nullOnDelete();
            $table->foreignId('circle_id')->nullable()->constrained('circles')->cascadeOnDelete();
            $table->boolean('is_lead_judge')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judge_assignments');
    }
};
