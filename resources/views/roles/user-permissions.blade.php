@extends('layouts.app')
@section('page-title', 'دور المستخدم — ' . $user->name)

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $displayMeta = $displayRole ? \App\Services\CrmRoleCatalogService::roleMeta($displayRole) : null;
@endphp

@include('crm.partials.page-header', [
    'title' => $user->name,
    'subtitle' => 'تعيين الدور + تخصيص صلاحيات CRUD — ' . $user->email,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('roles.index'),
    'actionLabel' => 'العودة للأدوار',
])

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-tajawal">{{ session('success') }}</div>
@endif

@include('roles.partials.permission-sync-status')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-900 font-tajawal">الدور الوظيفي</h3>
            <p class="text-xs text-gray-500 mt-1 font-tajawal">
                @if($workspaceMeta)
                    مساحة العمل: <strong>{{ $workspaceMeta['label'] }}</strong> —
                @endif
                الدور الحالي:
                @if($displayMeta)
                    <strong style="color: {{ $displayMeta['color'] }};">{{ $displayMeta['label'] }}</strong>
                @else
                    <span class="text-gray-400">غير محدد</span>
                @endif
                @if($user->employee?->department)
                    — القسم: <strong>{{ $user->employee->department->name }}</strong>
                @endif
            </p>
            <p class="text-[11px] text-amber-700 mt-2 font-tajawal bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                تغيير الدور لا يمسح التخصيصات الفردية. بعد الحفظ، حدّث الصفحة (أو سجّل دخول المستخدم من جديد) لترى السايدبار محدّثاً.
            </p>
        </div>
        <div class="p-5 sm:p-6">
            <form action="{{ route('roles.assign-role', $user) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($roles as $role)
                        @php
                            $meta = \App\Services\CrmRoleCatalogService::roleMeta($role->name);
                            $color = $meta['color'] ?? $themeColor;
                            $isSelected = $displayRole === $role->name;
                        @endphp
                        <label class="relative flex items-start p-4 border-2 rounded-xl cursor-pointer transition font-tajawal"
                               style="border-color: {{ $isSelected ? $color : '#e5e7eb' }}; background: {{ $isSelected ? $color . '08' : '#fff' }};">
                            <input type="radio" name="role" value="{{ $role->name }}" class="mt-1 ml-3" {{ $isSelected ? 'checked' : '' }} onchange="this.form.submit()">
                            <div>
                                <p class="font-bold text-sm text-gray-900">{{ $meta['label'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $meta['description'] }}</p>
                                <p class="text-xs mt-2 font-semibold" style="color: {{ $color }};">{{ $role->permissions->count() }} صلاحية افتراضية</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-900 font-tajawal">معاينة السايدبار</h3>
            <p class="text-xs text-gray-500 mt-1 font-tajawal">ما يظهر للمستخدم حسب صلاحياته الفعّالة</p>
        </div>
        <div class="p-4 max-h-[420px] overflow-y-auto">
            <ul class="space-y-1.5 text-sm font-tajawal">
                @foreach($sidebarPreview as $item)
                    <li class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $item['visible'] ? 'bg-green-50 text-green-800' : 'bg-gray-50 text-gray-400 line-through' }}">
                        @if($item['visible'])
                            <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-900 font-tajawal">مصفوفة الصلاحيات (CRUD) — {{ $permissions->count() }} صلاحية</h3>
        <p class="text-xs text-gray-500 mt-1 font-tajawal">
            <span class="text-green-600">دور</span> = من الدور الافتراضي —
            <span class="text-blue-600">+</span> = مضافة للمستخدم —
            <span class="text-red-600">−</span> = مُعطّلة رغم وجودها في الدور
        </p>
    </div>
    <div class="p-5 sm:p-6">
        <form action="{{ route('roles.assign-permissions', $user) }}" method="POST">
            @csrf
            @include('roles.partials.permission-matrix', [
                'permissionGroups' => $permissionGroups,
                'permissionModules' => $permissionModules,
                'checkedPermissions' => $userPermissions,
                'rolePermissions' => $rolePermissions,
                'customPermissionsMap' => $customPermissionsMap,
                'showSource' => true,
            ])

            <div class="flex flex-wrap gap-3 justify-between pt-4 border-t border-gray-100">
                <div class="flex gap-2">
                    <button type="button" onclick="selectAllPermissions()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">تحديد الكل</button>
                    <button type="button" onclick="deselectAllPermissions()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">إلغاء الكل</button>
                </div>
                <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal" style="background: {{ $themeColor }};">حفظ تخصيص المستخدم</button>
            </div>
        </form>
    </div>
</div>
@endsection
