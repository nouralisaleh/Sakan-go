<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Service\User\UserUpgradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Service\Chat\ChatService;



class UserController extends Controller
{
    protected UserUpgradeService $userUpgradeService;
    protected ChatService $chatService;

    public function __construct(
        UserUpgradeService $userUpgradeService,
        ChatService $chatService
    ) {
        $this->userUpgradeService = $userUpgradeService;
        $this->chatService = $chatService;
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
    public function inbox()
    {
        $userId = auth('user_api')->id();

        return response()->json([
            'status' => true,
            'data' => $this->chatService->inbox($userId),
        ], 200);
    }
    public function startChat(Request $request, ChatService $chatService)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id'
        ]);

        $user = auth('user_api')->user();

        $chat = $chatService->startChat(
            $user->id,
            $request->apartment_id
        );

        return response()->json([
            'chat_id' => $chat->id
        ], 200);
    }

   


}
