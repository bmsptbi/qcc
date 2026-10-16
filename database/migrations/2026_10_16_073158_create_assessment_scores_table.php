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
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('assessment_criterion_id')->constrained('assessment_criteria')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->decimal('weighted_score', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['assessment_id', 'assessment_criterion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_scores');
    }
};
