@extends('layouts.app')

@section('page-title', 'تعديل حساب عميل')

@section('content')
<div class="w-full max-w-full">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">تعديل حساب عميل</h1>
            <p class="text-gray-600 text-sm sm:text-base">تعديل بيانات الدخول وتغيير كلمة المرور (اختياري)</p>
        </div>
        <a href="{{ route('client-accounts.index') }}" class="bg-gray-600 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition-all duration-200 text-sm">العودة</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('client-accounts.update', $clientAccount) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                @include('partials.client-search-select', ['required' => true, 'value' => old('client_id', $clientAccount->client_id), 'label' => $clientAccount->client ? \App\Http\Controllers\ClientSearchController::formatLabel($clientAccount->client) : null, 'inputClass' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500', 'crmScope' => false])
                @error('client_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">اسم الحساب</label>
                    <input type="text" name="name" value="{{ old('name', $clientAccount->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email', $clientAccount->email) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">كلمة مرور جديدة (اختياري)</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">صلاحية البوابة</label>
                <select name="portal_role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('portal_role') border-red-500 @enderror">
                    <option value="owner" {{ old('portal_role', $clientAccount->portal_role ?? 'owner') === 'owner' ? 'selected' : '' }}>مالك — كامل الوصول</option>
                    <option value="billing" {{ old('portal_role', $clientAccount->portal_role ?? 'owner') === 'billing' ? 'selected' : '' }}>محاسبة — الفواتير والمستندات المالية</option>
                    <option value="technical" {{ old('portal_role', $clientAccount->portal_role ?? 'owner') === 'technical' ? 'selected' : '' }}>تقني — البلاغات والاجتماعات والتقويم</option>
                </select>
                @error('portal_role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $clientAccount->is_active) ? 'checked' : '' }} class="rounded border-gray-300">
                <label class="text-sm text-gray-700">الحساب مفعل</label>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 text-sm font-semibold">حفظ</button>
            </div>
        </form>
    </div>
</div>
@endsection

