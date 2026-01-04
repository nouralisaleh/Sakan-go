<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Service\Booking\BookingService;

Artisan::command('inspire', function () {
    
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::call(function () {
    app(BookingService::class)->autoCompleteExpiredBookings();
})->everyMinute();