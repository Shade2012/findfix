<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingRoomController;
use App\Http\Controllers\PasswordResetController;
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
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/create-rooms', [BuildingRoomController::class, 'createRoom']);
        });
    });
});

// Notification Routes
Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
});
