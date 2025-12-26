<?php

use App\Http\Middleware\EnsureUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\Admincontroller;
use  App\Http\Controllers\Auth\UserAuthController;
use  App\Http\Controllers\File\FileController;
use App\Http\Controllers\user\UserController;
use App\Http\Controllers\Apartment\ApartmentController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\BookingController as BookingBookingController;
use App\Http\Controllers\Booking\BookingController as ControllersBookingBookingController;

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
        Route::post('refresh', [AdminAuthController::class, 'refresh']);
        Route::get('profile', [AdminAuthController::class, 'profile']);
        Route::post('update-profile', [AdminAuthController::class, 'updateProfile']);
        Route::post('/users/approve', [Admincontroller::class, 'approve']);
        Route::post('/users/reject', [Admincontroller::class, 'reject']);
        Route::get('/users', [AdminController::class, 'dashbourd']);
        Route::get('/users/{user}', [AdminController::class, 'show']);
        Route::post('/owner-upgrade/approve',[Admincontroller::class,'approveUpgrade']);
        Route::post('/owner-upgrade/reject',[Admincontroller::class,'rejectUpgrade']);

    });
});

// /////////////////////////////User Routes///////////////////////////////////////////////////////

Route::prefix('user')->group(function () {

    // -------------------
    // GUEST ROUTES (Not authenticated)
    // -------------------
    Route::middleware('guest:user_api')->group(function () {
        Route::post('login', [UserAuthController::class, 'login']);
        Route::post('send-phone-otp', [UserAuthController::class, 'sendPhoneOtp']);
        Route::post('resend-phone-otp', [UserAuthController::class, 'resendPhoneOtp']);
        Route::post('verify-phone-otp', [UserAuthController::class, 'verifyPhoneOtp']);
        Route::post('submit-profile', [UserAuthController::class, 'submitProfile']);
    });
    // -------------------
    // AUTHENTICATED ROUTES (user_api)
    // -------------------
    Route::middleware('auth:user_api')->group(function () {
        Route::post('update-profile', [UserAuthController::class, 'updateProfile']);
        Route::get('show-profile', [UserAuthController::class, 'profile']);
        Route::post('refresh', [UserAuthController::class, 'refresh']);
        Route::post('upgrade',[UserController::class,'upgrade']);
        Route::post('logout', [UserAuthController::class, 'logout']);
    });
});

// //////////////////////////////////////////////////////////////////////////////////////////////




Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('send-otp', [AdminAuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AdminAuthController::class, 'verifyOtp']);
});

Route::get('/files/{type}/{user}', [FileController::class, 'show'])
    ->where('type', 'personal|id');
    
Route::prefix('apartment')->
   middleware(['auth:user_api',EnsureUser::class])->group(function () {

        Route::get('/showApartments',[ApartmentController::class,'show']);
        Route::post('/insertApartment',[ApartmentController::class,'store']);
        Route::delete('/deleteApartment/{apartment}',[ApartmentController::class,'delete']);
        Route::post('/updateApartment/{apartment}',[ApartmentController::class,'update']);
        Route::post('/apartmentFiltering',[ApartmentController::class,'filter']);
        Route::get('/showApartmentOwner/{apartment}',[ApartmentController::class,'apartmentOwner']);
        Route::get('/showLatestApartments',[ApartmentController::class,'showLatest']);

   });
   Route::prefix('booking')->middleware(['auth:user_api'])->group(function () {
          Route::post('/bookAnApartment/{apartment}',[BookingController::class,'store']);
          Route::get('/rejectAbook/{booking}',[BookingController::class,'reject']);
          Route::get('/cancelAbook/{booking}',[BookingController::class,'cancel']);
          Route::get('/showUserBookings',[BookingController::class,'showUserBookings']);


});
   
