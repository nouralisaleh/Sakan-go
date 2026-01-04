<?php

namespace App\Service\Admin;

use App\Models\User;


class ApprovalUsersService
{

    public function approveUser(array $data)
    {
        $user = User::findOrFail($data['id']);

        $user->update([
            'status' => 'approved',
            'rejected_reason' => null,
        ]);

        return [
            'status' => true,
            'message' => __('auth.approved'),
            'data' => [
                'user_status' => 'approved',
                'rejected_reason' => null,
            ],
            'code' => 200,
        ];
    }
    public function rejectUser(array $data)
    {
        $user = User::findOrFail($data['id']);

        $user->update([
            'status' => 'rejected',
            'rejected_reason' => $data['rejected_reasons'],
        ]);

        return [
            'status' => true,
            'message' => __('auth.rejected'),
            'data' => [
                'user_status' => 'rejected',
                'rejected_reason' => $user->rejected_reason,
            ],
            'code' => 200
        ];
    }

}
