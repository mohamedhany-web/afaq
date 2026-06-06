<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\VerificationCode;
use App\Models\User;
use App\Models\LoginActivityLog;
use App\Services\EmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('welcome');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if (config('app.skip_email_verification')) {
            LoginActivityLog::logActivity(
                $user->id,
                'login',
                null,
                null,
                'success',
                'تم تسجيل الدخول بنجاح (بدون تحقق بالبريد — وضع التطوير)'
            );

            $request->session()->forget('verification_pending');
            $request->session()->put('verified', true);
            $request->session()->regenerate();

            return redirect()->intended($user->homeRoute());
        }
        
        // توليد كود التحقق
        $verificationCode = VerificationCode::createForUser($user->id, 10);
        
        // الحصول على البريد الإلكتروني - الأولوية لبريد المستخدم، ثم بريد الموظف
        $email = null;
        
        // أولاً: التحقق من بريد المستخدم
        if (!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $email = $user->email;
        }
        
        // ثانياً: إذا لم يكن هناك بريد للمستخدم، استخدم بريد الموظف
        if (empty($email) && $user->employee && !empty($user->employee->email) && filter_var($user->employee->email, FILTER_VALIDATE_EMAIL)) {
            $email = $user->employee->email;
        }
        
        // التأكد من وجود بريد إلكتروني صحيح
        if (empty($email)) {
            \Log::error('Login: No valid email found for user', [
                'user_id' => $user->id,
                'user_email' => $user->email ?? 'null',
                'employee_email' => $user->employee->email ?? 'null'
            ]);
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'لم يتم العثور على بريد إلكتروني صحيح مرتبط بحسابك. يرجى التواصل مع المدير.']);
        }
        
        // الحصول على اسم المستخدم
        $userName = 'المستخدم'; // قيمة افتراضية
        if (!empty($user->name)) {
            $userName = $user->name;
        } elseif ($user->employee && !empty($user->employee->first_name)) {
            $userName = trim($user->employee->first_name . ' ' . ($user->employee->last_name ?? ''));
            if (empty($userName)) {
                $userName = 'المستخدم';
            }
        }
        
        // إرسال الكود عبر البريد الإلكتروني باستخدام PHPMailer
        $emailService = new EmailService();
        $sent = $emailService->sendVerificationCode($email, $verificationCode->code, $userName);
        
        // تسجيل عملية تسجيل الدخول وإرسال الكود
        LoginActivityLog::logActivity(
            $user->id,
            'login',
            null,
            null,
            'success',
            'تم تسجيل الدخول بنجاح'
        );
        
        LoginActivityLog::logActivity(
            $user->id,
            'verification_code_sent',
            $verificationCode->code,
            $email,
            $sent ? 'success' : 'failed',
            $sent ? 'تم إرسال كود التحقق بنجاح' : 'فشل إرسال كود التحقق'
        );
        
        // تسجيل حالة عدم التحقق بالكود في الجلسة
        $request->session()->put('verification_pending', true);
        $request->session()->regenerate();

        return redirect()->route('verification.show');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // تسجيل عملية تسجيل الخروج
        if ($user) {
            LoginActivityLog::logActivity(
                $user->id,
                'logout',
                null,
                null,
                'success',
                'تم تسجيل الخروج بنجاح'
            );
        }
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
