<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and apply strict security headers and HTTPS redirection.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Force HTTPS redirect in production or when configured, unless running in local test environment
        if (!app()->environment('testing')) {
            $isSecure = $request->secure() || $request->header('X-Forwarded-Proto') === 'https';
            $forceHttps = config('app.env') === 'production' || env('FORCE_HTTPS', false);

            if ($forceHttps && !$isSecure) {
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        $response = $next($request);

        // 2. Set industry-standard HTTP security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        // Only enforce HSTS when on HTTPS
        if ($request->secure() || $request->header('X-Forwarded-Proto') === 'https' || config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
