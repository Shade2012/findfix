<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingRoomController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\FoundController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\BadgeController;


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Password Reset Routes
    Route::post('/forgot-password', [PasswordResetController::class, 'requestOTP']);
    Route::post('/verify-otp', [PasswordResetController::class, 'verifyOTP']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
    Route::get('/get', [AuthController::class, 'index'])->middleware('auth:sanctum');
});

Route::prefix('users')->group(function () {
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/get', [AuthController::class, 'getUsers']);
        Route::get('/roles', [AuthController::class, 'getUserRole']);
        Route::post('/add', [AuthController::class, 'addUser']);
        Route::post('/update/{id}', [AuthController::class, 'updateUser']);
        Route::delete('/{id}', [AuthController::class, 'deleteUser']);
        Route::get('/admin', [AuthController::class, 'testAdmin']);
    });
});

Route::prefix('buildings')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/{building_id}/rooms', [BuildingRoomController::class, 'getRoomByBuildingId']);
        Route::get('/rooms', [BuildingRoomController::class, 'getRoomAndBuilding']);
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/create-room', [BuildingRoomController::class, 'createRoom']);
            Route::post('/create-building', [BuildingRoomController::class, 'createBuilding']);
            Route::post('/update-room/{id}', [BuildingRoomController::class, 'updateRoom']);
            Route::post('/update-building/{id}', [BuildingRoomController::class, 'updateBuilding']);
            Route::delete('/delete-room/{id}', [BuildingRoomController::class, 'deleteRoom']);
            Route::delete('/delete-building/{id}', [BuildingRoomController::class, 'deleteBuilding']);
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
        Route::get('/get-found/{id}',[FoundController::class,'getFound']);
        Route::get('/get-founds-statistic',[FoundController::class,'getFoundCountByStatusId']);
        Route::post('/create-report',[FoundController::class,'store']);
        Route::delete('/delete-report/{id}',[FoundController::class,'deleteReport']);
        Route::post('/delete-images',[FoundController::class,'deleteImages']);
        Route::post('/update-found/{id}',[FoundController::class,'update']);
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/confirm-found',[FoundController::class,'confirmStatusFound']);
        });
        // update
    });
});

Route::prefix('hubs')->group(function(){
    Route::middleware('auth:sanctum')->group(function(){
        Route::get('get-hubs',[HubController::class,'getAllHub']);
        Route::get('get-hubs/{id}',[HubController::class,'getHub']);
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/create-hub',[HubController::class,'create']);
            Route::post('/update-hub/{id}',[HubController::class,'update']);
            Route::delete('/delete-hub/{id}',[HubController::class,'delete']);
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

// Badge Routes
Route::prefix('badges')->group(function(){
    Route::middleware('auth:sanctum')->group(function(){
        Route::get('get-badges',[BadgeController::class,'getAllBadges']);
        Route::get('get-badges/{id}',[BadgeController::class,'getBadge']);
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/create-badge',[BadgeController::class,'create']);
            Route::post('/update-badge/{id}',[BadgeController::class,'update']);
            Route::delete('/delete-badge/{id}',[BadgeController::class,'delete']);
        });
    });
});
