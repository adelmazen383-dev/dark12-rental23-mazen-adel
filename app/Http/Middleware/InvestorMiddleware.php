<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InvestorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->guest(route('login'))
                ->with('warning', 'يرجى تسجيل الدخول بحساب مستثمر للوصول إلى لوحة التحكم.');
        }

        if (!auth()->user()->isInvestor()) {
            abort(403, 'غير مصرح لك بالوصول إلى لوحة تحكم المستثمرين.');
        }

        return $next($request);
    }
}
