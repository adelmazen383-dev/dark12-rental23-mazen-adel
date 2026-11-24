<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude polling/push API routes from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'investor/api/unread-alerts',
            'investor/api/push-subscribe',
            'investor/api/test-push',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'investor' => \App\Http\Middleware\InvestorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// On Vercel serverless environment, use /tmp/storage so all file operations succeed
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
