<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            throw new AuthenticationException();
        }

        if ($request->user()->role !== $role) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Acesso negado. Permissão insuficiente.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
