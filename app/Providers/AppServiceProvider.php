<?php

namespace App\Providers;

use App\Models\AssessmentPeriod;
use App\Models\Circle;
use App\Models\CriteriaCategory;
use App\Models\User;
use Database\Seeders\BekaertUsersSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-seed Bekaert Users jika belum ada di database
        try {
            if (Schema::hasTable('users') && ! User::where('nik', '971205')->exists()) {
                (new BekaertUsersSeeder)->run();
            }

            // Pastikan kolom status di circles bertipe varchar agar mendukung: submitted, verified, evaluating, evaluated
            if (Schema::hasTable('circles')) {
                DB::statement("ALTER TABLE circles MODIFY status VARCHAR(50) NOT NULL DEFAULT 'submitted'");
                DB::table('circles')
                    ->whereIn('status', ['draft', 'scheduled'])
                    ->whereNotIn('id', function ($query) {
                        $query->select('circle_id')->from('assessments');
                    })
                    ->update(['status' => 'submitted']);
                DB::table('circles')
                    ->where('status', 'under_assessment')
                    ->update(['status' => 'evaluating']);
            }
        } catch (\Throwable $e) {
            // Abaikan jika database belum siap / koneksi offline
        }

        View::composer('*', function ($view) {
            $sessionYear = session('active_year');
            if (request()->filled('year')) {
                $sessionYear = request()->query('year') === 'all' ? 'all' : (int) request()->query('year');
                session(['active_year' => $sessionYear]);
            }

            $allYears = [(int) date('Y'), 2026];
            try {
                if (Schema::hasTable('circles') && Schema::hasTable('assessment_periods')) {
                    $circleYears = Circle::distinct()->pluck('year')->filter()->map(fn ($y) => (int) $y)->toArray();
                    $periodYears = AssessmentPeriod::distinct()->pluck('year')->filter()->map(fn ($y) => (int) $y)->toArray();
                    $criteriaYears = (Schema::hasTable('criteria_categories') && Schema::hasColumn('criteria_categories', 'year'))
                        ? CriteriaCategory::distinct()->pluck('year')->filter()->map(fn ($y) => (int) $y)->toArray()
                        : [];
                    $allYears = array_values(array_filter(array_unique(array_merge($circleYears, $periodYears, $criteriaYears, [(int) date('Y'), 2026]))));
                    rsort($allYears);
                }
            } catch (\Throwable $e) {
                // Ignore during bootstrapping or migration edge cases
            }

            $activeYear = $sessionYear ?? ($allYears[0] ?? (int) date('Y'));

            $view->with([
                'activeYear' => $activeYear,
                'availableYears' => $allYears,
            ]);
        });
    }
}
