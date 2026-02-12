<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingRoomController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\FoundController;
use App\Http\Controllers\NotificationController;


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Password Reset Routes
    Route::post('/forgot-password', [PasswordResetController::class, 'requestOTP']);
    Route::post('/verify-otp', [PasswordResetController::class, 'verifyOTP']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/get', [AuthController::class, 'index']);
    });

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/admin', [AuthController::class, 'testAdmin']);
    });
});

Route::prefix('buildings')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/{building_id}/rooms', [BuildingRoomController::class, 'getRoomByBuildingId']);
        Route::get('/rooms', [BuildingRoomController::class, 'getRoomAndBuilding']);
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
        Route::get('/get-founds-statistic',[FoundController::class,'getFoundCountByStatusId']);
        Route::post('/create-report',[FoundController::class,'store']);
        Route::delete('/delete-report/{id}',[FoundController::class,'deleteReport']);
        Route::post('/delete-images',[FoundController::class,'deleteImages']);
        Route::post('/update-found/{id}',[FoundController::class,'update']);
        // update
    });
});

// Notification Routes
Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
});
