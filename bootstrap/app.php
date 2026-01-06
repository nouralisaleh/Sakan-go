<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetApplang;
use App\Http\Middleware\OtpSessionMiddleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(SetApplang::class);
         $middleware->alias([
            'otp.session' => OtpSessionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
        \Illuminate\Auth\AuthenticationException $e,
        $request
    ) {
        return response()->json([
            'message' => 'Unauthenticated.',
            'code' => 401
        ]);
    });
      // 422 - Validation Error
    $exceptions->render(function (ValidationException $e, $request) {
        return response()->json([
            'message' => $e->getMessage(),
            'errors'  => $e->errors(),
            'code'    => 422
        ], 422);
    });
                 $exceptions->render(function (NotFoundHttpException $e) {

                return response()->json([
                     'status' => false,
                     'message' => match ($e->getMessage()) {
                        'APARTMENT_NOT_FOUND' => __('apartments.not_found'),
                        'BOOKING_NOT_FOUND'   => __('booking.not_found'),
                        //'Booking'=>__('booking.not_found'),
                        'BOOKING_UPDATE_REQUEST_NOT_FOUND' => __('booking.update_request_not_found'),
                        'APARTMENT_HAS_ACTIVE_BOOKINGS'=>__('apartments.booked'),
                         default     => __('errortts.not_found'),
                    },
                    'code' => 404,
                ], 404);
            });


            $exceptions->render(function (ModelNotFoundException $e) {

                return response()->json([
                     'status' => false,
                     'message' => match ($e->getMessage()) {
                        'APARTMENT_NOT_FOUND' => __('apartments.not_found'),
                        'BOOKING_NOT_FOUND'   => __('booking.not_found'),
                        'BOOKING_UPDATE_REQUEST_NOT_FOUND' => __('booking.update_request_not_found'),
                        'APARTMENT_HAS_ACTIVE_BOOKINGS'=>__('apartments.booked'),
                        default     => __('errors.not_found'),
                    },
                    'code' => 404,
                ], 404);
            });

                    $exceptions->render(function (\DomainException $e) {
                        return response()->json([
                            'status' => false,
                            'message' => match ($e->getMessage()) {
                                'APARTMENT_HAS_ACTIVE_BOOKINGS'=>__('apartments.booked'),
                                'BOOKING_CONFLICT'             => __('booking.conflict'),
                                'BOOKING_ALREADY_FINALIZED'   => __('booking.can_not_reject_or_cancel'),
                                'NOT_BOOKRED'=>__('booking.not_booked'),
                                'NOT_APARTMENT_OWNER'=>__('auth.only_owner_allowed'),
                                'FORBIDDEN_ACTION'=>__('auth.forbidden_action'),
                                'NOT_BOOKING_OWNER'=>__('booking.booking_owner'),
                                'BOOKING_NOT_COMPLETED'=>__('booking.booking_not_complete'),
                                'REVIEW_ALREADY_EXISTS'=>__('reviews.review_already_exists'),

                                default                        => __('errors.logic'),
                            },
                            'code' => 409
                            
                        ],
                        
                     409);
                });

              

    })->create();
