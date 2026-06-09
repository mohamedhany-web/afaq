@extends('layouts.app')
@section('page-title', ($marketingOnly ?? false) ? 'إضافة موظف تسويق' : 'إضافة موظف مبيعات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
@endphp

@include('crm.partials.page-header', [
    'title' => ($marketingOnly ?? false) ? 'إضافة موظف تسويق' : 'إضافة موظف مبيعات',
    'subtitle' => ($marketingOnly ?? false) ? 'قسم التسويق — مدير تسويق أو موظف تسويق' : 'قسم المبيعات العقارية — اختر الدور: مدير مبيعات أو موظف مبيعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
])

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5">
    <p class="font-bold text-red-800 font-tajawal mb-2">يرجى تصحيح الأخطاء التالية:</p>
    <ul class="list-disc pr-5 text-sm text-red-700 space-y-1 font-tajawal">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('employees.store') }}" method="POST" class="w-full space-y-6">
    @csrf
    @if($salesOnly ?? false)<input type="hidden" name="sales_only" value="1">@endif
    @if($marketingOnly ?? false)<input type="hidden" name="marketing_only" value="1">@endif

    {{-- حساب الدخول --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            حساب الدخول
        </div>
        <div class="p-5 sm:p-6 space-y-5">
            <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors font-tajawal"
                   style="border-color: {{ old('create_new_user', true) ? $themeColor . '50' : '#e5e7eb' }}; background: {{ old('create_new_user', true) ? $themeColor . '08' : '#fff' }};">
                <input type="checkbox" name="create_new_user" id="create_new_user" value="1" class="mt-1 w-5 h-5 rounded"
                       style="accent-color: {{ $themeColor }};"
                       {{ old('create_new_user', true) ? 'checked' : '' }}
                       onchange="toggleUserSelection()">
                <div>
                    <span class="font-semibold text-gray-900 block">إنشاء حساب مستخدم جديد</span>
                    <span class="text-xs text-gray-500 mt-1 block">يُنشأ حساب بالبريد وكلمة المرور مع صلاحيات الدور المختار</span>
                </div>
            </label>

            <div id="user_selection_container" class="hidden">
                <label class="{{ $label }}">ربط بمستخدم موجود</label>
                <select name="user_id" id="user_id" class="{{ $input }}">
                    <option value="">اختر مستخدماً</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} — {{ $user->email }}</option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>

            <div id="password_fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $label }}">كلمة المرور *</label>
                    <input type="password" name="password" id="password" class="{{ $input }}" placeholder="8 أحرف على الأقل">
                    @error('password')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $label }}">تأكيد كلمة المرور *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="{{ $input }}">
                </div>
            </div>
        </div>
    </div>

    {{-- البيانات الشخصية --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            البيانات الشخصية
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div>
                <label class="{{ $label }}">الاسم الأول *</label>
                <input name="first_name" value="{{ old('first_name') }}" required class="{{ $input }}" placeholder="الاسم الأول">
                @error('first_name')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">اسم العائلة *</label>
                <input name="last_name" value="{{ old('last_name') }}" required class="{{ $input }}" placeholder="اسم العائلة">
                @error('last_name')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">رقم الهاتف *</label>
                <input name="phone" value="{{ old('phone') }}" required class="{{ $input }}" placeholder="01xxxxxxxxx" dir="ltr">
                @error('phone')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2 lg:col-span-2">
                <label class="{{ $label }}">البريد الإلكتروني *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="{{ $input }}" placeholder="email@example.com" dir="ltr">
                @error('email')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="{{ $label }}">العنوان</label>
                <input name="address" value="{{ old('address') }}" class="{{ $input }}" placeholder="المدينة، الحي...">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="{{ $label }}">رقم الموظف</label>
                <div class="px-4 py-3 rounded-xl bg-gray-50 border-2 border-gray-200 text-sm text-gray-600 font-tajawal flex items-center gap-2">
                    <svg class="w-4 h-4" style="color: {{ $themeColor }};" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    يُولَّد تلقائياً عند الحفظ
                </div>
            </div>
        </div>
    </div>

    {{-- الدور والوظيفة --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            الدور والوظيفة — قسم المبيعات
        </div>
        <div class="p-5 sm:p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($roleLabels as $val => $labelText)
                <label class="relative cursor-pointer block">
                    <input type="radio" name="crm_role" value="{{ $val }}" class="peer sr-only"
                           @checked(old('crm_role', ($marketingOnly ?? false) ? 'marketing_rep' : 'employee') === $val) onchange="updateRoleHint()">
                    <div class="role-card p-4 rounded-xl border-2 border-gray-200 transition-all text-center font-tajawal">
                        <div class="font-bold text-gray-900">{{ $labelText }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            @if($marketingOnly ?? false)
                                @if($val === 'marketing_manager') إدارة الحملات والفريق @else تنفيذ المهام وجمع Leads @endif
                            @elseif($val === 'manager')
                                لوحة الفريق + إدارة فرق المبيعات
                            @else
                                CRM — العملاء ومسار المبيعات
                            @endif
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('crm_role')<p class="text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <label class="{{ $label }}">القسم</label>
                    <input type="hidden" name="department_id" value="{{ $salesDepartment->id }}">
                    <div class="px-4 py-3 rounded-xl bg-gray-50 border-2 border-gray-200 text-sm font-semibold text-gray-800 font-tajawal">
                        {{ $salesDepartment->name }}
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">المنصب (اختياري)</label>
                    <input name="position" id="position" value="{{ old('position') }}" class="{{ $input }}"
                           placeholder="يُملأ تلقائياً حسب الدور">
                </div>
            </div>
        </div>
    </div>

    {{-- التوظيف والراتب --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            بيانات التوظيف
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div>
                <label class="{{ $label }}">الراتب الشهري (ج.م) *</label>
                <input type="number" name="salary" value="{{ old('salary') }}" required min="0" step="0.01" class="{{ $input }}">
                @error('salary')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">تاريخ التوظيف *</label>
                <input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required class="{{ $input }}">
                @error('hire_date')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">نوع التوظيف *</label>
                <select name="employment_type" required class="{{ $input }}">
                    <option value="full_time" @selected(old('employment_type', 'full_time') === 'full_time')>دوام كامل</option>
                    <option value="part_time" @selected(old('employment_type') === 'part_time')>دوام جزئي</option>
                    <option value="contract" @selected(old('employment_type') === 'contract')>عقد</option>
                    <option value="intern" @selected(old('employment_type') === 'intern')>متدرب</option>
                </select>
                @error('employment_type')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    @include('employees.partials.work-schedule-fields', ['employee' => new \App\Models\Employee()])

    {{-- الطوارئ --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            جهة اتصال الطوارئ (اختياري)
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div>
                <label class="{{ $label }}">اسم جهة الاتصال</label>
                <input name="emergency_contact" value="{{ old('emergency_contact') }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">هاتف الطوارئ</label>
                <input name="emergency_phone" value="{{ old('emergency_phone') }}" class="{{ $input }}" dir="ltr">
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full pb-6">
        <a href="{{ route('employees.index', ($salesOnly ?? false) ? ['sales_only' => 1] : []) }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">
            إلغاء والعودة للقائمة
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            حفظ الموظف
        </button>
    </div>
</form>

<style>
    input[name="crm_role"]:checked + .role-card {
        border-color: {{ $themeColor }};
        background: {{ $themeColor }}14;
        box-shadow: 0 4px 14px {{ $themeColor }}25;
    }
</style>

<script>
function toggleUserSelection() {
    const createNew = document.getElementById('create_new_user').checked;
    const userBox = document.getElementById('user_selection_container');
    const passBox = document.getElementById('password_fields');
    const userSelect = document.getElementById('user_id');
    const pass = document.getElementById('password');
    const passConf = document.getElementById('password_confirmation');

    if (createNew) {
        userBox.classList.add('hidden');
        passBox.classList.remove('hidden');
        userSelect.required = false;
        userSelect.value = '';
        pass.required = true;
        passConf.required = true;
    } else {
        userBox.classList.remove('hidden');
        passBox.classList.add('hidden');
        userSelect.required = true;
        pass.required = false;
        passConf.required = false;
        pass.value = '';
        passConf.value = '';
    }
}

function updateRoleHint() {
    const role = document.querySelector('input[name="crm_role"]:checked')?.value;
    const pos = document.getElementById('position');
    if (!pos.value && role) {
        pos.placeholder = role === 'manager' ? 'مدير مبيعات' : 'موظف مبيعات';
    }
}

document.addEventListener('DOMContentLoaded', toggleUserSelection);
</script>
@endsection
