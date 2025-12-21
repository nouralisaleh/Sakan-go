<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Service\Admin\AdminAuthService;
use App\Http\Resources\AdminResource;
use App\Http\Requests\Admin\UpdateProfileRequest;
use APP\Http\Requests\Admin\SendOTPRequest;
use App\Http\Requests\Admin\VerifyOTPRequest;
use App\Service\Admin\AdminOtpService;



class AdminAuthController extends Controller
{
    public function login(AdminLoginRequest $request, AdminAuthService $service)
    {

        try {
            $result = $service->login(
                $request->validated(),
                $request->boolean('remember', false)
            );

            return response()->json([
                'message' => __('auth.logged_in'),
                'admin'   => new AdminResource($result['admin']),
                'access_token' => $result['access_token'],
                'token_type'   => $result['token_type'],
                'expires_in'   => $result['expires_in'],
            ], 200);
        } catch (\Exception $e) {

            if ($e->getMessage() === 'INVALID_CREDENTIALS') {
                return response()->json([
                    'success' => false,
                    'message' => __('auth.invalid_credentials')
                ], 401);
            }

            throw $e;
        }
    }

    public function logout(AdminAuthService $service)
    {
        $service->logout();

        return response()->json([
            'success' => true,
            'message' => __('auth.logged_out')
        ], 200);
    }

    public function refresh(AdminAuthService $service)
    {
        $data = $service->refresh();

        return response()->json([
            'success' => true,
            $data
        ], 201);
    }

    public function profile()
    {
        return response()->json([
            'success' => true,
            'admin' => new AdminResource(auth('admin_api')->user()),
        ], 200);
    }

    public function updateProfile(UpdateProfileRequest $request, AdminAuthService $service)
    {
        $admin = auth('admin_api')->user();

        $updatedAdmin = $service->updateProfile(
            $admin,
            $request->validated() + $request->allFiles()
        );

        return response()->json([
            'success' => true,
            'message' => __('auth.profile_updated'),
            'admin'   => new AdminResource($updatedAdmin)
        ], 201);
    }

    public function sendOtp(SendOTPRequest $request, AdminOtpService $otpService)
    {
        $data = $request->validated();

        try {
            $expires = $otpService->sendOtp($data['email']);

            return response()->json([
                'status' => true,
                'message' => __('auth.otp_sent', ['target' => 'email']),
                'data' => ['expires_in' => $expires]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('auth.unable_send_otp', ['target' => 'email']),
            ], 500);
        }
    }

    public function verifyOtp(VerifyOTPRequest $request, AdminOtpService $service)
    {
        $result = $service->verifyOtp(
            $request->email,
            $request->otp
        );

        return response()->json($result, $result['status'] ? 200 : 400);
    }

    
}
