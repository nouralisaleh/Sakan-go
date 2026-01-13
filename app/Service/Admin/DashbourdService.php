<?php

namespace App\Service\Admin;

use App\Http\Resources\UserDashResource;
use App\Models\User;
use Illuminate\Http\Request;


class DashbourdService
{
public function index(Request $request)
{

    $query = User::with('ownerRequest');


    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }


    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }


    if ($request->filled('request_status')) {
        $query->whereHas('ownerRequests', function ($q) use ($request) {
            $q->where('request_status', $request->request_status);
        });
    }

    $users = $query->latest()->get();

    return [
        'data' => UserDashResource::collection($users),
        'code' => 200
    ];
}
    public function showUser(User $user)
    {
        $user->load('profile', 'ownerRequest');

        return [
            'data' => new UserDashResource($user),
        'code' => 200];
    }

}
