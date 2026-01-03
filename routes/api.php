<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\Admincontroller;
use  App\Http\Controllers\Auth\UserAuthController;
use  App\Http\Controllers\File\FileController;
use App\Http\Controllers\user\UserController;
use App\Http\Middleware\OtpSessionMiddleware;

// /////////////////////////////Admin Routes/////////////////////////////////////////////////////

Route::prefix('admin')->group(function () {

    // -------------------
    // GUEST ROUTES (Not authenticated)
    // -------------------
    Route::middleware('guest:admin_api')->group(function () {
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::post('send-otp', [AdminAuthController::class, 'sendOtp']);
        Route::post('verify-otp', [AdminAuthController::class, 'verifyOtp']);
        Route::post('reset-password', [AdminAuthController::class, 'resetPasswordWithOtp']);
    });

    // -------------------
    // AUTHENTICATED ROUTES (admin_api)
    // -------------------
    Route::middleware('auth:admin_api')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::get('refresh', [AdminAuthController::class, 'refresh']);
        Route::get('profile', [AdminAuthController::class, 'profile']);
        Route::post('update-profile', [AdminAuthController::class, 'updateProfile']);
        Route::post('/users/approve', [Admincontroller::class, 'approve']);
        Route::post('/users/reject', [Admincontroller::class, 'reject']);
        Route::get('/users', [AdminController::class, 'dashbourd']);
        Route::get('/users/{user}', [AdminController::class, 'show']);
        Route::post('/owner-upgrade/approve', [Admincontroller::class, 'approveUpgrade']);
        Route::post('/owner-upgrade/reject', [Admincontroller::class, 'rejectUpgrade']);
        Route::delete('/users/delete', [Admincontroller::class, 'deleteUser']);
         Route::get('files/{type}/{admin}', [FileController::class, 'show'])
        ->where('type', 'personal|id');

    });
});

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('send-otp', [AdminAuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AdminAuthController::class, 'verifyOtp']);
});

// /////////////////////////////User Routes///////////////////////////////////////////////////////

Route::prefix('user')->group(function () {

    // -------------------
    // GUEST ROUTES (Not authenticated)
    // -------------------
    Route::middleware('guest:user_api')->group(function () {
        Route::post('send-phone-otp', [UserAuthController::class, 'sendPhoneOtp']);
        Route::post('resend-phone-otp', [UserAuthController::class, 'resendPhoneOtp']);
        Route::post('verify-phone-otp', [UserAuthController::class, 'verifyPhoneOtp']);
        Route::post('submit-profile', [UserAuthController::class, 'submitProfile'])->middleware('otp.session');
        Route::get('admin-approval-status', [UserAuthController::class, 'CheakStatus']);
    });

    // -------------------
    // AUTHENTICATED ROUTES (user_api)
    // -------------------
    Route::middleware('auth:user_api')->group(function () {
        Route::post('update-profile', [UserAuthController::class, 'updateProfile']);
        Route::get('show-profile', [UserAuthController::class, 'profile']);
        Route::get('refresh-token', [UserAuthController::class, 'refresh']); // aisha
        Route::post('upgrade', [UserController::class, 'upgrade']);
        Route::post('logout', [UserAuthController::class, 'logout']);
        Route::post('start-chat', [UserController::class, 'startChat']);
        Route::get('user-chats', [UserController::class, 'inbox']);
        Route::get('check-upgrade-status', [UserController::class, 'checkUpgradeStatus']);
        Route::get('/files/{type}/{user}', [FileController::class, 'show'])
        ->where('type', 'personal|id');

    });
});

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('send-otp', [UserController::class, 'sendPhoneOtp']);
    Route::post('verify-otp', [UserController::class, 'verifyPhoneOtp']);
});

// //////////////////////////////////Shared////////////////////////////////////////////////////////////

