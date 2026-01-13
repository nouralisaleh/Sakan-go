<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\Admin\ApprovalUsersService;
use App\Service\Admin\DashbourdService;
use App\Service\Admin\OwnerUpgradeService;
use App\Service\Admin\UserWalletChargeService;

use App\Models\User;
use App\Service\Admin\DeletUsersService;

class Admincontroller extends Controller
{
    protected ApprovalUsersService $approvalUsersService;
    protected DashbourdService $dashbourdService;
    protected OwnerUpgradeService $ownerUpgradeService;
    protected DeletUsersService $deletUsersService;
    protected UserWalletChargeService $userWalletChargeService;

    public function __construct(
        ApprovalUsersService $approvalUsersService,
        DashbourdService $dashbourdService,
        OwnerUpgradeService $ownerUpgradeService,
        DeletUsersService $deletUsersService,
        UserWalletChargeService $userWalletChargeService
    ) {
        $this->approvalUsersService = $approvalUsersService;
        $this->dashbourdService = $dashbourdService;
        $this->ownerUpgradeService = $ownerUpgradeService;
        $this->deletUsersService = $deletUsersService;
        $this->userWalletChargeService = $userWalletChargeService;
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
            'rejected_reasons' => 'required|array|min:1',
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
            'request_rejected_reason' => 'required',
        ]);

        $result =  $this->ownerUpgradeService->rejectUpgradeRequest($validated);
        return response()->json(
            $result,
            $result['code'],
        );
    }
    public function deleteUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        $result = $this->deletUsersService->deleteUsers($validated);
        return response()->json(
            $result,
            $result['code']
        );
    }
    public function restoreUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        $result = $this->deletUsersService->restoreUser($validated['user_id']);
        return response()->json(
            $result,
            $result['code']
        );
    }
public function ChargeWallet(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1|max:1000000000',
        ]);
        $result = $this->userWalletChargeService->chargeUserWallet($validated['user_id'], $validated['amount']);
        return response()->json(
            $result,
            $result['code']
        );
    }

}