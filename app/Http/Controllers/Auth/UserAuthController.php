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
        $validated = $request->validated();
        $result = $this->service->verifyPhoneOtp(
            $validated['phone_number'],
            $validated['country_code'],
            $validated['otp']
        );
        return response()->json(
            $result,
            $result['code']
        );
    }
    public function submitProfile(UserProfileRequest $request)
    {
        $data = $request->only(['phone_number', 'country_code', 'first_name', 'last_name', 'birth_date']);
        if ($request->hasFile('personal_image')) {
            $data['personal_image'] = $request
                ->file('personal_image')
                ->store('personal_images', 'private');
        }
        if ($request->hasFile('id_image')) {
            $data['id_image'] = $request
                ->file('id_image')
                ->store('id_images', 'private');
        }
        $result = $this->service->submitProfile($data);
        return response()->json(
            $result,
            $result['code']
        );
    }
    public function logout(Request $request)
    {
        return $this->service->logout($request);
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
        return $this->service->refresh();
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
            'user'   => new UserResource($updatedUser)
        ], 201);
    }
}
