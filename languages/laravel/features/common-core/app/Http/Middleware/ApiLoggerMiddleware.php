<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class ApiLoggerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $hiddenFields = ['password', 'password_confirmation', 'old_password', 'token', 'auth_token'];

        $logData = [
            'timestamp' => now()->toDateTimeString(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'headers' => Arr::except($request->header(), ['authorization', 'php-auth-pw']),
            'request' => $request->except($hiddenFields),
            'status' => $response->getStatusCode(),
            'response' => json_decode($response->getContent(), true),
        ];

        $prettyLog = json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        Log::channel('api_logs')->info("API_LOG\n" . $prettyLog . "\n" . str_repeat('-', 50));

        return $response;
    }
}
