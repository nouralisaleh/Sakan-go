<?php

namespace App\Service\User;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\UserProfile;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;

class UserAuthService
{
    protected int $otpExpiresMinutes = 10;
    protected int $resendCooldownSeconds = 60;
    protected int $code;


    public function sendPhoneOtp(string $phone, string $country_code): array
    {
        $existingOtp = DB::table('phone_otps')
            ->where('phone_number', $phone)
            ->where('country_code', $country_code)
            ->first();

        if ($existingOtp && now()->lt($existingOtp->expires_at)) {

            $seconds = max(
                0,
                now()->diffInSeconds($existingOtp->resend_available_at, false)
            );

            return [
                'status' => false,
                'message' => __('auth.otp_already_sent', ['target' => 'phone number']),
                'data' => [
                    'resend_available_at' => $existingOtp->resend_available_at,
                    'resend_after_seconds' => $seconds,
                ],
                'code' => 429,
            ];
        }

        if ($existingOtp && now()->gte($existingOtp->expires_at)) {
            DB::table('phone_otps')->where('id', $existingOtp->id)->delete();
        }

        $otp = rand(100000, 999999);

        $expiresAt = now()->addMinutes($this->otpExpiresMinutes);
        $resendAvailableAt = now()->addSeconds(60);

        DB::table('phone_otps')->insert([
            'phone_number' => $phone,
            'country_code' => $country_code,
            'otp' => $otp,
            'is_verified' => false,
            'is_used' => true,
            'expires_at' => $expiresAt,
            'resend_available_at' => $resendAvailableAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->sendOtpMessage($country_code . $phone, $otp);

        return [
            'status' => true,
            'message' => __('auth.otp_sent', ['target' => 'phone number']),
            'data' => [
                'expires_in' => $this->otpExpiresMinutes * 60,
                'resend_available_at' => $resendAvailableAt->toIso8601String(),
            ],
            'code' => 200,
        ];
    }
    public function resendPhoneOtp(string $phone, string $country_code): array
    {
        $record = DB::table('phone_otps')
            ->where('phone_number', $phone)
            ->where('country_code', $country_code)
            ->first();

        if (!$record) {
            return [
                'status' => false,
                'message' => __('auth.otp_not_found', ['target' => 'phone number']),
                'code' => 404,
            ];
        }

        if (now()->gte($record->expires_at)) {
            DB::table('phone_otps')->where('id', $record->id)->delete();

            return [
                'status' => false,
                'message' => __('auth.otp_expired', ['target' => 'phone number']),
                'code' => 410,
            ];
        }

        if (now()->lt($record->resend_available_at)) {

            $seconds = max(
                0,
                now()->diffInSeconds($record->resend_available_at, false)
            );

            return [
                'status' => false,
                'message' => __('auth.otp_resend_not_allowed', ['target' => 'phone number']),
                'data' => [
                    'resend_after_seconds' => $seconds,
                ],
                'code' => 429,
            ];
        }

        // إعادة إرسال OTP
        $otp = rand(100000, 999999);

        $expiresAt = now()->addMinutes($this->otpExpiresMinutes);
        $resendAvailableAt = now()->addSeconds(60);

        DB::table('phone_otps')->where('id', $record->id)->update([
            'otp' => $otp,
            'is_verified' => false,
            'is_used' => false,
            'expires_at' => $expiresAt,
            'resend_available_at' => $resendAvailableAt,
            'updated_at' => now(),
        ]);

        $this->sendOtpMessage($record->country_code . $record->phone_number, $otp);

        return [
            'status' => true,
            'message' => __('auth.otp_resent', ['target' => 'phone number']),
            'data' => [
                'expires_in' => $this->otpExpiresMinutes * 60,
                'resend_available_at' => $resendAvailableAt->toIso8601String(),
            ],
            'code' => 200,
        ];
    }
    private function sendOtpMessage(string $to, int $otp): void
    {
        $body = "Your verification code is: {$otp}.";

        $instance = env('ULTRAMSG_INSTANCE_ID');
        $token = env('ULTRAMSG_TOKEN');

        $res = Http::asForm()->post(
            "https://api.ultramsg.com/{$instance}/messages/chat",
            [
                'token' => $token,
                'to' => $to,
                'body' => $body
            ]
        );

        if ($res->failed()) {
            Log::error('UltraMsg send failed', ['response' => $res->body()]);
            throw new \Exception('OTP sending failed');
        }
    }
    public function verifyPhoneOtp(string $phone, string $country_code, string $otp): array
    {
        $record = DB::table('phone_otps')
            ->where('phone_number', $phone)
            ->where('country_code', $country_code)
            ->first();


        if (!$record) {
            return [
                'status' => false,
                'message' => __(
                    'auth.otp_invalid',
                    ['target' => 'phone number']
                ),
                'code' => 422,
            ];
        }
        if (now()->greaterThan($record->expires_at)) {
            return [
                'status' => false,
                'message' => __('auth.otp_expired', ['target' => 'phone number']),
                'code' => 410,
            ];
        }
        if ((string)$record->otp !== (string)$otp) {
            return [
                'status' => false,
                'message' => __('auth.otp_invalid', ['target' => 'phone number']),
                'code' => 422
            ];
        }
        DB::table('phone_otps')
            ->where('id', $record->id)
            ->update(
                ['is_verified' => true, 'updated_at' => now(), 'is_used' => true]
            );
        $user = User::where(
            'phone_number',
            $phone
        )->first();
        if ($user) {
            if ($user->status === 'approved') {
                return [
                    'status' => true,
                    'message' => __('auth.logged_in'),
                    'data' => [
                        'token' => JWTAuth::fromUser($user),
                        'token_type' => 'bearer'
                    ],
                    'code' => 200,
                ];
            }
            if ($user->status === 'pending') {
                return [
                    'status' => false,
                    'message' => __('auth.pending'),
                    'code' => 403,
                ];
            }
            if ($user->status === 'rejected') {
                return [
                    'status' => false,
                    'message' => __('auth.rejected'),
                    'code' => 403,
                ];
            }
        }
        return [
            'status' => true,
            'message' => __('auth.profile_does_not_exist'),
            'data' => [
                'phone_number' => $phone,
                'country_code' => $country_code,
                'is_phone_verified' => true,
                'verified_at' => now()->toIso8601String(),
            ],
            'code' => 200
        ];
    }
    public function submitProfile(array $data): array
    {
        $otp = DB::table('phone_otps')
            ->where('phone_number', $data['phone_number'])
            ->where('country_code', $data['country_code'])
            ->where('is_verified', true)
            ->where('updated_at', '>=', now()
                ->subMinutes(15))
            ->first();
        if (!$otp) {
            return [
                'status' => false,
                'message' => __('auth.phone_not_verified'),
                'code' => 401
            ];
        }
        $user = User::updateOrCreate(
            ['phone_number' => $data['phone_number']],
            [
                'country_code' => $data['country_code'],
                'status' => 'pending',
                'role' => 'tenant',
                'phone_verified_at' => now(),
            ]
        );
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'birth_date' => $data['birth_date'],
                'personal_image' => $data['personal_image'] ?? null,
                'id_image' => $data['id_image'] ?? null,
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );
        return [
            'status' => true,
            'message' => __('auth.user_profile_submitted'),
            'code' => 200
        ];
    }
    public function logout()
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('user_api');
        $guard->logout();

        return response()->json([
            'success' => true,
            'message' => __('auth.logged_out')
        ], 200);
    }
    public function refresh()
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('admin_api');

        $newToken = $guard->refresh();

        return [
            "success" => true,
            "message" => __('auth.refreshed'),
            'token' => $newToken,
            'token_type' => 'bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
        ];
    }
    public function profile(): array
    {
        $user = auth('user_api')->user();

        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.failed_profile'),
                'code' => 401
            ];
        }

        return [
            'success' => true,
            'user' => new UserResource($user),
            'code' => 200
        ];
    }
    public function updateUserProfile(User $user, array $data): User
    {
        $updates = [];

        foreach (
            [
                'first_name',
                'last_name',
                'birth_date',
                'phone_number',
                'country_code'
            ] as $field
        ) {
            if (isset($data[$field])) {
                $updates[$field] = $data[$field];
            }
        }

        $profile = $user->profile;

        if (isset($data['personal_image'])) {

            if ($profile->personal_image && Storage::disk('private')->exists($profile->personal_image)) {
                Storage::disk('private')
                    ->delete($profile->personal_image);
            }

            $updates['personal_image'] = $data['personal_image']->store(
                'personal_images/' . $profile->id,
                'private'
            );
        }

        if (isset($data['id_image'])) {

            if ($profile->id_image && Storage::disk('private')->exists($profile->id_image)) {
                Storage::disk('private')->delete($profile->id_image);
            }

            $updates['id_image'] = $data['id_image']->store(
                'id_images/' . $profile->id,
                'private'
            );
        }

        if (!empty($updates)) {
            $profile->update($updates);
        }

        return $user->load('profile');
    }
}
