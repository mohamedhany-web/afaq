<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DeveloperAuthController extends Controller
{
    public function showLogin()
    {
        return view('developer-auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('developer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $account = Auth::guard('developer')->user();
            if (!$account->is_active || !$account->developer?->portal_enabled) {
                Auth::guard('developer')->logout();
                throw ValidationException::withMessages([
                    'email' => 'هذا الحساب غير مفعل أو البوابة موقوفة',
                ]);
            }

            $account->update(['last_login_at' => now()]);

            return redirect()->route('developer.dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'بيانات الدخول غير صحيحة',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('developer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('developer.login');
    }
}
