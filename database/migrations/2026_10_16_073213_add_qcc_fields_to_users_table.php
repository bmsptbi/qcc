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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'judge', 'facilitator', 'circle_leader', 'member'])->default('member')->after('email');
            $table->string('nik', 50)->nullable()->after('role');
            $table->foreignId('department_id')->nullable()->after('nik')->constrained('departments')->nullOnDelete();
            $table->string('phone', 50)->nullable()->after('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['role', 'nik', 'phone']);
        });
    }
};
