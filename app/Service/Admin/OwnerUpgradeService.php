<?php

namespace App\Service\Admin;

use Illuminate\Http\Request;
use App\Models\OwnerRequest;


class OwnerUpgradeService
{

    public function approveUpgradeRequest(int $requestId): array
    {
        $request = OwnerRequest::with('user')->findOrFail($requestId);

        if ($request->request_status === 'rejected') {
            return [
                'status' => false,
                'message' => __('auth.already_rejected'),
                'code' => 404,
            ];
        }

        $request->update([
            'request_status' => 'approved',
            'request_rejected_reason' => null,
        ]);

        $request->user->update([
            'role' => 'owner',
        ]);

        return [
            'status' => true,
            'message' => __('auth.approved_request'),
            'code' => 200
        ];
    }
    public function rejectUpgradeRequest(int $requestId, string $reason): array
    {
        $request = OwnerRequest::findOrFail($requestId);

        $request->update([
            'request_status' => 'rejected',
            'request_rejected_reason' => $reason,
        ]);

        return [
            'status' => true,
            'message' => __('auth.rejected_request'),
            'code' => 200
        ];
    }
    
}
