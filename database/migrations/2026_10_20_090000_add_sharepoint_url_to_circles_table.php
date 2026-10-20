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
        if (Schema::hasTable('circles') && ! Schema::hasColumn('circles', 'sharepoint_url')) {
            Schema::table('circles', function (Blueprint $table) {
                $table->text('sharepoint_url')->nullable()->after('theme_title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('circles') && Schema::hasColumn('circles', 'sharepoint_url')) {
            Schema::table('circles', function (Blueprint $table) {
                $table->dropColumn('sharepoint_url');
            });
        }
    }
};
