@extends('layouts.app')
@section('page-title', 'مستخدم جديد')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-tajawal focus:ring-2 focus:border-transparent';
@endphp

@include('crm.partials.page-header', [
    'title' => 'إضافة مستخدم',
    'subtitle' => 'إنشاء حساب دخول واختيار الدور المناسب للنظام',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>',
    'actionUrl' => route('users.index'),
    'actionLabel' => 'العودة للقائمة',
])

@if($errors->any())
<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">
    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
</div>
@endif

<form method="POST" action="{{ route('users.store') }}" class="space-y-6 font-tajawal" x-data="{ withEmployee: {{ old('create_employee') ? 'true' : 'false' }} }">
    @csrf

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold text-gray-900 mb-4">بيانات الحساب</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="{{ $label }}">الاسم الكامل *</label>
                <input name="name" value="{{ old('name') }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">البريد الإلكتروني *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="{{ $input }}" dir="ltr">
            </div>
            <div>
                <label class="{{ $label }}">كلمة المرور *</label>
                <input type="password" name="password" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">تأكيد كلمة المرور *</label>
                <input type="password" name="password_confirmation" required class="{{ $input }}">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold text-gray-900 mb-1">الدور في النظام *</h2>
        <p class="text-sm text-gray-500 mb-4">اختر دوراً واحداً يحدد صلاحيات المستخدم ومساحة عمله</p>
        @include('users.partials.role-picker', ['assignableRoles' => $assignableRoles, 'selected' => old('role')])
    </div>

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <label class="flex items-center gap-3 cursor-pointer mb-4">
            <input type="checkbox" name="create_employee" value="1" class="rounded border-gray-300" x-model="withEmployee" @checked(old('create_employee'))>
            <span class="font-bold text-gray-900">إنشاء سجل موظف مرتبط (حضور، إجازات، راتب)</span>
        </label>
        <div x-show="withEmployee" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="{{ $label }}">الاسم الأول *</label>
                <input name="first_name" value="{{ old('first_name') }}" class="{{ $input }}" :required="withEmployee">
            </div>
            <div>
                <label class="{{ $label }}">اسم العائلة *</label>
                <input name="last_name" value="{{ old('last_name') }}" class="{{ $input }}" :required="withEmployee">
            </div>
            <div>
                <label class="{{ $label }}">الهاتف *</label>
                <input name="phone" value="{{ old('phone') }}" class="{{ $input }}" dir="ltr" :required="withEmployee">
            </div>
            <div>
                <label class="{{ $label }}">القسم</label>
                <select name="department_id" class="{{ $input }}">
                    <option value="">تلقائي حسب الدور</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(old('department_id') == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">المنصب</label>
                <input name="position" value="{{ old('position') }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">الراتب</label>
                <input type="number" name="salary" value="{{ old('salary', 0) }}" min="0" step="0.01" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">تاريخ التعيين</label>
                <input type="date" name="hire_date" value="{{ old('hire_date', now()->toDateString()) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">نوع التوظيف</label>
                <select name="employment_type" class="{{ $input }}">
                    @foreach(['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'intern' => 'متدرب'] as $k => $v)
                    <option value="{{ $k }}" @selected(old('employment_type', 'full_time') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-3 rounded-xl text-white font-bold" style="background:{{ $themeColor }}">حفظ المستخدم</button>
        <a href="{{ route('users.index') }}" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-bold">إلغاء</a>
    </div>
</form>
@endsection
