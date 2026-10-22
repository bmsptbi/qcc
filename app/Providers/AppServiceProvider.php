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

            $currentYear = (int) date('Y');
            $defaultYears = [$currentYear, $currentYear + 1, 2026];
            if (is_numeric($sessionYear)) {
                $defaultYears[] = (int) $sessionYear;
            }
            if (request()->filled('year') && is_numeric(request()->query('year'))) {
                $defaultYears[] = (int) request()->query('year');
            }

            $circleYears = [];
            $periodYears = [];
            $criteriaYears = [];

            try {
                if (Schema::hasTable('circles')) {
                    $circleYears = Circle::distinct()->pluck('year')->filter()->map(fn ($y) => (int) $y)->toArray();
                }
                if (Schema::hasTable('assessment_periods')) {
                    $periodYears = AssessmentPeriod::distinct()->pluck('year')->filter()->map(fn ($y) => (int) $y)->toArray();
                }
                if (Schema::hasTable('criteria_categories') && Schema::hasColumn('criteria_categories', 'year')) {
                    $criteriaYears = CriteriaCategory::distinct()->pluck('year')->filter()->map(fn ($y) => (int) $y)->toArray();
                }
            } catch (\Throwable $e) {
                // Ignore during bootstrapping or migration edge cases
            }

            $allYears = array_values(array_filter(array_unique(array_merge(
                $circleYears,
                $periodYears,
                $criteriaYears,
                $defaultYears
            ))));
            rsort($allYears);

            // Default tahun aktif jika belum ada di sesi: utamakan tahun yang ada datanya di DB (misal 2026)
            $fallbackDefaultYear = 2026;
            if (! empty($circleYears)) {
                $fallbackDefaultYear = $circleYears[0];
            } elseif (! empty($criteriaYears)) {
                $fallbackDefaultYear = $criteriaYears[0];
            }

            $activeYear = $sessionYear ?? $fallbackDefaultYear;

            // Pastikan jika activeYear numerik, dia ada di availableYears
            if (is_numeric($activeYear) && ! in_array((int) $activeYear, $allYears)) {
                $allYears[] = (int) $activeYear;
                rsort($allYears);
            }

            $view->with([
                'activeYear' => $activeYear,
                'availableYears' => $allYears,
            ]);
        });
    }
}
