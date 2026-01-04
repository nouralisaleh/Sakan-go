<?php

namespace App\Service\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;


class DashbourdService
{
    public function index(Request $request)
    {
        $query = User::with('profile');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->get();

        return UserResource::collection($users);
    }
    public function showUser(User $user)
    {
        $user->load('profile');

        return new UserResource($user);
    }

}
