<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetApplang;
use App\Http\Middleware\VerifyCsrfToken;    
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(SetApplang::class);
    })
     ->withExceptions(function (Exceptions $exceptions) {
             $exceptions->render(function (NotFoundHttpException $e) {

                return response()->json([
                     'status' => false,
                     'message' => match ($e->getMessage()) {
                        'APARTMENT_NOT_FOUND' => __('apartments.not_found'),
                        'BOOKING_NOT_FOUND'   => __('booking.not_found'),
                        'Booking'=>__('booking.not_found'),
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
                                'NOT_BOOKRD'=>__('booking.not_booked'),
                                'NOT_APARTMENT_OWNER'=>__('auth.only_owner_allowed'),
                                'FORBIDDEN_ACTION'=>__('auth.forbidden_action'),

                                default                        => __('errors.logic'),
                            }
                        ], 422);
                    });

                })->create();
