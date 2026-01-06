<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\Admincontroller;
use  App\Http\Controllers\Auth\UserAuthController;
use  App\Http\Controllers\File\FileController;
use App\Http\Controllers\user\UserController;
use App\Http\Middleware\OtpSessionMiddleware;
use App\Http\Middleware\EnsureUser;
use App\Http\Controllers\Apartment\ApartmentController;
use App\Http\Controllers\Favorite\FavoriteController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\BookingUpdateRequestController;
use App\Http\Controllers\Review\ReviewController;

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
        Route::post('refresh-token', [UserAuthController::class, 'refresh']); // aisha
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
   Route::prefix('apartment')-> middleware(['auth:user_api'])->group(function () {
    
        Route::get('showApartments',[ApartmentController::class,'show']);
        Route::post('apartmentFiltering',[ApartmentController::class,'filter']);
        Route::get('favoriteUserApartments',[FavoriteController::class,'favoriteList']);
        Route::get('showLatestApartments',[ApartmentController::class,'showLatest']);

        Route::get('showApartmentOwner/{apartment}',[ApartmentController::class,'apartmentOwner']);
        Route::get('homePage',[ApartmentController::class,'home']);
        Route::get('showApartmentImages/{apartment}',[ApartmentController::class,'showApartmentImages']);
        Route::get('showAnApartment/{apartment}',[ApartmentController::class,'showAnApartment']);
        Route::get('addToFavorite/{apartment}',[FavoriteController::class,'toggel']);

   });    
Route::prefix('apartment')->
   middleware(['auth:user_api',EnsureUser::class])->group(function () {
    
        Route::post('/updateApartment/{apartment}',[ApartmentController::class,'update']);
        Route::delete('deleteApartment/{apartment}',[ApartmentController::class,'delete']);
        Route::post('/insertApartment',[ApartmentController::class,'store']);



        
   });


   Route::prefix('booking')->middleware(['auth:user_api'])->group(function () {

          Route::post('bookAnApartment/{apartment}',[BookingController::class,'store']);
          Route::get('cancelAbook/{booking}',[BookingController::class,'cancel']);
          Route::get('showUserBookings',[BookingController::class,'showUserBookings']);
          Route::get('showAbook/{booking}',[BookingController::class,'showABook']);


          Route::post('updateBooking/{booking}',[BookingUpdateRequestController::class,'store']);
          Route::get('showUserBookingUpdateRequests',[BookingUpdateRequestController::class,'showUserBookingUpdateRequests']);
          Route::get('showBookingUpdateRequest/{booking_update_request}',[BookingUpdateRequestController::class,'show']);
          Route::get('cancelBookingUpdateRequest/{booking_update_request}',[BookingUpdateRequestController::class,'cancel']);

});
    Route::prefix('booking')->middleware(['auth:user_api',EnsureUser::class])->group(function () {
             Route::get('rejectAbook/{booking}',[BookingController::class,'reject']);
             Route::get('approveAbooke/{booking_id}',[BookingController::class,'approve']);
             Route::get('ownerBookingRequests',[BookingController::class,'ownerBookingRequests']);

             Route::get('OwnerBookingUpdateRequests',[BookingUpdateRequestController::class,'showOwnerBookingUpdateRequests']);
             Route::get('approveBookingUpdateRequest/{booking_update_request}',[BookingUpdateRequestController::class,'approve']);
             Route::get('rejectBookingUpdateRequest/{booking_update_request}',[BookingUpdateRequestController::class,'reject']);
         
    });
   Route::prefix('review')->middleware('auth:user_api')->group(function () {

          Route::post('createReview/{bookingId}',[ReviewController::class,'store']);
          Route::get('apartmentAverageRating/{apartment_id}',[ReviewController::class,'getApartmentReview']);
   
   });

// //////////////////////////////////Shared////////////////////////////////////////////////////////////

