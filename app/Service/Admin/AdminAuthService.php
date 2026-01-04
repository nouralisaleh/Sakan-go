<?php

namespace App\Service\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;

class AdminAuthService
{
    public function login(array $data, bool $remember): array
    {
        $admin = Admin::where('email', $data['email'])->first();

        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            return [
                'message' => __('auth.invalid_credentials'),
                'code' => 401
            ];
        }
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('admin_api');

        $ttl = $remember ? 60 * 24 * 7 : 60;

        $token = $guard->setTTL($ttl)->login($admin);

        return [
            'message' => __('auth.logged_in'),
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $guard->factory()->getTTL() * 60,
            ],
            'code' => 200
        ];
    }
    public function logout(): array
    {

        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('admin_api');
        $guard->logout();
        return [
            'message' => __('auth.logged_out'),
            'code' => 200
        ];
    }
    public function refresh(): array
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('admin_api');
        $token = $guard->refresh();

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $guard->getTTL() * 60,
            'code' => 200
        ];
    }
    public function updateProfile(Admin $admin, array $data): array
    {

        $updates = [];

        foreach (
            [
                'name',
                'birth_date',
                'phone_number',

            ] as $field
        ) {
            if (isset($data[$field])) {
                $updates[$field] = $data[$field];
            }
        }
        if (isset($data['personal_image'])) {

            if ($admin->personal_image && Storage::disk('private')->exists($admin->personal_image)) {
                Storage::disk('private')
                    ->delete($admin->personal_image);
            }

            $updates['personal_image'] = $data['personal_image']->store(
                'Admin/personal_images/' . $admin->id,
                'private'
            );
        }


        if (isset($data['id_image'])) {

            if ($admin->id_image && Storage::disk('private')->exists($admin->id_image)) {
                Storage::disk('private')->delete($admin->id_image);
            }

            $updates['id_image'] = $data['id_image']->store(
                'Admin/id_images/' . $admin->id,
                'private'
            );
        }

        if (!empty($updates)) {
            $admin->update($updates);
        }

        return [
            'message' => __('auth.profile_updated'),
            'data' => new AdminResource($admin),
            'code' => 200
        ];
    }
    public function show(Admin $admin): array
    {

        return [
            'status' => 'success',
            'data' => new AdminResource($admin),
            'code' => 200
        ];
    }

}
