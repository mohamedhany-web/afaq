@extends('layouts.app')
@section('page-title', ($marketingOnly ?? false) ? 'تعديل موظف تسويق' : 'تعديل موظف مبيعات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $fullName = trim($employee->first_name . ' ' . $employee->last_name);
    $isSuperAdminUser = $employee->user?->hasRole('super_admin') ?? false;
@endphp

@include('crm.partials.page-header', [
    'title' => ($marketingOnly ?? false) ? 'تعديل موظف تسويق' : 'تعديل موظف مبيعات',
    'subtitle' => $fullName . ' — ' . ($employee->employee_id ?? ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
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

<form action="{{ route('employees.update', $employee) }}" method="POST" class="w-full space-y-6">
    @csrf @method('PUT')
    @if($salesOnly ?? false)<input type="hidden" name="sales_only" value="1">@endif
    @if($marketingOnly ?? false)<input type="hidden" name="marketing_only" value="1">@endif

    @if($employee->user)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">حساب النظام</div>
        <div class="p-5 sm:p-6 text-sm font-tajawal text-gray-600">
            <span class="font-semibold text-gray-900">{{ $employee->user->name }}</span>
            <span class="mx-2">·</span>
            <span dir="ltr">{{ $employee->user->email }}</span>
            @if($isSuperAdminUser)
            <p class="text-xs text-amber-600 mt-2">مستخدم super admin — لا يُغيّر دوره من هنا.</p>
            @endif
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">البيانات الشخصية</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div>
                <label class="{{ $label }}">رقم الموظف *</label>
                <input name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required class="{{ $input }}">
                @error('employee_id')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">الاسم الأول *</label>
                <input name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="{{ $input }}">
                @error('first_name')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">اسم العائلة *</label>
                <input name="last_name" value="{{ old('last_name', $employee->last_name) }}" required class="{{ $input }}">
                @error('last_name')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">الهاتف *</label>
                <input name="phone" value="{{ old('phone', $employee->phone) }}" required class="{{ $input }}" dir="ltr">
                @error('phone')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $label }}">البريد الإلكتروني *</label>
                <input type="email" name="email" value="{{ old('email', $employee->email) }}" required class="{{ $input }}" dir="ltr">
                @error('email')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="{{ $label }}">العنوان</label>
                <input name="address" value="{{ old('address', $employee->address) }}" class="{{ $input }}">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">الدور والوظيفة — {{ ($marketingOnly ?? false) ? 'قسم التسويق' : 'قسم المبيعات' }}</div>
        <div class="p-5 sm:p-6 space-y-5">
            @unless($isSuperAdminUser)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($roleLabels as $val => $labelText)
                <label class="relative cursor-pointer block">
                    <input type="radio" name="crm_role" value="{{ $val }}" class="peer sr-only"
                           @checked(old('crm_role', $currentRole) === $val)>
                    <div class="role-card p-4 rounded-xl border-2 border-gray-200 transition-all text-center font-tajawal">
                        <div class="font-bold text-gray-900">{{ $labelText }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            @if($marketingOnly ?? false)
                                @if($val === 'marketing_manager') إدارة الحملات والفريق @else تنفيذ المهام وجمع Leads @endif
                            @elseif($val === 'manager') لوحة الفريق + إدارة فرق المبيعات
                            @else CRM — العملاء ومسار المبيعات @endif
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('crm_role')<p class="text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            @endunless

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <label class="{{ $label }}">القسم</label>
                    <input type="hidden" name="department_id" value="{{ $salesDepartment->id }}">
                    <div class="px-4 py-3 rounded-xl bg-gray-50 border-2 border-gray-200 text-sm font-semibold font-tajawal">{{ $salesDepartment->name }}</div>
                </div>
                <div>
                    <label class="{{ $label }}">المنصب</label>
                    <input name="position" value="{{ old('position', $employee->position) }}" class="{{ $input }}" placeholder="يُملأ تلقائياً حسب الدور">
                </div>
                <div>
                    <label class="{{ $label }}">حالة الموظف *</label>
                    <select name="status" required class="{{ $input }}">
                        <option value="active" @selected(old('status', $employee->status) === 'active')>نشط</option>
                        <option value="inactive" @selected(old('status', $employee->status) === 'inactive')>غير نشط</option>
                        <option value="terminated" @selected(old('status', $employee->status) === 'terminated')>منتهي الخدمة</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">بيانات التوظيف</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div>
                <label class="{{ $label }}">الراتب (ج.م) *</label>
                <input type="number" name="salary" value="{{ old('salary', $employee->salary) }}" required min="0" step="0.01" class="{{ $input }}">
                @error('salary')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">ساعات العمل *</label>
                <input type="number" name="daily_hours" value="{{ old('daily_hours', $employee->daily_hours ?? 8) }}" required min="1" max="12" class="{{ $input }}">
                @error('daily_hours')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">تاريخ التوظيف</label>
                <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">نوع التوظيف</label>
                <select name="employment_type" class="{{ $input }}">
                    @foreach(['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'intern' => 'متدرب'] as $val => $txt)
                        <option value="{{ $val }}" @selected(old('employment_type', $employee->employment_type ?? 'full_time') === $val)>{{ $txt }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">جهة اتصال الطوارئ</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div>
                <label class="{{ $label }}">الاسم</label>
                <input name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">الهاتف</label>
                <input name="emergency_phone" value="{{ old('emergency_phone', $employee->emergency_phone) }}" class="{{ $input }}" dir="ltr">
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employees.show', array_merge(['employee' => $employee], array_filter(['sales_only' => ($salesOnly ?? false) ? 1 : null, 'marketing_only' => ($marketingOnly ?? false) ? 1 : null]))) }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
            @if($canDelete ?? false)
            <button type="button" onclick="if(confirm('حذف هذا الموظف؟')) document.getElementById('delete-employee-form').submit();"
                    class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-red-200 text-red-600 font-semibold text-sm hover:bg-red-50 font-tajawal">حذف الموظف</button>
            @endif
        </div>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">حفظ التعديلات</button>
    </div>
</form>

@if($canDelete ?? false)
<form id="delete-employee-form" action="{{ route('employees.destroy', $employee) }}" method="POST" class="hidden">
    @csrf @method('DELETE')
    @if($salesOnly ?? false)<input type="hidden" name="sales_only" value="1">@endif
</form>
@endif

<style>
    input[name="crm_role"]:checked + .role-card {
        border-color: {{ $themeColor }};
        background: {{ $themeColor }}14;
        box-shadow: 0 4px 14px {{ $themeColor }}25;
    }
</style>
@endsection
