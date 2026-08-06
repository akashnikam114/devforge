<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    public function handle(Request $request, Closure $next)
    {
        $sanitized = $this->sanitize($request->all());
        $request->merge($sanitized);

        return $next($request);
    }

    protected function sanitize(array $input)
    {
        $sanitized = [];
    
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = filter_var($value, FILTER_SANITIZE_STRING);
                $sanitized[$key] = html_entity_decode($sanitized[$key], ENT_QUOTES | ENT_HTML5);
            } elseif (is_numeric($value)) {
                $sanitized[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            } else {
                $sanitized[$key] = $value;
            }
        }
    
        return $sanitized;
    }    
}
