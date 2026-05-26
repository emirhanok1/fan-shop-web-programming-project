<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\CheckUserActive::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->redirectUsersTo(fn () => auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if (app()->environment('production') && ($request->is('api/*') || $request->ajax())) {
                \Illuminate\Support\Facades\Log::error('API/Ajax Error: ' . $e->getMessage(), [
                    'url' => $request->url(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'message' => 'Sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.'
                ], 500);
            }
        });
    })->create();
