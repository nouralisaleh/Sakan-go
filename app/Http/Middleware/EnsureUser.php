<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('user_api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('auth.unauthenticated'),
            ], 401);
        }

        if ($user->role !== 'owner') {
            return response()->json([
                'status' => false,
                'message' => __('apartments.only_owner_allowed'),
            ],403 );
        }

        return $next($request);    }
}
