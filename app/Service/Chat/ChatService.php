<?php

namespace App\Service\Chat;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Chat;
use Google\Service\AccessContextManager\Status;

class ChatService
{
    public function startChat(int $tenantId, int $apartmentId): array
    {
        $apartment = Apartment::with('owner')->findOrFail($apartmentId);


        $ownerId = $apartment->user_id;

        $owner = User::with('profile')->find($ownerId);


        if (!$ownerId) {
            throw new \Exception('This apartment does not have an owner yet.');
        }

        $chat = Chat::where('tenant_id', $tenantId)
            ->where('owner_id', $ownerId)
            ->where('apartment_id', $apartmentId)
            ->first();
        if ($tenantId === $ownerId) {
            return [
                'status' => false,
                'message' => 'You cannot start a chat with yourself.',
                'code' => 400
            ];
        }
        if ($chat) {
            return [
                'status' => true,
                'message' => 'Chat already exists',
                'data' => [
                    'chat_id' => $chat->id,
                    'firebase_chat_id' => $chat->firebase_chat_id,
                    'owner_name' => ($owner->profile->first_name ?? '') . ' ' . ($owner->profile->last_name ?? ''),
                    'owner_image' => $owner->profile->personal_image ?? null,
                ],
                'code' => 200
            ];
        }
        Chat::create([
            'tenant_id' => $tenantId,
            'owner_id' => $ownerId,
            'apartment_id' => $apartmentId,
            'firebase_chat_id' => Str::uuid()->toString(),
        ]);

        return [
            'status' => true,
            'message' => 'Chat retrieved successfully',
            'data' => [
                'chat_id' => $chat->id,
                'firebase_chat_id' => $chat->firebase_chat_id,
                'owner_name' => ($owner->profile->first_name ?? '') . ' ' . ($owner->profile->last_name ?? ''),
                'owner_image' => $owner->profile->personal_image ?? null,
            ],
            'code' => 201
        ];
    }
    public function inbox(int $userId)
    {

        $chats = Chat::where('tenant_id', $userId)
            ->orWhere('owner_id', $userId)
            ->with(['tenant.profile', 'owner.profile', 'apartment'])
            ->get();

        return $chats->map(function ($chat) use ($userId) {

            $isTenant = $chat->tenant_id === $userId;

            $otherUser = $isTenant
                ? $chat->owner
                : $chat->tenant;

            return [
                'chat_id' => $chat->id,
                'firebase_chat_id' => $chat->firebase_chat_id,
                'apartment_id' => $chat->apartment_id,
                'apartment_title' => $chat->apartment->title ?? null,

                'other_user_info' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->profile?->first_name . ' ' . $otherUser->profile?->last_name,
                    'image' => $otherUser->profile?->personal_image,
                    'role' => $isTenant ? 'owner' : 'tenant',
                ],
                'code' => 200
            ];
        });
    }
    
}
