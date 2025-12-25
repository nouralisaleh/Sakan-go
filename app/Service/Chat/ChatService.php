<?php

namespace App\Service\Chat;

use App\Models\Apartment;
use Illuminate\Support\Str;
use App\Models\Chat;

class ChatService
{
    public function startChat(int $tenantId, int $apartmentId): Chat
    {
        $apartment = Apartment::with('owner')->findOrFail($apartmentId);
        $ownerId = $apartment->owner_id;
        $chat = Chat::where('tenant_id', $tenantId)
            ->where('owner_id', $ownerId)
            ->where('apartment_id', $apartmentId)
            ->first();
        if ($tenantId === $ownerId) {
            throw new \Exception('You cannot chat with yourself');
        }
        if ($chat) {
            return
                $chat;
        }
        return Chat::create([
            'tenant_id' => $tenantId,
            'owner_id' => $ownerId,
            'apartment_id' => $apartmentId,
            'firebase_chat_id' => Str::uuid()->toString(),
        ]);
    }
    public function inbox(int $userId)
    {

    $chats = Chat::where('tenant_id', $userId)
        ->orWhere('owner_id', $userId)
        ->with(['tenant.profile', 'owner.profile', 'apartment'])
        ->touch()
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

            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->profile->first_name . ' ' . $otherUser->profile->last_name,
                'image' => $otherUser->profile->personal_image,
                'role' => $isTenant ? 'owner' : 'tenant',
            ],
        ];
    });
    }

}
