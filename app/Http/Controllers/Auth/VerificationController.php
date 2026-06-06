<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerificationRequest;
use App\Models\VerificationCode;
use App\Models\LoginActivityLog;
use App\Services\EmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Show the verification code entry form.
     */
    public function show(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (config('app.skip_email_verification')) {
            return redirect()->intended(Auth::user()->homeRoute());
        }

        if (!session('verification_pending')) {
            return redirect()->route('login');
        }
        
        return view('auth.verify-code');
    }

    /**
     * Verify the code entered by the user.
     */
    public function verify(VerificationRequest $request): RedirectResponse
    {
        if (!Auth::check() || !session('verification_pending')) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $code = $request->input('code');

        // Rate limiting check
        $key = 'verification:' . $user->id . ':' . md5($request->ip());
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            Log::warning('Rate limit exceeded for verification code', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'seconds_remaining' => $seconds,
            ]);

            return redirect()->back()
                ->withErrors(['code' => 'تم تجاوز عدد المحاولات المسموح به. يرجى المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.']);
        }

        // البحث عن الكود الصحيح باستخدام Query Builder الآمن
        $verificationCode = VerificationCode::where('user_id', $user->id)
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verificationCode) {
            // Increment rate limiter on failed attempt
            RateLimiter::hit($key);

            Log::warning('Failed verification code attempt', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'code_attempted' => substr($code, 0, 2) . '****', // Partial code for logging
            ]);

            // Log security event
            Log::channel('security')->warning('failed_verification_attempt', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return redirect()->back()
                ->withErrors(['code' => 'الكود غير صحيح أو منتهي الصلاحية. يرجى المحاولة مرة أخرى.'])
                ->withInput();
        }

        // تم التحقق بنجاح - حذف حالة التحقق من الجلسة
        $verificationCode->markAsUsed();
        
        // Clear rate limiter on success
        RateLimiter::clear($key);
        
        // تسجيل عملية التحقق من الكود
        LoginActivityLog::logActivity(
            $user->id,
            'verification_code_verified',
            $code,
            null,
            'success',
            'تم التحقق من الكود بنجاح'
        );
        
        // Regenerate session ID for security
        $request->session()->regenerate();
        
        $request->session()->forget('verification_pending');
        $request->session()->put('verified', true);

        return redirect()->intended($user->homeRoute());
    }

    /**
     * Resend verification code.
     */
    public function resend(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            Log::warning('Resend verification code: User not authenticated');
            return redirect()->route('login');
        }

        $user = Auth::user();
        $key = 'resend_verification:' . $user->id . ':' . md5($request->ip());
        
        // Rate limiting: 3 resends per 5 minutes
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            
            Log::warning('Rate limit exceeded for resend verification code', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'seconds_remaining' => $seconds,
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'تم تجاوز عدد محاولات إعادة الإرسال المسموح بها. يرجى المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.']);
        }

        Log::info('Resend verification code requested', [
            'user_id' => $user->id,
            'has_verification_pending' => session('verification_pending'),
            'route' => $request->route()->getName()
        ]);

        // السماح بإعادة الإرسال حتى لو لم يكن verification_pending موجود
        // لأن المستخدم قد يحتاج لإعادة إرسال الكود

        try {
            $user = Auth::user();
            
            // تحميل علاقة الموظف إن وجدت
            $user->load('employee');
            
            \Log::info('Generating new verification code', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'employee_email' => $user->employee->email ?? null
            ]);
            
            // توليد كود جديد
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
                \Log::error('Resend verification code: No valid email found for user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email ?? 'null',
                    'employee_email' => $user->employee->email ?? 'null'
                ]);
                return redirect()->back()
                    ->withErrors(['error' => 'لم يتم العثور على بريد إلكتروني صحيح مرتبط بحسابك. يرجى التحقق من بياناتك.']);
            }
            
            \Log::info('Selected email for resend', [
                'selected_email' => $email,
                'user_email' => $user->email ?? 'null',
                'employee_email' => $user->employee->email ?? 'null'
            ]);
            
            // الحصول على اسم المستخدم - مع معالجة شاملة
            $userName = 'المستخدم'; // قيمة افتراضية
            if (!empty($user->name) && trim($user->name) !== '') {
                $userName = trim($user->name);
            } elseif ($user->employee) {
                $firstName = $user->employee->first_name ?? '';
                $lastName = $user->employee->last_name ?? '';
                $fullName = trim($firstName . ' ' . $lastName);
                if (!empty($fullName)) {
                    $userName = $fullName;
                }
            }
            
            // التأكد من أن userName ليس فارغاً
            if (empty($userName) || trim($userName) === '') {
                $userName = 'المستخدم';
            }
            
            \Log::info('Sending verification code', [
                'email' => $email,
                'code' => $verificationCode->code,
                'userName' => $userName
            ]);
            
            // إرسال الكود عبر البريد الإلكتروني باستخدام PHPMailer
            $emailService = new EmailService();
            $sent = $emailService->sendVerificationCode($email, $verificationCode->code, $userName);
            
            // تسجيل عملية إعادة إرسال الكود
            LoginActivityLog::logActivity(
                $user->id,
                'verification_code_resend',
                $verificationCode->code,
                $email,
                $sent ? 'success' : 'failed',
                $sent ? 'تم إعادة إرسال كود التحقق بنجاح' : 'فشل إعادة إرسال كود التحقق'
            );
            
            if ($sent) {
                // Clear rate limiter on success
                RateLimiter::clear($key);
                
                Log::info('Verification code sent successfully', ['email' => $email]);
                // إعادة تعيين حالة التحقق في الجلسة
                $request->session()->put('verification_pending', true);
                return redirect()->back()
                    ->with('success', 'تم إرسال كود التحقق الجديد إلى بريدك الإلكتروني.');
            } else {
                // Increment rate limiter on failure
                RateLimiter::hit($key);
                
                Log::error('Failed to send verification code', ['email' => $email]);
                return redirect()->back()
                    ->withErrors(['error' => 'حدث خطأ أثناء إرسال الكود. يرجى المحاولة مرة أخرى.']);
            }
        } catch (\Exception $e) {
            // Increment rate limiter on exception
            RateLimiter::hit($key);
            
            Log::error('Error in resend verification code: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ أثناء إرسال الكود. يرجى المحاولة مرة أخرى.']);
        }
    }
}

