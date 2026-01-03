<?php

namespace App\Service\User;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Support\Str;
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

        // إذا في OTP موجود
        if ($existingOtp) {

            // إذا لسه وقت إعادة الإرسال ما خلص
            if (now()->lt($existingOtp->resend_available_at)) {

                $seconds = now()->diffInSeconds($existingOtp->resend_available_at);

                return [
                    'status' => false,
                    'message' => __('auth.otp_already_sent', ['target' => 'phone number']),
                    'data' => [
                        'resend_after_seconds' => $seconds,
                    ],
                    'code' => 429,
                ];
            }

            // إذا وقت الإعادة خلص → نحدّث الكود
            $otp = rand(100000, 999999);

            DB::table('phone_otps')
                ->where('id', $existingOtp->id)
                ->update([
                    'otp' => $otp,
                    'is_verified' => false,
                    'is_used' => false,
                    'expires_at' => now()->addMinutes($this->otpExpiresMinutes),
                    'resend_available_at' => now()->addSeconds(60),
                    'updated_at' => now(),
                ]);

            $this->sendOtpMessage($country_code . $phone, $otp);

            return [
                'status' => true,
                'message' => __('auth.otp_sent', ['target' => 'phone number']),
                'data' => [
                    'expires_in' => $this->otpExpiresMinutes * 60,
                    'resend_available_at' => now()->addSeconds(60)->toIso8601String(),
                ],
                'code' => 200,
            ];
        }

        // أول مرة – لا يوجد OTP سابق
        $otp = rand(100000, 999999);

        DB::table('phone_otps')->insert([
            'phone_number' => $phone,
            'country_code' => $country_code,
            'otp' => $otp,
            'is_verified' => false,
            'is_used' => false,
            'expires_at' => now()->addMinutes($this->otpExpiresMinutes),
            'resend_available_at' => now()->addSeconds(60),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->sendOtpMessage($country_code . $phone, $otp);

        return [
            'status' => true,
            'message' => __('auth.otp_sent', ['target' => 'phone number']),
            'data' => [
                'expires_in' => $this->otpExpiresMinutes * 60,
                'resend_available_at' => now()->addSeconds(60)->toIso8601String(),
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
        $body = __('auth.message_body', ['otp' => $otp]);

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
        $token = Str::uuid()->toString();

        DB::table('otp_sessions')->insert([
            'token' => $token,
            'phone_number' => $phone,
            'country_code' => $country_code,
            'expires_at' => now()->addHours(4),
            'created_at' => now(),
        ]);

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
                        'token_type' => 'bearer',
                        'user_role' => $user->role,
                    ],
                    'code' => 200,
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
                'cookie' => $token,
            ],
            'code' => 200
        ];
    }
    public function submitProfile(array $data, string $otpToken): array
    {
        $session = DB::table('otp_sessions')
            ->where('token', $otpToken)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            return [
                'message' => 'OTP session invalid',
                'code' => 401
            ];
        }

        $user = User::updateOrCreate(
            ['phone_number' => $session->phone_number],
            [
                'country_code' => $session->country_code,
                'status' => 'pending',
                'role' => 'tenant',
                'phone_verified_at' => now(),
            ]
        );

        $profileData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'birth_date' => $data['birth_date'],
            'is_completed' => true,
            'completed_at' => now(),
        ];

        if (isset($data['personal_image'])) {
            $profileData['personal_image'] = $data['personal_image']->store(
                'Users/personal_images/' . $user->id,
                'private'
            );
        }

        if (isset($data['id_image'])) {
            $profileData['id_image'] = $data['id_image']->store(
                'Users/id_images/' . $user->id,
                'private'
            );
        }

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return [
            'status' => true,
            'message' => __('auth.user_profile_submitted'),
            'code' => 200
        ];
    }
    public function logout(): array
    {

        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('user_api');
        $guard->logout();
        return[
            'status' => true,
            'message' => __('auth.logged_out'),
            'code' => 200
        ];
    }
    public function refresh(): array
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('user_api');
        $token = $guard->refresh();

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $guard->getTTL() * 60,
            'code' => 200,
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
    public function chackStatus(string $otptoken): array
    {
        $session = DB::table('otp_sessions')
            ->where('token', $otptoken)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            return [
                'status' => false,
                'message' => 'Session expired',
                'code' => 401,
            ];
        }
        $user = User::where('phone_number', $session->phone_number)->first();
        if ($user->status === 'pending') {
            return [
                'status' => 'true',
                'message' => __('auth.pending'),
                'data' => [
                    'user_status' => 'pending',
                    'rejected_reason' => null,
                    'token' => null
                ],
                'code' => 200,
            ];
        }
        if ($user->status === 'rejected') {
            return [
                'status' => 'true',
                'message' => __('auth.rejected'),
                'data' => [
                    'user_status' => 'rejected',
                    'rejected_reason' => $user->rejected_reason,
                    'token' => null
                ],
                'code' => 200,
            ];
        }
        DB::table('otp_sessions')->where('id', $session->id)->delete();

        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('user_api');
        $token = $guard->login($user);

        return [
            'status' => 'true',
            'message' => __('auth.approved'),
            'data' => [
                'user_status' => 'approved',
                'rejected_reason' => null,
                'token' => $token,
            ],
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
                'Users/personal_images/' . $profile->id,
                'private'
            );
        }

        if (isset($data['id_image'])) {

            if ($profile->id_image && Storage::disk('private')->exists($profile->id_image)) {
                Storage::disk('private')->delete($profile->id_image);
            }

            $updates['id_image'] = $data['id_image']->store(
                'Users/id_images/' . $profile->id,
                'private'
            );
        }

        if (!empty($updates)) {
            $profile->update($updates);
        }

        return $user->load('profile');
    }
   
}
