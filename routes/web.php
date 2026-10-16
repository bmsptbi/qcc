<?php

use App\Http\Controllers\QccAssessmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QccAssessmentController::class, 'index'])->name('qcc.index');
Route::get('/circles/{circle}', [QccAssessmentController::class, 'show'])->name('qcc.circles.show');
Route::get('/circles/{circle}/assess', [QccAssessmentController::class, 'assess'])->name('qcc.circles.assess');
Route::post('/circles/{circle}/assess', [QccAssessmentController::class, 'storeAssessment'])->name('qcc.circles.assess.store');
Route::get('/leaderboard', [QccAssessmentController::class, 'leaderboard'])->name('qcc.leaderboard');
Route::post('/switch-judge', [QccAssessmentController::class, 'switchJudge'])->name('qcc.switch-judge');
