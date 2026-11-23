<?php
namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->guest(route('login'))
                ->with('warning', 'يرجى تسجيل الدخول بحساب مسؤول للوصول إلى لوحة الإدارة.');
        }

        if (!auth()->user()->isAdmin()) {
            AuditService::log('security_unauthorized_admin_access', auth()->user(), null, [
                'user_id' => auth()->id(),
                'role' => auth()->user()->role,
                'email' => auth()->user()->email,
                'attempted_url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            abort(403, 'غير مصرح لك بالوصول إلى لوحة الإدارة المحمية.');
        }

        return $next($request);
    }
}
