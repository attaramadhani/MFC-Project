<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

if (getenv('VERCEL') || isset($_SERVER['VERCEL'])) {
    header('Content-Type: text/plain');
    echo "VERCEL environment detected!\n";
    echo "getenv('APP_PACKAGES_CACHE'): " . var_export(getenv('APP_PACKAGES_CACHE'), true) . "\n";
    echo "\$_ENV['APP_PACKAGES_CACHE']: " . var_export($_ENV['APP_PACKAGES_CACHE'] ?? null, true) . "\n";
    echo "\$_SERVER['APP_PACKAGES_CACHE']: " . var_export($_SERVER['APP_PACKAGES_CACHE'] ?? null, true) . "\n";
    echo "putenv value check: " . var_export(getenv('APP_PACKAGES_CACHE'), true) . "\n";
    exit;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e) {
            // Check if we are running in Vercel to intercept and display the original exception
            if (getenv('VERCEL') || isset($_SERVER['VERCEL'])) {
                echo "<h1>Original Exception Caught in bootstrap/app.php</h1>";
                echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                exit;
            }
        });
    })->create();