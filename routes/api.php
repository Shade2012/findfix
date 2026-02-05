<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;


Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Password Reset Routes
    Route::post('/forgot-password', [PasswordResetController::class, 'requestOTP']);
    Route::post('/verify-otp', [PasswordResetController::class, 'verifyOTP']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/get', [AuthController::class, 'index']);
    });
    Route::middleware('auth:sanctum','role:admin')->group(function () {
        Route::get('/admin', [AuthController::class, 'testAdmin']);
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
