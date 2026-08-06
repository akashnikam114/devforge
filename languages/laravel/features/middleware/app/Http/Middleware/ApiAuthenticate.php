<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next, $guard)
    {
        auth()->shouldUse($guard);

        try {
            if (!$user = auth($guard)->authenticate()) {
                return response()->json([
                    'status' => false,
                    'message' => 'User account not found or deactivated.'
                ], 404);
            }
        } catch (JWTException $e) {
            throw $e;
        }

        return $next($request);
    }
}
