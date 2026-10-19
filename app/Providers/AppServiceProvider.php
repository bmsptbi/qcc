<?php

namespace App\Providers;

use App\Models\AssessmentPeriod;
use App\Models\Circle;
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
