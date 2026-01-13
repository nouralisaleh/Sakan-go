<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Service\Admin\AdminAuthService;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Requests\Admin\SendOTPRequest;
use App\Http\Requests\Admin\VerifyOTPRequest;
use App\Service\Admin\AdminOtpService;
use App\Models\Admin;




class AdminAuthController extends Controller
{
    protected AdminOtpService $adminOtpService;
    protected AdminAuthService $adminAuthService;

    public function __construct(AdminOtpService $adminOtpService, AdminAuthService $adminAuthService)
    {
        $this->adminOtpService = $adminOtpService;
        $this->adminAuthService = $adminAuthService;
    }
    public function login(AdminLoginRequest $request,)
    {

        $result = $this->adminAuthService->login(
            $request->validated(),
            $request->boolean('remember', false)
        );

        return response()->json([
            $result,
        ], $result['code']);
    }
    public function logout()
    {
        $result = $this->adminAuthService->logout();

        return response()->json([
            $result
        ], $result['code']);
    }
    public function refresh()
    {
        $result = $this->adminAuthService->refresh();

        return response()->json([
            $result,
            $result['code']
        ]);
    }
    public function profile()
    {
        $admin = auth('admin_api')->user();

        $result = $this->adminAuthService->show($admin);

        return response()->json([
            $result,
            $result['code']
        ]);
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        $admin = auth('admin_api')->user();

        $updatedAdmin = $this->adminAuthService->updateProfile(
            $admin,
            $request->validated() + $request->allFiles()
        );

        return response()->json([
            $updatedAdmin,
            $updatedAdmin['code']
        ]);
    }
    public function sendOtp(SendOTPRequest $request)
    {
        $data = $request->validated();

        try {
            $result = $this->adminOtpService->sendOtp($data['email']);

            return response()->json([
                $result,
                $result['code']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __(
                    'auth.unable_send_otp',
                    ['target' => 'email'],
                ),
                'code' => 500
            ]);
        }
    }
    public function verifyOtp(VerifyOTPRequest $request)
    {
        $result = $this->adminOtpService->verifyEmailOtp(
            $request->email,
            $request->otp
        );

        return response()->json(
            $result,
            $result['code']
        );
    }
    public function resetPasswordWithOtp(ResetPasswordRequest $request)
    {
        $validate = $request->validated();
        $result = $this->adminOtpService->resetPassword(
            $validate['email'],
            $validate['new_password']
        );

        return response()->json(
            $result,
            $result['code']
        );
    }

}
