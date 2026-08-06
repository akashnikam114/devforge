<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use \Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return $this->handleApiExceptions($e);
            }
        });
    }

    private function handleApiExceptions(Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return $this->apiResponse('The given data was invalid.', 422, $e->errors());
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->apiResponse('The requested resource or endpoint was not found.', 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->apiResponse('The HTTP method is not allowed for this endpoint.', 405);
        }

        if ($e instanceof AuthenticationException || $e instanceof UnauthorizedHttpException) {
            return $this->apiResponse('Authorization token is required or invalid.', 401);
        }

        if ($e instanceof TokenExpiredException || $e instanceof TokenBlacklistedException) {
            return $this->apiResponse('Your session has expired. Please sign in again.', 401);
        }

        if ($e instanceof TokenInvalidException ) {
            return $this->apiResponse('Authorization token is invalid.', 401);
        }

        if ($e instanceof JWTException) {
            return $this->apiResponse('An error occurred while processing the authorization token.', 401);
        }

        return $this->apiResponse('An unexpected server error occurred.', 500, $e->getMessage());
    }

    private function apiResponse($message, $code, $error = null)
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if (!is_null($error)) {
            $response['error'] = $error;
        }

        return response()->json($response, $code);
    }
}
