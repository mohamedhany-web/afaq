<!DOCTYPE html>
<html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>بوابة المطور</title>
<script src="https://cdn.tailwindcss.com"></script>
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
<body class="min-h-screen flex items-center justify-center bg-gray-50 font-[Tajawal] p-4">
<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border">
    <h1 class="text-2xl font-extrabold text-center mb-2">بوابة المطور العقاري</h1>
    <p class="text-center text-sm text-gray-500 mb-6">{{ \App\Helpers\SettingsHelper::getCompanyName() }}</p>
    <form method="POST" action="{{ route('developer.login.submit') }}" class="space-y-4">@csrf
        <div><label class="text-xs font-bold text-gray-500">البريد</label><input type="email" name="email" required class="w-full mt-1 border-2 rounded-xl px-4 py-3 text-sm" value="{{ old('email') }}"></div>
        <div><label class="text-xs font-bold text-gray-500">كلمة المرور</label><input type="password" name="password" required class="w-full mt-1 border-2 rounded-xl px-4 py-3 text-sm"></div>
        @error('email')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        <button type="submit" class="w-full py-3 rounded-xl text-white font-bold" style="background:{{ $themeColor }}">دخول</button>
    </form>
</div></body></html>
