@extends('layouts.app')
@section('page-title', 'صلاحيات الدور — ' . ($meta['label'] ?? $role->name))

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $color = $meta['color'] ?? $themeColor;
@endphp

@include('crm.partials.page-header', [
    'title' => $meta['label'] ?? $role->name,
    'subtitle' => 'صلاحيات الدور الافتراضية — تُطبَّق على كل مستخدم بهذا الدور ما لم يُخصَّص له',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'actionUrl' => route('roles.index'),
    'actionLabel' => 'العودة للأدوار',
])

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-tajawal">{{ session('success') }}</div>
@endif

@include('roles.partials.permission-sync-status')

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-6 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="font-bold text-gray-900 font-tajawal">{{ $meta['label'] }}</h3>
            <p class="text-xs text-gray-500 mt-1 font-tajawal">{{ $meta['description'] ?? '' }} — <code dir="ltr">{{ $role->name }}</code></p>
        </div>
        <span class="text-xs font-semibold px-3 py-1.5 rounded-full" style="background: {{ $color }}15; color: {{ $color }};">
            {{ count($rolePermissions) }} صلاحية مفعّلة
        </span>
    </div>
    <div class="p-5 sm:p-6">
        <form action="{{ route('roles.update-permissions', $role) }}" method="POST">
            @csrf
            @include('roles.partials.permission-matrix', [
                'permissionGroups' => $permissionGroups,
                'permissionModules' => $permissionModules,
                'checkedPermissions' => $rolePermissions,
                'showSource' => false,
            ])

            <div class="flex flex-wrap gap-3 justify-between pt-4 border-t border-gray-100">
                <div class="flex gap-2">
                    <button type="button" onclick="selectAllPermissions()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">تحديد الكل</button>
                    <button type="button" onclick="deselectAllPermissions()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">إلغاء الكل</button>
                </div>
                <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal" style="background: {{ $themeColor }};">
                    حفظ صلاحيات الدور
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
