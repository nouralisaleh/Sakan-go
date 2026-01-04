<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;



class OtpSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
  public function handle($request, Closure $next)
{
    $token = $request->bearerToken();

    if (!$token) {
        return response()->json([
            'message' => 'OTP token missing'
        ], 401);
    }

    $session = DB::table('otp_sessions')
        ->where('token', $token)
        ->first();

    if (!$session) {
        return response()->json([
            'message' => 'OTP session invalid'
        ], 401);
    }

    return $next($request);
}

}
