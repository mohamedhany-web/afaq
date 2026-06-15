@extends('layouts.app')
@section('page-title', 'الأدوار والصلاحيات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
@endphp

@include('crm.partials.page-header', [
    'title' => 'الأدوار والصلاحيات',
    'subtitle' => 'نظام Solvesta العقاري — أدوار CRM الحالية فقط',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
])

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-tajawal">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-tajawal">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'أدوار نشطة', 'value' => $stats['total_roles'], 'accent' => 'theme', 'href' => route('roles.index') . '#page-data', 'linkLabel' => 'عرض الأدوار'])
    @include('crm.partials.stat-card', ['label' => 'صلاحيات CRM', 'value' => $stats['total_permissions'], 'accent' => 'purple', 'href' => route('roles.index') . '#page-data', 'linkLabel' => 'عرض الصلاحيات'])
    @include('crm.partials.stat-card', ['label' => 'المستخدمون', 'value' => $stats['total_users'], 'accent' => 'blue', 'href' => route('users.index'), 'linkLabel' => 'عرض المستخدمين'])
    @include('crm.partials.stat-card', ['label' => 'فريق المبيعات', 'value' => $stats['crm_users'], 'accent' => 'amber', 'href' => route('employees.index', ['sales_only' => 1]), 'linkLabel' => 'عرض الفريق'])
</div>

<div id="page-data" class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-6 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-900 font-tajawal">أدوار النظام العقاري</h3>
        <p class="text-xs text-gray-500 mt-1 font-tajawal">تم إخفاء الأدوار القديمة (مشاريع برمجية، QA، تصميم…) — الأدوار أدناه فقط</p>
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($roles as $role)
            @php
                $meta = \App\Services\CrmRoleCatalogService::roleMeta($role->name);
                $color = $meta['color'] ?? $themeColor;
            @endphp
            <div class="rounded-xl border border-gray-200 p-5 hover:shadow-md transition" style="background: linear-gradient(135deg, {{ $color }}08, {{ $color }}03);">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background: {{ $color }};">{{ mb_substr($meta['label'], 0, 1) }}</div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full" style="background: {{ $color }}15; color: {{ $color }};">{{ $role->permissions->count() }} صلاحية</span>
                </div>
                <h4 class="font-bold text-gray-900 font-tajawal">{{ $meta['label'] }}</h4>
                <p class="text-xs text-gray-500 mt-1 font-tajawal">{{ $role->name }}</p>
                <p class="text-sm text-gray-600 mt-2 font-tajawal">{{ $meta['description'] }}</p>
                @if(!empty($meta['workspace']))
                    <span class="inline-block mt-3 text-[10px] px-2 py-1 rounded-full bg-gray-100 text-gray-600 font-tajawal">
                        {{ match($meta['workspace']) {
                            'admin' => 'لوحة الإدارة',
                            'crm_manager' => 'CRM — مدير',
                            'crm_rep' => 'CRM — موظف',
                            'hr' => 'موارد بشرية',
                            'client_portal' => 'بوابة العميل',
                            default => $meta['workspace'],
                        } }}
                    </span>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h3 class="font-bold text-gray-900 font-tajawal">المستخدمون والأدوار</h3>
            <p class="text-xs text-gray-500 font-tajawal">تعيين دور CRM لكل مستخدم</p>
        </div>
        <a href="{{ route('users.index') }}" class="text-xs font-semibold font-tajawal px-3 py-2 rounded-lg" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">إدارة المستخدمين</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm font-tajawal">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="text-right px-5 py-3">المستخدم</th>
                    <th class="text-right px-5 py-3">الدور الحالي</th>
                    <th class="text-right px-5 py-3">الصلاحيات</th>
                    <th class="text-right px-5 py-3">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                    @php
                        $displayRole = \App\Services\CrmRoleCatalogService::resolveUserDisplayRole($user);
                        $meta = $displayRole ? \App\Services\CrmRoleCatalogService::roleMeta($displayRole) : null;
                        $color = $meta['color'] ?? '#6b7280';
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500" dir="ltr">{{ $user->email }}</p>
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
                                إدارة الدور
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
