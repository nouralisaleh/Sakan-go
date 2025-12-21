<?php

namespace App\Service\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminAuthService
{
    public function login(array $data, bool $remember): array
    {
        $admin = Admin::where('email', $data['email'])->first();

        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            throw new \Exception('INVALID_CREDENTIALS', 401);
        }
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('admin_api');

        // TTL
        $ttl = $remember ? 60 * 24 * 7 : 60;
        $guard->setTTL($ttl);

        $token = $guard->login($admin);

        return [
            'admin' => $admin,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
        ];
    }
    public function logout(): void
    {

        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('admin_api');
        $guard->logout();
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
        ];
    }
    public function updateProfile(Admin $admin, array $data): Admin
    {
        $updates = [];

        foreach (
            [
                'name',
                'birth_date',
                'email',
                'phone_number',
                'country_code'

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
                'personal_images/' . $admin->id,
                'private'
            );
        }


        if (isset($data['id_image'])) {

            if ($admin->id_image && Storage::disk('private')->exists($admin->id_image)) {
                Storage::disk('private')->delete($admin->id_image);
            }

            $updates['id_image'] = $data['id_image']->store(
                'id_images/' . $admin->id,
                'private'
            );
        }

        if (!empty($updates)) {
            $admin->update($updates);
        }

        return $admin;
    }
    
}
