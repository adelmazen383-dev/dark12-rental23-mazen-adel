<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enforce HTTPS URLs when running in production, behind reverse proxies, or when configured
        if (config('app.env') === 'production' || request()->header('X-Forwarded-Proto') === 'https' || env('FORCE_HTTPS', false)) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Auto-heal public storage symbolic link if broken or a physical directory on server
        try {
            $publicStorage = public_path('storage');
            $targetStorage = storage_path('app/public');

            if (is_dir($targetStorage) && PHP_OS_FAMILY !== 'Windows') {
                if (is_dir($publicStorage) && !is_link($publicStorage)) {
                    @shell_exec('cp -rn ' . escapeshellarg($publicStorage) . '/* ' . escapeshellarg($targetStorage) . '/ 2>/dev/null');
                    @rename($publicStorage, $publicStorage . '_legacy_' . date('Ymd'));
                    @symlink($targetStorage, $publicStorage);
                } elseif (!file_exists($publicStorage)) {
                    @symlink($targetStorage, $publicStorage);
                }
            }
        } catch (\Throwable) {
            // Suppress if restricted by permissions
        }
    }
}
