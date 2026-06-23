<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>بوابة المطور — {{ \App\Helpers\SettingsHelper::getCompanyName() }}</title>

    @php
        $faviconUrl = \App\Helpers\SettingsHelper::getFaviconUrl();
        $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
        $logoUrl = \App\Helpers\SettingsHelper::getLogoUrl();
        $companyName = \App\Helpers\SettingsHelper::getCompanyName();
    @endphp

    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl ?: '/favicon.ico' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --brand: {{ $themeColor }}; }
        body { font-family: 'Tajawal', sans-serif; }
        .btn-brand { background: linear-gradient(135deg, var(--brand) 0%, color-mix(in srgb, var(--brand) 85%, #000) 100%); }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4 font-tajawal">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6 shadow-lg"
                 style="background: linear-gradient(135deg, var(--brand) 0%, color-mix(in srgb, var(--brand) 85%, #000) 100%);">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="w-full h-full object-contain rounded-xl p-2">
                @else
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                @endif
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">بوابة المطور العقاري</h1>
            <p class="text-gray-600">شركاء {{ $companyName }}</p>
            <div class="w-20 h-1 bg-gray-200 rounded-full mx-auto mt-4"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-7 sm:p-8 border border-gray-100">
            <div class="text-center mb-7">
                <h2 class="text-xl font-extrabold text-gray-900">تسجيل الدخول</h2>
                <p class="mt-2 text-sm text-gray-500">أدخل بيانات حساب المطور للوصول إلى لوحتك</p>
            </div>

            <form method="POST" action="{{ route('developer.login.submit') }}" class="space-y-5">
                @csrf
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition focus:ring-2 focus:border-transparent @error('email') border-red-500 bg-red-50 @enderror"
                           style="--tw-ring-color: {{ $themeColor }}"
                           placeholder="developer@company.com">
                    @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">كلمة المرور</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm transition focus:ring-2 @error('password') border-red-500 bg-red-50 @enderror"
                           placeholder="••••••••">
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                    تذكرني
                </label>
                <button type="submit" class="w-full text-white font-extrabold py-3.5 px-6 rounded-xl btn-brand shadow-lg hover:shadow-xl transition">
                    دخول بوابة المطور
                </button>
            </form>
        </div>

        <p class="text-center mt-7 text-xs text-gray-500">
            نظام {{ $companyName }} — بوابة المطورين المعتمدين
        </p>
    </div>
</body>
</html>
