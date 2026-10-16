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
        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->unsignedSmallInteger('year');
            $table->string('theme')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_deadline')->nullable();
            $table->enum('status', ['draft', 'active', 'assessment', 'completed', 'archived'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_periods');
    }
};
