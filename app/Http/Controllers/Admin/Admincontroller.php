<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\Admin\ApprovalUsersServic;
use App\Service\Admin\DashbourdService;
use App\Service\Admin\OwnerUpgradeService;

use App\Models\User;

class Admincontroller extends Controller
{
    protected ApprovalUsersServic $approvalUsersService;
    protected DashbourdService $dashbourdService;
    protected OwnerUpgradeService $ownerUpgradeService;

    public function __construct(
        ApprovalUsersServic $approvalUsersService,
        DashbourdService $dashbourdService,
        OwnerUpgradeService $ownerUpgradeService
    ) {


        $this->approvalUsersService = $approvalUsersService;
        $this->dashbourdService = $dashbourdService;
        $this->ownerUpgradeService = $ownerUpgradeService;
    }
    public function approve(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
        ]);
        $result = $this->approvalUsersService->approveUser($validated);
        return response()->json(
            $result,
            $result['code']
        );
    }
    public function reject(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
            'rejected_reason' => 'required|string|max:255',
        ]);

        $result = $this->approvalUsersService->rejectUser($validated);
        return response()->json(
            $result,
            $result['code']
        );
    }
    public function dashbourd(Request $request)
    {
        return $this->dashbourdService->index($request);
    }
    public function show(User $user)
    {
        return $this->dashbourdService->showUser($user);
    }
    public function approveUpgrade(Request $request)
    {

        $validated = $request->validate([
            'request_id' => 'required|exists:owner_requests,id',
        ]);
        $result =  $this->ownerUpgradeService->approveUpgradeRequest(
            $validated['request_id']

        );

        return response()->json(
            $result,
            $result['code']
        );
    }
    public function rejectUpgrade(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:owner_requests,id',
            'rejection_reason' => 'required|string|max:255',
        ]);

        $result =  $this->ownerUpgradeService->rejectUpgradeRequest(
            $validated['request_id'],
            $validated['rejection_reason']
        );

        return response()->json(
            $result,
            $result['code'],
        );
    }
}
