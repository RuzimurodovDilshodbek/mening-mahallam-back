<?php

use App\Http\Controllers\Api\Admin\NeighborhoodController as AdminNeighborhoodController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CameraController;
use App\Http\Controllers\Api\NeighborhoodController;
use App\Http\Controllers\Api\PoiController;
use App\Http\Controllers\Api\PoiTypeController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/neighborhoods', [NeighborhoodController::class, 'index']);
    Route::post('/neighborhoods', [NeighborhoodController::class, 'store']);
    Route::get('/neighborhoods/{neighborhood}', [NeighborhoodController::class, 'show']);
    Route::put('/neighborhoods/{neighborhood}', [NeighborhoodController::class, 'update']);
    Route::delete('/neighborhoods/{neighborhood}', [NeighborhoodController::class, 'destroy']);
    Route::post('/neighborhoods/{neighborhood}/publish', [NeighborhoodController::class, 'publish']);
    Route::get('/neighborhoods/{neighborhood}/geojson', [NeighborhoodController::class, 'geojson']);

    // POI Types
    Route::get('/poi-types', [PoiTypeController::class, 'index']);
    Route::post('/poi-types', [PoiTypeController::class, 'store']);

    // POIs
    Route::get('/neighborhoods/{neighborhood}/pois', [PoiController::class, 'index']);
    Route::post('/neighborhoods/{neighborhood}/pois', [PoiController::class, 'store']);
    Route::put('/neighborhoods/{neighborhood}/pois/{poi}', [PoiController::class, 'update']);
    Route::delete('/neighborhoods/{neighborhood}/pois/{poi}', [PoiController::class, 'destroy']);
    Route::post('/neighborhoods/{neighborhood}/pois/{poi}/images', [PoiController::class, 'uploadImage']);
    Route::delete('/neighborhoods/{neighborhood}/pois/{poi}/images/{image}', [PoiController::class, 'deleteImage']);
    Route::put('/neighborhoods/{neighborhood}/pois/{poi}/images/{image}/primary', [PoiController::class, 'setPrimaryImage']);

    // Cameras
    Route::get('/neighborhoods/{neighborhood}/cameras', [CameraController::class, 'index']);
    Route::post('/neighborhoods/{neighborhood}/cameras', [CameraController::class, 'store']);
    Route::put('/neighborhoods/{neighborhood}/cameras/{camera}', [CameraController::class, 'update']);
    Route::delete('/neighborhoods/{neighborhood}/cameras/{camera}', [CameraController::class, 'destroy']);

    Route::prefix('admin')->group(function (): void {
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::put('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
        Route::get('/neighborhoods', [AdminNeighborhoodController::class, 'index']);
        Route::post('/neighborhoods/{neighborhood}/verify', [AdminNeighborhoodController::class, 'verify']);
    });
});
