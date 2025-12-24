<?php

namespace App\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPassword;
use Exception;

class AdminOtpService
{
    public function sendOtp(string $email): int
    {
        $otp = rand(100000, 999999);

        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp' => $otp,
                'is_verified' => false,
                'is_used' => false,
                'expires_at' => now()->addMinutes(15),
                'updated_at' => now(),
            ]
        );

        Mail::to($email)->send(new ForgetPassword($otp));

        return 15 * 60;
    }
    public function verifyOtp(string $email, string $otp): void
    {
        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->first();

        if (!$record) {
            throw new Exception('INVALID_OTP');
        }

        if (now()->greaterThan($record->expires_at)) {
            throw new Exception('OTP_EXPIRED');
        }

        DB::table('password_reset_otps')
            ->where('id', $record->id)
            ->update(['is_verified' => true]);
    }
    public function resetPassword(string $email, string $hashedPassword): void
    {
        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('is_verified', true)
            ->where('is_used', false)
            ->first();

        if (!$record) {
            throw new Exception('OTP_NOT_VERIFIED');
        }

        if (now()->greaterThan($record->expires_at)) {
            throw new Exception('OTP_EXPIRED');
        }

        DB::table('password_reset_otps')
            ->where('id', $record->id)
            ->update(['is_used' => true]);
    }

}
