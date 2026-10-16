<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QccAssessmentController;
use Illuminate\Support\Facades\Route;

// Public Judging & Convention Routes
Route::get('/', [QccAssessmentController::class, 'index'])->name('qcc.index');
Route::get('/circles/{circle}', [QccAssessmentController::class, 'show'])->name('qcc.circles.show');
Route::get('/circles/{circle}/assess', [QccAssessmentController::class, 'assess'])->name('qcc.circles.assess');
Route::post('/circles/{circle}/assess', [QccAssessmentController::class, 'storeAssessment'])->name('qcc.circles.assess.store');
Route::get('/leaderboard', [QccAssessmentController::class, 'leaderboard'])->name('qcc.leaderboard');
Route::post('/switch-judge', [QccAssessmentController::class, 'switchJudge'])->name('qcc.switch-judge');

// Authentication Routes (Admin Sign In / Out)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Routes (Hanya dapat diakses setelah Sign In dengan role Admin)
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/circles/create', [AdminController::class, 'createCircle'])->name('circles.create');
    Route::post('/circles', [AdminController::class, 'storeCircle'])->name('circles.store');
    Route::get('/judges', [AdminController::class, 'judgesIndex'])->name('judges.index');
    Route::post('/judges', [AdminController::class, 'storeJudge'])->name('judges.store');
    Route::post('/judges/assign', [AdminController::class, 'assignJudge'])->name('judges.assign');
    Route::delete('/judges/assignment/{assignment}', [AdminController::class, 'destroyAssignment'])->name('judges.assignment.destroy');
    Route::get('/criteria', [AdminController::class, 'criteriaIndex'])->name('criteria.index');
    Route::post('/criteria/categories', [AdminController::class, 'storeCategory'])->name('criteria.categories.store');
    Route::put('/criteria/categories/{category}', [AdminController::class, 'updateCategory'])->name('criteria.categories.update');
    Route::delete('/criteria/categories/{category}', [AdminController::class, 'destroyCategory'])->name('criteria.categories.destroy');
    Route::post('/criteria', [AdminController::class, 'storeCriterion'])->name('criteria.store');
    Route::put('/criteria/{criterion}', [AdminController::class, 'updateCriterion'])->name('criteria.update');
    Route::delete('/criteria/{criterion}', [AdminController::class, 'destroyCriterion'])->name('criteria.destroy');
});
