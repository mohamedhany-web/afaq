@extends('layouts.app')
@section('page-title', 'الأدوار والصلاحيات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
@endphp

@include('crm.partials.page-header', [
    'title' => 'الأدوار والصلاحيات',
    'subtitle' => 'نظام مرن — عيّن الدور، عدّل صلاحياته الافتراضية، أو خصّص صلاحيات كل مستخدم',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
])

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-tajawal">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-tajawal">{{ session('error') }}</div>
@endif

@include('roles.partials.permission-sync-status', ['permissionSyncReport' => $permissionSyncReport ?? null])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'أدوار نشطة', 'value' => $stats['total_roles'], 'accent' => 'theme', 'href' => route('roles.index') . '#roles-grid', 'linkLabel' => 'عرض الأدوار'])
    @include('crm.partials.stat-card', ['label' => 'صلاحيات قابلة للتخصيص', 'value' => $stats['total_permissions'], 'accent' => 'purple', 'href' => route('roles.index') . '#roles-grid', 'linkLabel' => 'مصفوفة CRUD'])
    @include('crm.partials.stat-card', ['label' => 'كل المستخدمين', 'value' => $stats['total_users'], 'accent' => 'blue', 'href' => route('users.index'), 'linkLabel' => 'عرض المستخدمين'])
    @include('crm.partials.stat-card', ['label' => $workspaceFilter && isset($workspaceGroups[$workspaceFilter]) ? $workspaceGroups[$workspaceFilter]['label'] : 'المعروضون', 'value' => $stats['workspace_users'], 'accent' => 'amber', 'href' => route('roles.index'), 'linkLabel' => 'قائمة المستخدمين'])
</div>

<div id="roles-grid" class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-6 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-900 font-tajawal">أدوار النظام</h3>
        <p class="text-xs text-gray-500 mt-1 font-tajawal">اضغط «تعديل الصلاحيات» لتعيين صلاحيات CRUD الافتراضية للدور — تُورَّث لكل مستخدم ما لم تُخصَّص له</p>
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($roles as $role)
            @php
                $meta = \App\Services\CrmRoleCatalogService::roleMeta($role->name);
                $color = $meta['color'] ?? $themeColor;
            @endphp
            <div class="rounded-xl border border-gray-200 p-5 hover:shadow-md transition flex flex-col" style="background: linear-gradient(135deg, {{ $color }}08, {{ $color }}03);">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background: {{ $color }};">{{ mb_substr($meta['label'], 0, 1) }}</div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full" style="background: {{ $color }}15; color: {{ $color }};">{{ $role->permissions->count() }} صلاحية</span>
                </div>
                <h4 class="font-bold text-gray-900 font-tajawal">{{ $meta['label'] }}</h4>
                <p class="text-xs text-gray-500 mt-1 font-tajawal" dir="ltr">{{ $role->name }}</p>
                <p class="text-sm text-gray-600 mt-2 font-tajawal flex-1">{{ $meta['description'] }}</p>
                <a href="{{ route('roles.role-permissions', $role) }}" class="mt-4 inline-flex items-center justify-center px-4 py-2 rounded-lg text-xs font-semibold text-white font-tajawal" style="background: {{ $color }};">
                    تعديل صلاحيات الدور
                </a>
            </div>
        @endforeach
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 font-tajawal">المستخدمون — تخصيص فردي</h3>
                <p class="text-xs text-gray-500 font-tajawal">أضف أو أزل صلاحيات فوق الدور — تنعكس على السايدبار والصفحات</p>
            </div>
            <a href="{{ route('users.index') }}" class="text-xs font-semibold font-tajawal px-3 py-2 rounded-lg shrink-0" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">إدارة المستخدمين</a>
        </div>

        <form method="GET" action="{{ route('roles.index') }}" class="mt-4 flex flex-col sm:flex-row gap-3">
            <input type="text" name="q" value="{{ $search }}" placeholder="بحث بالاسم أو البريد…"
                   class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-tajawal">
            <select name="workspace" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-tajawal min-w-[180px]">
                <option value="">كل الأقسام / مساحات العمل</option>
                @foreach($workspaceGroups as $key => $group)
                    <option value="{{ $key }}" @selected($workspaceFilter === $key)>
                        {{ $group['label'] }} ({{ $workspaceCounts[$key]['count'] ?? 0 }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal" style="background: {{ $themeColor }};">تصفية</button>
            @if($workspaceFilter || $search)
                <a href="{{ route('roles.index') }}" class="px-4 py-2.5 rounded-xl bg-gray-100 text-sm font-tajawal text-center">إعادة تعيين</a>
            @endif
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm font-tajawal">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="text-right px-5 py-3">المستخدم</th>
                    <th class="text-right px-5 py-3">القسم</th>
                    <th class="text-right px-5 py-3">الدور</th>
                    <th class="text-right px-5 py-3">الصلاحيات الفعّالة</th>
                    <th class="text-right px-5 py-3">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    @php
                        $displayRole = \App\Services\CrmRoleCatalogService::resolveUserDisplayRole($user);
                        $meta = $displayRole ? \App\Services\CrmRoleCatalogService::roleMeta($displayRole) : null;
                        $color = $meta['color'] ?? '#6b7280';
                        $dept = $user->employee?->department?->name;
                        $wsKey = $displayRole ? \App\Services\CrmRoleCatalogService::workspaceGroupForRole($displayRole) : null;
                        $wsLabel = $wsKey ? ($workspaceGroups[$wsKey]['label'] ?? $wsKey) : '—';
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500" dir="ltr">{{ $user->email }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-gray-800">{{ $dept ?? '—' }}</p>
                            <p class="text-[10px] text-gray-400">{{ $wsLabel }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if($displayRole && $meta)
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $color }}15; color: {{ $color }};">
                                    {{ $meta['label'] }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">بدون دور</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $user->getAllPermissions()->count() }}</td>
                        <td class="px-5 py-4">
                            <a href="{{ route('roles.user-permissions', $user) }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold text-white" style="background: {{ $themeColor }};">
                                إدارة الصلاحيات
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-500">لا يوجد مستخدمون مطابقون للتصفية</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
