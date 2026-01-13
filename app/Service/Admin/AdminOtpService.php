<?php

namespace App\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Mail\ForgetPassword;
use Carbon\Carbon;

class AdminOtpService
{
    public function sendOtp(string $email): array
    {

        $exitingOtp = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('is_used', false)
            ->first();

        if ($exitingOtp) {
            $expiresAt = Carbon::parse($exitingOtp->expires_at);
            if (now()->lessThan($expiresAt)) {
                return [
                    'message' => __('auth.otp_already_sent', ['target' => 'email']),
                    'retry_after' => (int) now()->diffInSeconds($expiresAt),
                    'code' => 422
                ];
            }
        }
        $otp = rand(100000, max: 999999);

        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp' => $otp,
                'is_verified' => false,
                'is_used' => false,
                'expires_at' => now()->addMinutes(15),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Mail::to($email)->send(new ForgetPassword($otp));

        return [
            'message' => __(
                'auth.otp_sent',
                ['target' => 'email']
            ),
            'code' => 200
        ];
    }
    public function verifyEmailOtp(string $email, string $otp): array
    {
        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->first();

        if (!$record) {
            return [
                'message' => __(
                    'auth.otp_invalid',
                    ['target' => 'email']
                ),
                'code' => 422
            ];
        }
        $expiresAt = Carbon::parse($record->expires_at);

        if (now()->greaterThan($expiresAt)) {
            return [
                'message' => __('auth.otp_expired',
                 ['target' => 'email']),
                'code' => 410
            ];
        }

        DB::table('password_reset_otps')
            ->where('id', $record->id)
            ->update(['is_verified' => true]);

        return [
            'message' => __(
                'auth.otp_verified',
                ['target' => 'email']
            ),
            'code' => 200
        ];
    }
    public function resetPassword(string $email, string $hashedPassword): array
    {
        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('is_verified', true)
            ->where('is_used', false)
            ->first();

        if (!$record) {
            return [
                'message' => __(
                    'auth.otp_not_verified',
                    ['target' => 'email']
                ),
                'code' => 400
            ];
        }
        if (now()->greaterThan(Carbon::parse($record->expires_at))) {
            return [
                'message' => __(
                    'auth.otp_expired',
                    ['target' => 'email']
                ),
                'code' => 400
            ];
        }

        Admin::where('email', $email)->update([
            'password' => Hash::make($hashedPassword)
        ]);

        DB::table('password_reset_otps')
            ->where('id', $record->id)
            ->update(['is_used' => true]);

        return [
            'message' => __('auth.password_reset_success'),
            'code' => 200
        ];
    }
}
