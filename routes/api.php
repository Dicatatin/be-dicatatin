<?php

use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\HeroSectionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;




Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
//logout route
    Route::post('/auth/logout', [AuthController::class, 'logout']);

// Workspace routes
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::get('/workspaces/{id}', [WorkspaceController::class, 'show']);
    Route::put('/workspaces/{id}', [WorkspaceController::class, 'update']);
    Route::post('/workspaces/{id}/transform', [WorkspaceController::class, 'transform']);
    Route::delete('/workspaces/{id}', [WorkspaceController::class, 'destroy']);
    Route::post('/workspaces/{id}/flashcards', [WorkspaceController::class, 'regenerateFlashcards']);
});

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'service' => 'be-dicatatin',
        'status'  => 'ok',
    ]);
});

// ─── Public: Landing Page ────────────────────────────────────────────────────
Route::get('/landing/hero', [HeroSectionController::class, 'show']);

// ─── Admin: CMS Hero Section (auth:sanctum + admin role) ─────────────────────
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::put('/landing/hero', [HeroSectionController::class, 'update']);
    Route::post('/landing/hero/reset', [HeroSectionController::class, 'reset']);
});

