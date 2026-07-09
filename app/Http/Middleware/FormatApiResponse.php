<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class FormatApiResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        return $response instanceof JsonResponse
            ? $this->format($response)
            : $response;
    }

    public static function buildPayload(array $original, int $status): array
    {
        if (array_key_exists('success', $original)) {
            return $original;
        }

        return $status >= 200 && $status < 300
            ? self::buildSuccessPayload($original)
            : self::buildErrorPayload($original);
    }

    public static function resolveExceptionStatus(Throwable $e): int
    {
        return match (true) {
            $e instanceof AuthenticationException => Response::HTTP_UNAUTHORIZED,
            $e instanceof ValidationException => Response::HTTP_UNPROCESSABLE_ENTITY,
            $e instanceof HttpExceptionInterface => $e->getStatusCode(),
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };
    }

    public static function resolveExceptionPayload(Throwable $e, int $status): array
    {
        $payload = [
            'message' => $status === Response::HTTP_INTERNAL_SERVER_ERROR
                ? 'Ocorreu um erro.'
                : $e->getMessage(),
        ];

        if ($e instanceof ValidationException) {
            $payload['errors'] = $e->errors();
        }

        return self::buildErrorPayload($payload);
    }

    private function format(JsonResponse $response): JsonResponse
    {
        $payload = self::buildPayload($response->getData(true), $response->getStatusCode());

        return $response->setData($payload);
    }

    private static function buildSuccessPayload(array $original): array
    {
        if (self::isPaginated($original)) {
            return [
                'success' => true,
                'data' => $original['data'],
                'meta' => [
                    'current_page' => $original['current_page'],
                    'per_page' => $original['per_page'],
                    'total' => $original['total'],
                    'last_page' => $original['last_page'],
                ],
            ];
        }

        return [
            'success' => true,
            'data' => $original,
        ];
    }

    private static function buildErrorPayload(array $original): array
    {
        $payload = [
            'success' => false,
            'message' => $original['message'] ?? 'Ocorreu um erro.',
        ];

        if (isset($original['errors'])) {
            $payload['errors'] = $original['errors'];
        }

        return $payload;
    }

    private static function isPaginated(array $data): bool
    {
        return array_key_exists('current_page', $data)
            && array_key_exists('data', $data);
    }
}
