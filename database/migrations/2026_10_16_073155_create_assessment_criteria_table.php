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
        Schema::create('assessment_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria_category_id')->constrained('criteria_categories')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('title', 255);
            $table->text('guidelines')->nullable();
            $table->decimal('min_score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_criteria');
    }
};
