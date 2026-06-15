@extends('layouts.app')
@section('page-title', 'مسار الصفقات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $stageColors = [
        'lead' => ['bg' => '#6366f1', 'light' => '#eef2ff'],
        'prospect' => ['bg' => '#3b82f6', 'light' => '#eff6ff'],
        'proposal' => ['bg' => '#0ea5e9', 'light' => '#f0f9ff'],
        'negotiation' => ['bg' => '#f59e0b', 'light' => '#fffbeb'],
        'closed_won' => ['bg' => '#16a34a', 'light' => '#f0fdf4'],
        'closed_lost' => ['bg' => '#ef4444', 'light' => '#fef2f2'],
    ];
@endphp

@include('crm.partials.page-header', [
    'title' => 'مسار الصفقات',
    'subtitle' => 'عرض Kanban حسب مرحلة الصفقة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />',
    'actionUrl' => route('crm.pipeline.create'),
    'actionLabel' => 'صفقة جديدة',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])

<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الصفقات', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />', 'href' => route('crm.pipeline.index', ['view' => 'deals']) . '#pipeline-kanban', 'linkLabel' => 'عرض Kanban'])
    @include('crm.partials.stat-card', ['label' => 'صفقات نشطة', 'value' => $stats['active'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />', 'href' => route('crm.pipeline.index', ['view' => 'deals']) . '#pipeline-kanban', 'linkLabel' => 'عرض النشطة'])
    @include('crm.partials.stat-card', ['label' => 'تم البيع', 'value' => $stats['won'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won', 'show_closed' => 1]) . '#pipeline-kanban', 'linkLabel' => 'عرض المباع'])
    @include('crm.partials.stat-card', ['label' => 'قيمة المسار', 'value' => $money($stats['pipeline_value']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />', 'href' => route('crm.pipeline.index', ['view' => 'deals']) . '#pipeline-kanban', 'linkLabel' => 'عرض المسار'])
    @include('crm.partials.stat-card', ['label' => 'إيرادات مكتملة', 'value' => $money($stats['won_value']), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won', 'show_closed' => 1]) . '#pipeline-kanban', 'linkLabel' => 'عرض الإيرادات'])
</div>

@include('crm.pipeline.partials.view-switcher', ['current' => 'deals'])

@include('crm.partials.filter-bar')

<div id="pipeline-kanban" class="mb-8">
    <div class="flex items-center gap-3 mb-3">
        <h2 class="text-base font-bold text-gray-900 font-tajawal">مراحل البيع النشطة</h2>
        <span class="text-xs px-2.5 py-0.5 rounded-full font-medium" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">{{ number_format($stats['active']) }} صفقة</span>
    </div>
    <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory">
        @foreach($activeStages as $stage)
            @if(!request('stage') || request('stage') === $stage)
            @include('crm.pipeline.partials.column', compact('stage', 'columns', 'stageLabels', 'stageTotals', 'stageColors', 'themeColor'))
            @endif
        @endforeach
    </div>
</div>

<div>
    <div class="flex items-center gap-3 mb-3 flex-wrap">
        <h2 class="text-base font-bold text-gray-900 font-tajawal">نتيجة الصفقات</h2>
        <span class="text-xs px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">{{ number_format($stats['won']) }} ربح</span>
        <span class="text-xs px-2.5 py-0.5 rounded-full bg-red-100 text-red-600 font-medium">{{ number_format($stats['lost']) }} خسارة</span>
    </div>
    @if($showClosed)
    <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory">
        @foreach($closedStages as $stage)
            @include('crm.pipeline.partials.column', compact('stage', 'columns', 'stageLabels', 'stageTotals', 'stageColors', 'themeColor'))
        @endforeach
    </div>
    @else
    <a href="{{ route('crm.pipeline.index', array_merge(request()->query(), ['view' => 'deals', 'show_closed' => 1])) }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 font-tajawal">
        عرض الصفقات المغلقة ({{ number_format($stats['won'] + $stats['lost']) }})
    </a>
    @endif
</div>
@include('crm.partials.lost-reason-modal')

@endsection

@push('scripts')
@include('crm.partials.pipeline-kanban-scripts', [
    'updateUrl' => route('crm.pipeline.update-stage', ['sale' => '__ID__']),
    'loadMoreUrl' => route('crm.pipeline.column-deals', ['stage' => '__STAGE__']),
    'payloadKey' => 'stage',
    'itemKey' => 'dealId',
])
@endpush
