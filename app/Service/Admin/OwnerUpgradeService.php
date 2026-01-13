<?php

namespace App\Service\Admin;

use Illuminate\Http\Request;
use App\Models\OwnerRequest;


class OwnerUpgradeService
{
    public function approveUpgradeRequest(int $requestId): array
    {
        $request = OwnerRequest::findOrFail($requestId);

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
            'data' => [
                'request_status' => 'approved',
                'request_rejected_reason' => null,
            ],
            'code' => 200
        ];
    }
    public function rejectUpgradeRequest(array $data): array
    {
        $request = OwnerRequest::findOrFail($data['request_id']);

        $request->update([
            'request_status' => 'rejected',
            'request_rejected_reason' => $data['request_rejected_reason'],
        ]);

        return [
            'status' => true,
            'message' => __('auth.rejected_request'),
            'data' => [
                'request_status' => 'rejected',
                'request_rejected_reason' => $request->request_rejected_reason,
            ],
            'code' => 200
        ];
    }


}
