<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    
    ->withExceptions(function (Exceptions $exceptions): void {
        // 1. Pastikan semua ralat di bawah /api/ sentiasa dipulangkan sebagai JSON
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // 2. Ralat Validasi Input Borang (HTTP 422 Unprocessable Entity)
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ralat validasi input: Sila semak data yang dihantar.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // 3. Ralat Endpoint atau Rekod Tidak Dijumpai (HTTP 404 Not Found)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint atau data tidak dijumpai (404 Not Found).',
                ], 404);
            }
        });

        // 4. Ralat Dalaman Pelayan (HTTP 500 Internal Server Error)
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => app()->hasDebugModeEnabled() ? $e->getMessage() : 'Ralat dalaman pelayan (500 Server Error).',
                ], 500);
            }
        });
    })->create();
