<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class NormalizeApiResponse
{
    public function handle($request, Closure $next)
    {
        $this->normalizeIncomingRequest($request);

        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $response->setData($this->convertKeys($data, 'camel'));
        }

        return $response;
    }

    private function normalizeIncomingRequest($request)
    {
        if ($request->query->count() > 0) {
            $request->query->replace($this->convertKeys($request->query->all(), 'snake'));
        }

        if ($request->isJson()) {
            $payload = $this->convertKeys($request->json()->all(), 'snake');
            $request->json()->replace($payload);
            $request->request->replace($payload);
            return;
        }

        if ($request->request->count() > 0) {
            $request->request->replace($this->convertKeys($request->request->all(), 'snake'));
        }
    }

    private function convertKeys(array $array, string $case)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = is_string($key)
                ? ($case === 'snake' ? Str::snake($key) : Str::camel($key))
                : $key;

            $result[$newKey] = is_array($value) ? $this->convertKeys($value, $case) : $value;
        }

        return $result;
    }
}
