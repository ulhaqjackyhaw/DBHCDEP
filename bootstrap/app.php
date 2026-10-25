<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'large.import' => App\Http\Middleware\HandleLargeImport::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF Token Mismatch (Session Expired)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session Anda telah berakhir. Silakan login kembali.',
                    'expired' => true
                ], 419);
            }

            // Redirect ke login dengan pesan
            return redirect()->route('login')
                ->with('error', 'Session Anda telah berakhir. Silakan login kembali.')
                ->with('session_expired', true);
        });
    })->create();
