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

            // 1b. Canonical host 301 redirect (consolidate thiqa.me/app -> https://darak.thiqa.me)
            // so Google Search indexes ONLY darak.thiqa.me (never thiqa.me/app or Vercel).
            $canonicalUrl = rtrim((string) config('app.url', ''), '/');
            $canonicalHost = strtolower((string) parse_url($canonicalUrl, PHP_URL_HOST));
            $requestHost = strtolower((string) $request->getHost());

            if (
                $canonicalHost !== ''
                && str_starts_with($canonicalUrl, 'https://')
                && !in_array($requestHost, [$canonicalHost, 'localhost', '127.0.0.1'], true)
            ) {
                $isNonRedirectPath = $request->is(
                    'sw.js',
                    'manifest.json',
                    'manifest-client.json',
                    'csrf-token',
                    'api/*',
                    'client/api/*',
                    'investor/api/*',
                    'storage/*',
                    'google*.html'
                );

                if (in_array($request->method(), ['GET', 'HEAD'], true) && !$isNonRedirectPath) {
                    $pathInfo = $request->getPathInfo();
                    $target = $canonicalUrl . ($pathInfo === '/' ? '' : $pathInfo);
                    if ($qs = $request->getQueryString()) {
                        $target .= '?' . $qs;
                    }
                    return redirect()->away($target, 301);
                }
            }
        }

        $response = $next($request);

        // Prevent search engines from indexing any non-canonical host responses (e.g. thiqa.me/app)
        if (!app()->environment('testing')) {
            $canonicalHost = strtolower((string) parse_url((string) config('app.url', ''), PHP_URL_HOST));
            $requestHost = strtolower((string) $request->getHost());
            if ($canonicalHost !== '' && !in_array($requestHost, [$canonicalHost, 'localhost', '127.0.0.1'], true)) {
                $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            }
        }

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

        // Clean up any legacy 'dark_rental_session' cookies on older paths (/app, /app/, /)
        // when the application has rotated to a clean session cookie name.
        $activeCookie = (string) config('session.cookie', 'dark_rental_session');
        if ($activeCookie !== 'dark_rental_session' && $request->cookies->has('dark_rental_session')) {
            $isHttps = $request->secure() || $request->header('X-Forwarded-Proto') === 'https' || config('app.env') === 'production';
            foreach (['/app', '/app/', '/'] as $legacyPath) {
                $response->headers->setCookie(
                    cookie('dark_rental_session', '', -2628000, $legacyPath, null, $isHttps, true, false, 'lax')
                );
            }
        }

        return $response;
    }
}
