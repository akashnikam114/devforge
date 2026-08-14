<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminActivityLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($user = $request->user()) {
            $properties = [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status_code' => $response->getStatusCode(),
            ];

            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $properties['request_data'] = $request->except(['password', 'password_confirmation', '_token']);
            }

            $event = strtolower($request->method());

            activity('admin-action')
                ->event($event)
                ->causedBy($user)
                ->withProperties($properties)
                ->log("Admin performed a {$request->method()} request to {$request->path()}");
        }

        return $response;
    }
}
