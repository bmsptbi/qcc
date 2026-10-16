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
        Schema::create('circles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_period_id')->constrained('assessment_periods')->cascadeOnDelete();
            $table->foreignId('circle_category_id')->constrained('circle_categories')->restrictOnDelete();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->foreignId('facilitator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('leader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('circle_code', 50)->unique();
            $table->string('circle_name', 150);
            $table->string('theme_title', 255);
            $table->text('problem_statement')->nullable();
            $table->text('target_statement')->nullable();
            $table->string('paper_file_path')->nullable();
            $table->string('presentation_file_path')->nullable();
            $table->dateTime('presentation_schedule')->nullable();
            $table->string('presentation_room')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified', 'scheduled', 'evaluated', 'awarded'])->default('draft');
            $table->decimal('final_score', 5, 2)->nullable();
            $table->enum('award_level', ['Diamond', 'Platinum', 'Gold', 'Silver', 'Bronze', 'Participant'])->nullable();
            $table->unsignedSmallInteger('ranking')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circles');
    }
};
