<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingRoomController;
use App\Http\Controllers\FoundController;


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

Route::prefix('founds')->group(function(){
    Route::middleware('auth:sanctum')->group(function(){
        Route::get('/get-found-category',[FoundController::class,'getFoundCategory']);
        Route::get('/get-found-status',[FoundController::class,'getFoundStatus']);
        Route::get('/get-count-report',[FoundController::class,'getCountReport']);
        Route::get('/get-newest-report',[FoundController::class,'getNewestReport']);
        Route::get('/get-founds',[FoundController::class,'getFounds']);
        Route::post('/create-report',[FoundController::class,'store']);
        Route::delete('/delete-report/{id}',[FoundController::class,'deleteReport']);
        Route::post('/delete-images',[FoundController::class,'deleteImages']);
        Route::post('/update-found/{id}',[FoundController::class,'update']);
        // update
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
