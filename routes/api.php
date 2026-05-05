<?php

use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JudgeController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ScoreController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\UploadFileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Award Management System – API Routes
|--------------------------------------------------------------------------
| Auth   : POST /api/auth/login  →  returns bearer token
| All other routes require: Authorization: Bearer {token}
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('auth/me',     [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    /* ── Users ──────────────────────────────────────────────────── */
    Route::get('users',                        [UserController::class, 'index']);
    Route::patch('users/{user}',               [UserController::class, 'update']);
    Route::post('users/{user}/password',       [UserController::class, 'changePassword']);

    /* ── Judges (global list) ───────────────────────────────────── */
    Route::get('judges', [JudgeController::class, 'index']);
    Route::patch('judges/{judge}', [JudgeController::class, 'update']);

    /* ── Programs ───────────────────────────────────────────────── */
    Route::get('programs',          [ProgramController::class, 'index']);
    Route::post('programs',         [ProgramController::class, 'store']);
    Route::get('programs/{program}', [ProgramController::class, 'show']);
    Route::patch('programs/{program}', [ProgramController::class, 'update']);

    /* ── Per-program judges ─────────────────────────────────────── */
    Route::get('programs/{program}/judges',              [JudgeController::class, 'programJudges']);
    Route::post('programs/{program}/judges',             [JudgeController::class, 'addToProgram']);
    Route::delete('programs/{program}/judges/{judge}',   [JudgeController::class, 'removeFromProgram']);

    /* ── Submissions ────────────────────────────────────────────── */
    Route::get('programs/{program}/submissions',                        [SubmissionController::class, 'index']);
    Route::post('programs/{program}/submissions',                       [SubmissionController::class, 'store']);
    Route::patch('programs/{program}/submissions/{submission}',          [SubmissionController::class, 'update']);
    Route::post('programs/{program}/submissions/{submission}/recall',   [SubmissionController::class, 'recall']);
    Route::post('programs/{program}/submissions/{submission}/review',   [SubmissionController::class, 'review']);

    /* ── Judge assignments ──────────────────────────────────────── */
    Route::post('programs/{program}/submissions/bulk-assign',                  [AssignmentController::class, 'bulkAssign']);
    Route::post('programs/{program}/submissions/{submission}/assign/{judge}',   [AssignmentController::class, 'assign']);
    Route::delete('programs/{program}/submissions/{submission}/assign/{judge}', [AssignmentController::class, 'unassign']);

    /* ── Scores ─────────────────────────────────────────────────── */
    Route::get('programs/{program}/submissions/{submission}/scores', [ScoreController::class, 'index']);
    Route::put('programs/{program}/submissions/{submission}/scores', [ScoreController::class, 'upsert']);

    /* ── Dashboard stats ────────────────────────────────────────── */
    Route::get('programs/{program}/dashboard', [DashboardController::class, 'show']);

    Route::post('upload', [UploadFileController::class, 'store']);
});
