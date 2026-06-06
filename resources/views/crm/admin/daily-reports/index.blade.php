@extends('layouts.app')

@section('page-title', 'تقارير المبيعات — الإدارة')

@section('content')

@include('crm.partials.page-header', [
    'title' => 'تقارير المبيعات اليومية',
    'subtitle' => 'عرض جميع التقارير المرفوعة من موظفي المبيعات — للإدارة العليا فقط',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
])

@include('crm.daily-reports.partials.alerts')

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي التقارير', 'value' => $reports->total(), 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'])
    @include('crm.partials.stat-card', ['label' => 'مرفوعة', 'value' => $stats['submitted'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'])
    @include('crm.partials.stat-card', ['label' => 'تقارير اليوم', 'value' => $stats['today'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'])
</div>

@include('crm.daily-reports.partials.filters', ['teamMembers' => $teamMembers, 'showStatusFilter' => false])

@include('crm.daily-reports.partials.list-table', [
    'tableTitle' => 'تقارير موظفي المبيعات',
    'showEmployeeColumn' => true,
    'emptyMessage' => 'لا توجد تقارير مرفوعة بعد.',
])
@endsection
