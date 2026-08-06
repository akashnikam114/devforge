<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class NormalizeApiResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $response->setData($this->convertSnakeToCamelCase($data));
        }

        return $response;
    }

    private function convertSnakeToCamelCase(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = preg_replace_callback('/_([a-z])/', function ($matches) {
                return strtoupper($matches[1]);
            }, $key);

            $result[$newKey] = is_array($value) ? $this->convertSnakeToCamelCase($value) : $value;
        }
        return $result;
    }
}