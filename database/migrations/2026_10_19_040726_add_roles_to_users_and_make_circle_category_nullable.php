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
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'roles')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('roles')->nullable()->after('role');
            });

            // Backfill roles dari role yang sudah ada
            $users = DB::table('users')->get();
            foreach ($users as $user) {
                $initialRoles = ! empty($user->role) ? [$user->role] : ['member'];
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['roles' => json_encode($initialRoles)]);
            }
        }

        if (Schema::hasTable('circles') && Schema::hasColumn('circles', 'circle_category_id')) {
            Schema::table('circles', function (Blueprint $table) {
                $table->unsignedBigInteger('circle_category_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'roles')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('roles');
            });
        }
    }
};
