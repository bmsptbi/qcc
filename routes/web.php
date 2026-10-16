<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\QccAssessmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QccAssessmentController::class, 'index'])->name('qcc.index');
Route::get('/circles/{circle}', [QccAssessmentController::class, 'show'])->name('qcc.circles.show');
Route::get('/circles/{circle}/assess', [QccAssessmentController::class, 'assess'])->name('qcc.circles.assess');
Route::post('/circles/{circle}/assess', [QccAssessmentController::class, 'storeAssessment'])->name('qcc.circles.assess.store');
Route::get('/leaderboard', [QccAssessmentController::class, 'leaderboard'])->name('qcc.leaderboard');
Route::post('/switch-judge', [QccAssessmentController::class, 'switchJudge'])->name('qcc.switch-judge');

// Admin Routes (Pendaftaran Circle, Manajemen Juri, & Kriteria Penilaian)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/circles/create', [AdminController::class, 'createCircle'])->name('circles.create');
    Route::post('/circles', [AdminController::class, 'storeCircle'])->name('circles.store');
    Route::get('/judges', [AdminController::class, 'judgesIndex'])->name('judges.index');
    Route::post('/judges', [AdminController::class, 'storeJudge'])->name('judges.store');
    Route::post('/judges/assign', [AdminController::class, 'assignJudge'])->name('judges.assign');
    Route::delete('/judges/assignment/{assignment}', [AdminController::class, 'destroyAssignment'])->name('judges.assignment.destroy');
    Route::get('/criteria', [AdminController::class, 'criteriaIndex'])->name('criteria.index');
    Route::post('/criteria/categories', [AdminController::class, 'storeCategory'])->name('criteria.categories.store');
    Route::post('/criteria', [AdminController::class, 'storeCriterion'])->name('criteria.store');
    Route::delete('/criteria/{criterion}', [AdminController::class, 'destroyCriterion'])->name('criteria.destroy');
});
