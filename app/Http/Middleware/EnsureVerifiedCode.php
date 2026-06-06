<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerifiedCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.skip_email_verification')) {
            return $next($request);
        }

        // التحقق من أن المستخدم مسجل الدخول
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // السماح بالوصول إلى صفحة التحقق من الكود وإعادة الإرسال
        $routeName = $request->route()->getName();
        if (in_array($routeName, ['verification.show', 'verification.verify', 'verification.resend'])) {
            return $next($request);
        }

        // إذا كان المستخدم لم يتحقق بعد بالكود
        if (session('verification_pending') && !session('verified')) {
            return redirect()->route('verification.show');
        }

        return $next($request);
    }
}
