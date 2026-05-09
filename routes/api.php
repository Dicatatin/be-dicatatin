<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

// Biarkan rute Auth diurus nanti atau buat dummy controller serupa.
// Pastikan semua route workspace dibungkus middleware auth:sanctum
    Route::get('/workspaces', [WorkspaceController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::get('/workspaces/{id}', [WorkspaceController::class, 'show']);
    Route::put('/workspaces/{id}', [WorkspaceController::class, 'update']);
    Route::post('/workspaces/{id}/transform', [WorkspaceController::class, 'transform']);
});
