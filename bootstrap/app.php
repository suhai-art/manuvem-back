<?php

use App\Http\Middleware\FormatApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        /* web: __DIR__.'/../routes/web.php', */
        api: __DIR__ . '/../routes/tenant.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn() => null);
        $middleware->append(
            FormatApiResponse::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('*'),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            $status = FormatApiResponse::resolveExceptionStatus($e);

            return response()->json(
                FormatApiResponse::resolveExceptionPayload($e, $status),
                $status
            );
        });
    })
    ->create();
