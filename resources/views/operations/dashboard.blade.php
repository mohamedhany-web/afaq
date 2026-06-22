@extends('layouts.app')
@section('page-title', __('operations.dashboard_title'))

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $isLtr = app()->getLocale() === 'en';
    $kpiLinks = [
        'lead_management' => route('operations.clients.index', ['view' => 'distribution']),
        'crm_management' => route('operations.crm.index'),
        'sales_operations' => route('operations.crm.index'),
        'revenue_impact' => route('operations.crm.index'),
        'inventory_operations' => route('operations.inventory.index'),
        'team_performance' => route('operations.team.index'),
        'reporting_management' => route('operations.reports.index'),
    ];
    $detailArrow = $isLtr ? '→' : '←';
@endphp

<div class="operations-locale-surface {{ $isLtr ? 'text-start' : 'text-right' }}" @if($isLtr) dir="ltr" @endif>
@include('crm.partials.page-header', [
    'title' => __('operations.dashboard_title'),
    'subtitle' => __('operations.dashboard_subtitle'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
    'actionUrl' => route('operations.reports.index'),
    'actionLabel' => __('operations.actions.my_reports'),
])

<div class="flex flex-wrap items-center gap-3 mb-6 font-tajawal">
    @include('partials.ui-compact-toggle', ['themeColor' => $themeColor, 'labelOn' => __('operations.ui.compact_on'), 'labelOff' => __('operations.ui.compact_off')])
    @include('operations.partials.rep-search-form', ['salesReps' => $salesReps, 'compact' => true])
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 mb-6" id="page-data">
    @foreach($workspaceSections as $section)
    @include('crm.partials.stat-card', [
        'label' => $section['label'],
        'value' => number_format($section['count']),
        'accent' => $section['accent'],
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . $section['icon'] . '"/>',
        'href' => $section['href'],
        'linkLabel' => $section['linkLabel'],
    ])
    @endforeach
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6 ui-compact-hidden">
    @include('crm.partials.stat-card', ['label' => __('operations.stats.pending_distribution'), 'value' => $stats['unassigned_leads'], 'accent' => 'amber', 'href' => route('operations.clients.index', ['view' => 'distribution']) . '#page-data', 'linkLabel' => __('operations.clients.tab_distribution')])
    @include('crm.partials.stat-card', ['label' => __('operations.stats.absence_reviews'), 'value' => $stats['pending_absence_reviews'], 'accent' => 'red', 'href' => ($absenceReviewsLink ?? route('operations.attendance-reviews.index', ['status' => 'pending'])) . '#page-data', 'linkLabel' => __('operations.actions.view_details')])
    @include('crm.partials.stat-card', ['label' => __('operations.stats.checkout_approvals'), 'value' => $stats['pending_checkout_reviews'], 'accent' => 'purple', 'href' => route('operations.checkout-reviews.index') . '#page-data', 'linkLabel' => __('operations.actions.view_details')])
    @include('crm.partials.stat-card', ['label' => __('operations.stats.active_projects'), 'value' => $stats['active_projects'], 'accent' => 'theme', 'href' => route('operations.inventory.index') . '#page-data', 'linkLabel' => __('operations.actions.view_details')])
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6 font-tajawal">
    <div class="bg-white rounded-2xl border p-5 ui-compact-hidden text-start">
        <p class="text-xs text-gray-500 mb-1">{{ __('operations.stats.compensation_kpis') }} — {{ $period->label }}</p>
        <p class="text-3xl font-extrabold" style="color:{{ $themeColor }}">{{ number_format($kpi['total_score'] ?? 0, 1) }}%</p>
        <a href="{{ route('crm.compensation.dashboard') }}" class="inline-flex items-center gap-1 text-xs font-bold mt-3 hover:underline" style="color:{{ $themeColor }}">{{ __('operations.stats.compensation_details') }} {{ $detailArrow }}</a>
    </div>
    <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach([
            ['route' => 'operations.clients.index', 'params' => ['bucket' => 'all'], 'label' => __('operations.clients.hub_title'), 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['route' => 'operations.follow-ups.index', 'params' => [], 'label' => __('operations.quick_actions.follow_ups'), 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['route' => 'operations.team.index', 'params' => [], 'label' => __('operations.quick_actions.team_performance'), 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['route' => 'operations.reps.search', 'params' => [], 'label' => __('operations.actions.search_sales_rep'), 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
            ['route' => 'operations.inventory.index', 'params' => [], 'label' => __('operations.quick_actions.inventory'), 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ] as $action)
        <a href="{{ route($action['route'], $action['params'] ?? []) }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl border bg-white hover:shadow-md transition-shadow text-center group">
            <div class="p-3 rounded-xl text-white" style="background:{{ $themeColor }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/></svg>
            </div>
            <span class="text-xs font-bold text-gray-800">{{ $action['label'] }}</span>
        </a>
        @endforeach
    </div>
</div>

@if(!empty($kpiGroups))
<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 font-tajawal ui-compact-hidden">
    @foreach($kpiGroups as $group)
    @include('operations.partials.kpi-group', [
        'group' => $group,
        'link' => $kpiLinks[$group['key'] ?? ''] ?? null,
    ])
    @endforeach
</div>
@endif
</div>
@endsection
