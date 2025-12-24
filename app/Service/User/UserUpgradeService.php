<?php

namespace App\Service\User;

use App\Models\OwnerRequest;
use App\Models\User;

class UserUpgradeService
{
    public function submitRequest(User $user): array
    {

        OwnerRequest::create([
            'user_id' => $user->id,
            'request_status' => 'pending',
        ]);

        return [
            'status' => true,
            'message' => __('auth.Upgrade_request'),
            'code'=>200,
        ];
    }
}
