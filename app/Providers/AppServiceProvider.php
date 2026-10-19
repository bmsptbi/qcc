<?php

namespace App\Providers;

use App\Models\AssessmentPeriod;
use App\Models\Circle;
use App\Models\User;
use Database\Seeders\BekaertUsersSeeder;
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
        } catch (\Throwable $e) {
            // Abaikan jika database belum siap / koneksi offline
        }

        View::composer('*', function ($view) {
            $sessionYear = session('active_year');
            if (request()->filled('year')) {
                $sessionYear = request()->query('year') === 'all' ? 'all' : (int) request()->query('year');
                session(['active_year' => $sessionYear]);
            }

            $allYears = [2027, 2026, 2025, 2024];
            try {
                if (Schema::hasTable('circles') && Schema::hasTable('assessment_periods')) {
                    $circleYears = Circle::distinct()->pluck('year')->filter()->map(fn ($y) => (int) $y)->toArray();
                    $periodYears = AssessmentPeriod::distinct()->pluck('year')->filter()->map(fn ($y) => (int) $y)->toArray();
                    $allYears = array_values(array_unique(array_merge($circleYears, $periodYears, [(int) date('Y'), 2026, 2025, 2024])));
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
