<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Service\User\UserUpgradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    protected UserUpgradeService $userUpgradeService;

    public function __construct(UserUpgradeService $userUpgradeService)
    {
        $this->userUpgradeService = $userUpgradeService;
    }
    public function upgrade()
    {
        $user = Auth::guard('user_api')->user();
        $result = $this->userUpgradeService->submitRequest($user);
        return response()->json(
            $result,
            $result['code'],
        );
    }

}
