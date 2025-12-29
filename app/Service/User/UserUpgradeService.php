<?php

namespace App\Service\User;

use App\Models\OwnerRequest;
use App\Models\User;

class UserUpgradeService
{
    public function submitRequest(User $user): array
    {
        if ($user->ownerRequest()->where('request_status', 'pending')->exists()) {
            return [
                'status' => false,
                'message' => __('auth.existing_pending_request'),
                'code' => 400,
            ];
        }
        if ($user->role === 'owner') {
            return [
                'status' => false,
                'message' => __('auth.already_owner'),
                'code' => 400,
            ];
        }
        OwnerRequest::create([
            'user_id' => $user->id,
            'request_status' => 'pending',
        ]);

        return [
            'status' => true,
            'message' => __('auth.Upgrade_request'),
            'code' => 200,
        ];
    }
    public function checkUpgreadeStatus(User $user)
    {
        $ownerRequest = $user->ownerRequest()->latest()->first();
        if ($ownerRequest->request_status === 'pending') {
            return [
                'status' => true,
                'message' => __('auth.pending'),
                'code' => 200
            ];
        }
        if ($ownerRequest->request_status === 'rejected') {
            return [
                'status' => true,
                'message' => __('auth.rejected'),
                'data' => [
                    'request_rejected_reason' => $ownerRequest->request_rejected_reason,
                ],
                'code' => 200
            ];
        }
        return [
            'status' => true,
            'message' => __('auth.approved'),
            'code' => 200
        ];
    }
}
