@extends('layouts.app')
@section('page-title', 'تعديل مستخدم')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-tajawal';
@endphp

@include('crm.partials.page-header', [
    'title' => 'تعديل: ' . $user->name,
    'subtitle' => $user->email,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
    'actionUrl' => route('users.show', $user),
    'actionLabel' => 'عرض الملف',
])

<form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6 font-tajawal">
    @csrf @method('PUT')

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold mb-4">بيانات الحساب</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="{{ $label }}">الاسم *</label>
                <input name="name" value="{{ old('name', $user->name) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">البريد *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="{{ $input }}" dir="ltr">
            </div>
            <div>
                <label class="{{ $label }}">كلمة مرور جديدة (اختياري)</label>
                <input type="password" name="password" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="{{ $input }}">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold mb-1">الدور</h2>
        <p class="text-sm text-gray-500 mb-4">تغيير الدور يُحدّث الصلاحيات ومساحة العمل تلقائياً</p>
        @include('users.partials.role-picker', [
            'assignableRoles' => $assignableRoles,
            'workspaceGroups' => $workspaceGroups,
            'roleHints' => $roleHints,
            'selected' => old('role', $currentRole),
        ])
    </div>

    @if($user->employee)
    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold mb-4">بيانات الموظف المرتبط</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="{{ $label }}">الاسم الأول</label><input name="first_name" value="{{ old('first_name', $user->employee->first_name) }}" class="{{ $input }}" required></div>
            <div><label class="{{ $label }}">اسم العائلة</label><input name="last_name" value="{{ old('last_name', $user->employee->last_name) }}" class="{{ $input }}" required></div>
            <div><label class="{{ $label }}">الهاتف</label><input name="phone" value="{{ old('phone', $user->employee->phone) }}" class="{{ $input }}" required></div>
            <div>
                <label class="{{ $label }}">القسم</label>
                <select name="department_id" class="{{ $input }}" required>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(old('department_id', $user->employee->department_id) == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="{{ $label }}">المنصب</label><input name="position" value="{{ old('position', $user->employee->position) }}" class="{{ $input }}" required></div>
            <div><label class="{{ $label }}">الراتب</label><input type="number" name="salary" value="{{ old('salary', $user->employee->salary) }}" class="{{ $input }}" required></div>
            <div>
                <label class="{{ $label }}">نوع التوظيف</label>
                <select name="employment_type" class="{{ $input }}" required>
                    @foreach(['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'intern' => 'متدرب'] as $k => $v)
                    <option value="{{ $k }}" @selected(old('employment_type', $user->employee->employment_type) === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">الحالة</label>
                <select name="status" class="{{ $input }}" required>
                    @foreach(['active'=>'نشط','inactive'=>'غير نشط','terminated'=>'منتهي'] as $k=>$v)
                    <option value="{{ $k }}" @selected(old('status', $user->employee->status) === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @else
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-900">
        هذا المستخدم ليس لديه سجل موظف. يمكنك إنشاء موظف من قسم <a href="{{ route('employees.create') }}" class="font-bold underline">الموظفين</a> وربطه بالمستخدم.
    </div>
    @endif

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-3 rounded-xl text-white font-bold" style="background:{{ $themeColor }}">حفظ التعديلات</button>
        <a href="{{ route('users.index') }}" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600">إلغاء</a>
    </div>
</form>
@endsection
