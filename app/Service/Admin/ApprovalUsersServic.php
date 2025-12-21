<?php

namespace App\Service\Admin;

use App\Models\User;


class ApprovalUsersServic
{

    public function approveUser(array $data)
    {
        $user = User::findOrFail($data['id']);


        if ($user->status === 'rejected') {
            return [
                'status' => false,
                'message' => __('auth.already_rejected'),
                'code' => 404,
            ];
        }

        $user->update([
            'status' => 'approved'
        ]);

        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('user_api');
        $token = $guard->login($user);

        return [
            'status' => true,
            'message' => __('auth.approved'),
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $guard->factory()->getTTL() * 60,
            ],
                'code' => 200,
        ];
    }
    public function rejectUser(array $data)
    {
        $user = User::findOrFail($data['id']);

        $user->update([
            'status' => 'rejected',
            'rejected_reason' => $data['rejected_reason'],
        ]);

        return [
            'status' => true,
            'message' => __('auth.rejected'),
            'rejected_reasone' => $data['rejected_reason'],
            'code' => 200
        ];
    }

}
