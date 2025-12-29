<?php

namespace App\Service\Admin;

use App\Models\User;



class DeletUsersService
{

    public function deleteUser(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $user->delete();

        return [
            'status' => true,
            'message' => __('auth.deleted'),
            'code' => 204
        ];
    }
    
}
