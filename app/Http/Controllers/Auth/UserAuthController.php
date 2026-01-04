<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SendOTPPhoneRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Http\Requests\User\UserProfileRequest;
use App\Http\Requests\User\VerifyOTPPhoneRequest;
use App\Http\Resources\UserResource;
use App\Service\User\UserAuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Http\Request;


class UserAuthController extends Controller
{
    protected  UserAuthService $service;

    function __construct(UserAuthService $service)
    {
        $this->service = $service;
    }
    public function sendPhoneOtp(SendOTPPhoneRequest $request)
    {
        $validated = $request->validated();
        $result = $this->service->sendPhoneOtp($validated['phone_number'], $validated['country_code']);
        return response()->json(
            $result,
            $result['code']
        );
    }
    public function resendPhoneOtp(SendOTPPhoneRequest $request)
    {
        $validated = $request->validated();
        $result = $this->service->resendPhoneOtp($validated['phone_number'], $validated['country_code']);
        return response()->json(
            $result,
            $result['code']
        );
    }
    public function verifyPhoneOtp(VerifyOTPPhoneRequest $request)
    {
        $result = $this->service->verifyPhoneOtp(
            $request->phone_number,
            $request->country_code,
            $request->otp
        );

        return response()->json($result, $result['code'])
            ->cookie(
                'otp_session',
                $result['cookie'] ?? '',
                15,
                '/',
                null,
                true,
                true
            );
    }
    public function submitProfile(UserProfileRequest $request)
    {
        $otpToken = $request->bearerToken();


        if (!$otpToken) {
            return response()->json([
                'status' => false,
                'message' => 'OTP token missing'
            ], 401);
        }


        $result = $this->service->submitProfile(
            $request->validated(),
            $otpToken
        );
        return response()->json(
            $result,
            $result['code']
        );
    }
    public function CheakStatus(Request $request)
    {
        $otpToken = $request->bearerToken();

        if (!$otpToken) {
            return response()->json([
                'status' => false,
                'message' => 'OTP token missing'
            ], 401);
        }

        $result = $this->service->chackStatus($otpToken);

        return response()->json(
            $result,
            $result['code']
        );
    }
    public function logout(Request $request)
    {
        $result = $this->service->logout($request);
        return response()->json(
            $result,
            $result['code']
        );
    }
    public function profile()
    {
        $data = $this->service->profile();

        return response()->json(
            $data,
            $data['code']
        );
    }
    public function refresh()
    {
        $result = $this->service->refresh();
        return [$result, $result['code']];
    }
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        $user = auth('user_api')->user();

        $updatedUser = $this->service->updateUserProfile(
            $user,
            $request->validated() + $request->allFiles()
        );
        return response()->json([
            'success' => true,
            'message' => __('auth.profile_updated'),
            'user'   => new UserResource($updatedUser),
            'code'    => 200,
        ]);
    }
}
