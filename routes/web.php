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
Route::get('/circles/{circle}/export-assessment', [QccAssessmentController::class, 'exportAssessmentCsv'])->name('qcc.circles.export');
Route::get('/leaderboard', [QccAssessmentController::class, 'leaderboard'])->name('qcc.leaderboard');
Route::post('/switch-judge', [QccAssessmentController::class, 'switchJudge'])->name('qcc.switch-judge');
Route::post('/switch-year', [QccAssessmentController::class, 'switchYear'])->name('qcc.switch-year');
Route::post('/verify-admin-password', [QccAssessmentController::class, 'verifyAdminPassword'])->name('qcc.circles.verify-admin-password');
Route::post('/hide-judges', [QccAssessmentController::class, 'hideJudgeNames'])->name('qcc.circles.hide-judges');

// Authentication Routes (Admin Sign In / Out)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Routes (Hanya dapat diakses setelah Sign In dengan role Admin)
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    // User Management Routes
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Circle Management Routes
    Route::get('/circles', [AdminController::class, 'circlesIndex'])->name('circles.index');
    Route::get('/circles/export', [AdminController::class, 'exportCircles'])->name('circles.export');
    Route::get('/circles/template', [AdminController::class, 'downloadCircleTemplate'])->name('circles.template');
    Route::post('/circles/import', [AdminController::class, 'importCircles'])->name('circles.import');
    Route::get('/circles/create', [AdminController::class, 'createCircle'])->name('circles.create');
    Route::post('/circles', [AdminController::class, 'storeCircle'])->name('circles.store');
    Route::get('/circles/{circle}/edit', [AdminController::class, 'editCircle'])->name('circles.edit');
    Route::put('/circles/{circle}', [AdminController::class, 'updateCircle'])->name('circles.update');
    Route::delete('/circles/{circle}', [AdminController::class, 'destroyCircle'])->name('circles.destroy');

    // Judge Management Routes
    Route::get('/judges', [AdminController::class, 'judgesIndex'])->name('judges.index');
    Route::post('/judges', [AdminController::class, 'storeJudge'])->name('judges.store');
    Route::put('/judges/{user}', [AdminController::class, 'updateJudge'])->name('judges.update');
    Route::delete('/judges/{user}', [AdminController::class, 'destroyJudge'])->name('judges.destroy');
    Route::post('/judges/assign', [AdminController::class, 'assignJudge'])->name('judges.assign');
    Route::delete('/judges/assignment/{assignment}', [AdminController::class, 'destroyAssignment'])->name('judges.assignment.destroy');
    Route::get('/criteria', [AdminController::class, 'criteriaIndex'])->name('criteria.index');
    Route::post('/criteria/categories', [AdminController::class, 'storeCategory'])->name('criteria.categories.store');
    Route::put('/criteria/categories/{category}', [AdminController::class, 'updateCategory'])->name('criteria.categories.update');
    Route::delete('/criteria/categories/{category}', [AdminController::class, 'destroyCategory'])->name('criteria.categories.destroy');
    Route::post('/criteria', [AdminController::class, 'storeCriterion'])->name('criteria.store');
    Route::put('/criteria/{criterion}', [AdminController::class, 'updateCriterion'])->name('criteria.update');
    Route::delete('/criteria/{criterion}', [AdminController::class, 'destroyCriterion'])->name('criteria.destroy');

    // System Settings Routes (Admin only)
    Route::get('/settings', [AdminController::class, 'settingsIndex'])->name('settings.index');
    Route::post('/settings', [AdminController::class, 'settingsUpdate'])->name('settings.update');
});
