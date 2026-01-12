<?php

namespace App\Service\Admin;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class UserWalletChargeService
{
    public function chargeUserWallet(int $userId, float $amount): array
    {
        return DB::transaction(function () use ($userId, $amount) {

            $user = User::findOrFail($userId);

            $wallet = $user->wallet;

            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                ]);
            }

            $wallet->increment('balance', $amount);

            return [
                'success' => true,
                'message' => __('auth.wallet_charged'),
                'data' => [
                    'user_id' => $user->id,
                    'new_balance' => $wallet->balance,
                ],
                'code' => 200,
            ];
        });
    }
}