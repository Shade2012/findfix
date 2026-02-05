<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingRoomController;
use App\Http\Controllers\ItemController;


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/get', [AuthController::class, 'index']);

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AuthController::class, 'testAdmin']);
    });
});
});

Route::prefix('buildings')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/{building_id}/rooms', [BuildingRoomController::class, 'getRoomByBuildingId']);
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/create-rooms', [BuildingRoomController::class, 'createRoom']);
        });
    });
 
});

// Lost & Found Items Routes
Route::prefix('items')->group(function () {
    // Public routes
    Route::get('/', [ItemController::class, 'index']);
    Route::get('/{id}', [ItemController::class, 'show']);
    
    // Protected routes (requires authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ItemController::class, 'store']);
        Route::put('/{id}', [ItemController::class, 'update']);
        Route::delete('/{id}', [ItemController::class, 'destroy']);
        Route::get('/my/reports', [ItemController::class, 'myReports']);
    });
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// // routes/api.php
// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
//     Route::get('/admin', function () {
//         return response()->json(['message' => 'Admin access']);
//     });
// });
