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
            'client/api/push-subscribe',
            'client/api/test-push',
            'api/push-rotate',
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
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->isXmlHttpRequest() || $request->is('*/api/*') || $request->is('api/*')) {
                return response()->json([
                    'message' => 'انتهت صلاحية رمز الأمان (CSRF). تم تجديد الرمز، يرجى إعادة المحاولة.',
                    'csrf_token' => csrf_token(),
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation', 'mail_password']))
                ->with('warning', 'تم تحديث رمز أمان الصفحة تلقائياً للأمان. يرجى إعادة الضغط على الزر لإتمام طلبك بنجاح.');
        });
    })->create();

return $app;
