<?php

namespace App\Service\Admin;

use App\Models\User;



class DeletUsersService
{
    public function deleteUsers(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $user->delete();

        return [
            'status' => true,
            'message' => __('auth.deleted'),
            'code' => 200,
        ];
    }
    public function restoreUser(int $userId): array
    {
        $user = User::withTrashed()->findOrFail($userId);

        if (!$user) {

            return [
                'status' => false,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        $user->restore();

        return [
            'status' => true,
            'message' => 'User restored successfully',
            'code' => 200
        ];
    }

}
