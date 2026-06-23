<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Renders every API exception as a consistent JSON envelope:
 *   { "error": { "status": 404, "code": "not_found", "message": "..." } }
 */
final class ApiExceptionRenderer
{
    public static function render(Throwable $e): JsonResponse
    {
        $status = self::statusFor($e);
        $code = self::codeFor($status);

        $error = [
            'status' => $status,
            'code' => $code,
            'message' => self::messageFor($e, $status),
        ];

        if ($e instanceof ValidationException) {
            $error['fields'] = $e->errors();
        }

        return response()->json(['error' => $error], $status);
    }

    private static function statusFor(Throwable $e): int
    {
        return match (true) {
            $e instanceof ValidationException => 422,
            $e instanceof ModelNotFoundException => 404,
            $e instanceof AuthenticationException => 401,
            $e instanceof HttpExceptionInterface => $e->getStatusCode(),
            default => 500,
        };
    }

    private static function codeFor(int $status): string
    {
        return match ($status) {
            400 => 'bad_request',
            401 => 'unauthenticated',
            403 => 'forbidden',
            404 => 'not_found',
            405 => 'method_not_allowed',
            422 => 'validation_failed',
            429 => 'rate_limited',
            default => $status >= 500 ? 'server_error' : 'error',
        };
    }

    private static function messageFor(Throwable $e, int $status): string
    {
        if ($status === 404) {
            return 'The requested resource was not found.';
        }

        if ($status >= 500 && ! config('app.debug')) {
            return 'An unexpected server error occurred.';
        }

        return $e->getMessage() !== '' ? $e->getMessage() : 'An error occurred.';
    }
}
