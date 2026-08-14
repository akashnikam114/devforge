<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiRateLimitGuard
{
    public function handle(Request $request, Closure $next)
    {
        $limit = (int) env('API_RATE_LIMIT_PER_MINUTE', 60);
        $blockDuration = (int) env('API_RATE_LIMIT_BLOCK_DURATION_SECONDS', 3600);
        $decaySeconds = 60;
        $ip = $request->ip();
        $uri = $request->path();
        $key = "api_rate:{$ip}:{$uri}";
        $blockKey = "api_rate_block:{$ip}:{$uri}";

        if (Cache::has($blockKey)) {
            Log::warning("API rate limit blocked {$ip} for {$uri}.");

            return response()->json([
                'status' => false,
                'message' => 'Too many requests. Please try again later.',
            ], 429);
        }

        $attempts = Cache::get($key, 0);

        if ($attempts >= $limit) {
            Cache::put($blockKey, true, now()->addSeconds($blockDuration));
            Log::warning("API rate limit exceeded by {$ip} for {$uri}. Attempts: {$attempts}.");

            return response()->json([
                'status' => false,
                'message' => 'Too many requests. Please try again later.',
            ], 429);
        }

        Cache::put($key, $attempts + 1, now()->addSeconds($decaySeconds));

        return $next($request);
    }
}
