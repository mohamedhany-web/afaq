@extends('layouts.app')
@section('page-title', 'لوحة الموارد البشرية')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'لوحة تحكم الموارد البشرية',
    'subtitle' => 'الحضور · الأذونات · الإجازات · العقود · العهد · ملفات الموظفين',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
    'actionUrl' => route('hr.reports.monthly'),
    'actionLabel' => 'التقرير الشهري',
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'موظفون نشطون', 'value' => $stats['active_employees'], 'accent' => 'theme', 'href' => route('employees.index') . '#page-data', 'linkLabel' => 'سجل الموظفين'])
    @include('crm.partials.stat-card', ['label' => 'حضور اليوم', 'value' => $stats['present_today'], 'accent' => 'green', 'href' => route('attendances.index') . '#page-data', 'linkLabel' => 'سجل الحضور'])
    @include('crm.partials.stat-card', ['label' => 'غياب اليوم', 'value' => $stats['absent_today'], 'accent' => 'red', 'href' => route('hr.absences.index') . '#page-data', 'linkLabel' => 'مراجعة الغياب'])
    @include('crm.partials.stat-card', ['label' => 'تسجيل دخول اليوم', 'value' => $stats['checked_in_today'], 'accent' => 'blue', 'href' => route('attendances.index') . '#page-data', 'linkLabel' => 'الحضور والانصراف'])
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجازات معلّقة', 'value' => $stats['pending_leaves'], 'accent' => 'amber', 'href' => route('leaves.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => 'مراجعة الإجازات'])
    @include('crm.partials.stat-card', ['label' => 'أذونات معلّقة', 'value' => $stats['pending_permits'], 'accent' => 'purple', 'href' => route('hr.exit-permits.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => 'مراجعة الأذونات'])
    @include('crm.partials.stat-card', ['label' => 'غياب بانتظار المراجعة', 'value' => $stats['pending_absences'], 'accent' => 'red', 'href' => route('hr.absences.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => 'سجلات الغياب'])
    @include('crm.partials.stat-card', ['label' => 'إجازات معتمدة (الشهر)', 'value' => $stats['leaves_this_month'], 'accent' => 'green', 'href' => route('leaves.index', ['status' => 'approved']) . '#page-data', 'linkLabel' => 'عرض الإجازات'])
</div>

</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'عقود سارية', 'value' => $stats['active_contracts'], 'accent' => 'green', 'href' => route('hr.contracts.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'عرض العقود'])
    @include('crm.partials.stat-card', ['label' => 'عقود تنتهي قريباً', 'value' => $stats['expiring_contracts'], 'accent' => 'amber', 'href' => route('hr.contracts.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'متابعة الانتهاء'])
    @include('crm.partials.stat-card', ['label' => 'عهدة نشطة', 'value' => $stats['active_custody'], 'accent' => 'purple', 'href' => route('hr.custody.index') . '#page-data', 'linkLabel' => 'سجل العهد'])
    @include('crm.partials.stat-card', ['label' => 'ملفات محفوظة', 'value' => $stats['employee_documents'], 'accent' => 'blue', 'href' => route('hr.documents.index') . '#page-data', 'linkLabel' => 'أرشيف الملفات'])
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6 font-tajawal">
    @foreach([
        ['route' => 'attendances.index', 'label' => 'الحضور والانصراف', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['route' => 'hr.exit-permits.index', 'label' => 'الأذونات', 'icon' => 'M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z'],
        ['route' => 'leaves.index', 'label' => 'الإجازات', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['route' => 'hr.absences.index', 'label' => 'الغياب', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
        ['route' => 'hr.reports.monthly', 'label' => 'تقرير شهري', 'icon' => 'M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z'],
        ['route' => 'hr.contracts.index', 'label' => 'العقود', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['route' => 'hr.custody.index', 'label' => 'العهد', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['route' => 'hr.documents.index', 'label' => 'ملفات الموظفين', 'icon' => 'M5 19a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['route' => 'employees.index', 'label' => 'ملفات الموظفين', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    ] as $action)
    <a href="{{ route($action['route']) }}" class="bg-white rounded-2xl border p-4 hover:shadow-md transition text-center">
        <svg class="w-7 h-7 mx-auto mb-2" style="color:{{ $themeColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/></svg>
        <p class="text-sm font-bold text-gray-800">{{ $action['label'] }}</p>
    </a>
    @endforeach
</div>
@endsection
