<?php

/**
 * Vercel Serverless Bootstrap
 * 
 * This service provider adjusts Laravel's paths for Vercel's
 * read-only filesystem. Only /tmp is writable in serverless.
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class VercelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Only apply in production/serverless (Vercel sets VERCEL=1)
        if (!isset($_ENV['VERCEL']) && !getenv('VERCEL')) {
            return;
        }

        \Illuminate\Support\Facades\URL::forceScheme('https');

        // Redirect writable paths to /tmp
        $this->app->useStoragePath('/tmp/storage');
    }

    public function boot(): void
    {
        if (!isset($_ENV['VERCEL']) && !getenv('VERCEL')) {
            return;
        }

        \Illuminate\Support\Facades\URL::forceScheme('https');

        // Ensure view compiled path exists
        $viewPath = '/tmp/storage/framework/views';
        if (!is_dir($viewPath)) {
            @mkdir($viewPath, 0755, true);
        }

        // Ensure cache path exists
        $cachePath = '/tmp/storage/framework/cache/data';
        if (!is_dir($cachePath)) {
            @mkdir($cachePath, 0755, true);
        }

        // Ensure logs path
        $logPath = '/tmp/storage/logs';
        if (!is_dir($logPath)) {
            @mkdir($logPath, 0755, true);
        }

        // Ensure sessions path
        $sessionPath = '/tmp/storage/framework/sessions';
        if (!is_dir($sessionPath)) {
            @mkdir($sessionPath, 0755, true);
        }
    }
}
