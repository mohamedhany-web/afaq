@extends('layouts.app')
@section('page-title', 'تفاصيل المستخدم')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $meta = $displayRole ? \App\Services\CrmRoleCatalogService::roleMeta($displayRole) : null;
@endphp

@include('crm.partials.page-header', [
    'title' => $user->name,
    'subtitle' => $user->email,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
    'actionUrl' => auth()->user()->can('edit-users') ? route('users.edit', $user) : route('users.index'),
    'actionLabel' => auth()->user()->can('edit-users') ? 'تعديل' : 'العودة',
])

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-tajawal">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border p-5 sm:p-6">
            <h2 class="font-bold text-gray-900 mb-4">بيانات الحساب</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500 mb-1">الاسم</dt><dd class="font-semibold">{{ $user->name }}</dd></div>
                <div><dt class="text-gray-500 mb-1">البريد</dt><dd class="font-semibold" dir="ltr">{{ $user->email }}</dd></div>
                <div><dt class="text-gray-500 mb-1">تاريخ الإنشاء</dt><dd>{{ $user->created_at->format('Y/m/d H:i') }}</dd></div>
                <div><dt class="text-gray-500 mb-1">آخر تحديث</dt><dd>{{ $user->updated_at->format('Y/m/d H:i') }}</dd></div>
                <div>
                    <dt class="text-gray-500 mb-1">حالة الحساب</dt>
                    <dd>
                        @if($user->email_verified_at)
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">نشط / مفعّل</span>
                        @else
                        <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">بانتظار التفعيل</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        @if($user->employee)
        <div class="bg-white rounded-2xl border p-5 sm:p-6">
            <h2 class="font-bold text-gray-900 mb-4">سجل الموظف المرتبط</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500 mb-1">الرقم التوظيفي</dt><dd class="font-mono font-semibold" dir="ltr">{{ $user->employee->employee_id }}</dd></div>
                <div><dt class="text-gray-500 mb-1">القسم</dt><dd>{{ $user->employee->department?->name ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 mb-1">المنصب</dt><dd>{{ $user->employee->position }}</dd></div>
                <div><dt class="text-gray-500 mb-1">الهاتف</dt><dd dir="ltr">{{ $user->employee->phone }}</dd></div>
                <div><dt class="text-gray-500 mb-1">الحالة</dt><dd>{{ $user->employee->status }}</dd></div>
                <div>
                    <dt class="text-gray-500 mb-1">الملف</dt>
                    <dd><a href="{{ route('employees.show', $user->employee) }}" class="font-bold underline" style="color:{{ $themeColor }}">عرض الموظف</a></dd>
                </div>
            </dl>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border p-5 sm:p-6">
            <h2 class="font-bold text-gray-900 mb-4">الدور في النظام</h2>
            @if($meta)
            <div class="p-4 rounded-2xl border-2" style="border-color: {{ $meta['color'] }}40; background: {{ $meta['color'] }}08;">
                <p class="font-bold text-lg" style="color: {{ $meta['color'] }}">{{ $meta['label'] }}</p>
                <p class="text-sm text-gray-600 mt-2">{{ $meta['description'] }}</p>
                <p class="text-xs text-gray-400 mt-3 font-mono" dir="ltr">{{ $displayRole }}</p>
            </div>
            @else
            <p class="text-sm text-gray-500">لم يُعيَّن دور بعد</p>
            @endif
        </div>

        <div class="flex flex-col gap-2">
            <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-xl border-2 border-gray-200 text-center text-sm font-bold text-gray-600">العودة للقائمة</a>
            @can('delete-users')
            @if(!$user->hasRole('super_admin') && $user->id !== auth()->id())
            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('حذف هذا المستخدم؟')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-bold">حذف المستخدم</button>
            </form>
            @endif
            @endcan
        </div>
    </div>
</div>
@endsection
