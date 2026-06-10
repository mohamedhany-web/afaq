@extends('layouts.app')
@section('page-title', 'لوحة العمليات')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => $resolver->isAdmin() ? 'متابعة مديري العمليات' : 'مركز عمليات عقاري',
    'subtitle' => 'تحويل التسويق والمبيعات إلى منظومة قابلة للقياس والنمو — ' . now()->locale('ar')->translatedFormat('d F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>',
    'actionUrl' => route('operations.reports.index'),
    'actionLabel' => 'التقارير الدورية',
])

<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'عملاء بانتظار التوزيع', 'value' => $stats['unassigned_leads'] ?? 0, 'accent' => 'red'])
    @include('crm.partials.stat-card', ['label' => 'غياب بانتظار المراجعة', 'value' => $stats['pending_absence_reviews'] ?? 0, 'accent' => 'amber'])
    @include('crm.partials.stat-card', ['label' => 'مشاريع نشطة', 'value' => $stats['active_projects'], 'accent' => 'theme'])
    @include('crm.partials.stat-card', ['label' => 'مطورون نشطون', 'value' => $stats['developers'], 'accent' => 'blue'])
    @include('crm.partials.stat-card', ['label' => 'تقارير مرفوعة', 'value' => $stats['submitted_reports'], 'accent' => 'green'])
    @include('crm.partials.stat-card', ['label' => 'مسودات تقارير', 'value' => $stats['pending_reports'], 'accent' => 'amber'])
</div>

@if(($kpi['overall_score'] ?? 0) > 0 || !empty($kpi['items']))
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 font-tajawal">
    <div class="px-5 py-4 border-b font-bold" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">
        مؤشرات الأداء المركّبة — {{ $period->label ?? 'الفترة الحالية' }}
    </div>
    <div class="p-5 sm:p-6">
        <div class="flex flex-wrap items-center gap-4 mb-4">
            <div class="text-3xl font-extrabold" style="color: {{ $themeColor }};">{{ number_format($kpi['overall_score'] ?? 0, 1) }}%</div>
            <span class="px-3 py-1 rounded-full text-sm font-bold bg-gray-100">{{ $kpi['level']['label'] ?? '—' }}</span>
        </div>
        @if(!empty($kpi['items']))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach(array_slice($kpi['items'], 0, 6) as $item)
            <div class="p-3 rounded-xl bg-gray-50">
                <p class="text-xs text-gray-500">{{ $item['name'] }}</p>
                <p class="font-bold text-gray-900">{{ number_format($item['actual'], 1) }}%</p>
                <p class="text-xs mt-1" style="color: {{ $themeColor }};">تحقيق {{ number_format($item['achievement'], 1) }}%</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endif

@php
    $groupLinks = [
        'lead_management' => route('operations.leads.index'),
        'crm_management' => route('operations.crm.index'),
        'sales_operations' => route('operations.crm.index'),
        'revenue_impact' => route('operations.team.index'),
        'inventory_operations' => route('operations.inventory.index'),
        'team_performance' => route('operations.team.index'),
        'reporting_management' => route('operations.reports.index'),
    ];
@endphp

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
    @foreach($kpiGroups['groups'] ?? [] as $key => $group)
        @include('operations.partials.kpi-group', ['group' => $group, 'link' => $groupLinks[$key] ?? null])
    @endforeach
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 font-tajawal">
    <a href="{{ route('operations.leads.index') }}" class="p-5 rounded-2xl border-2 border-gray-200 bg-white hover:shadow-lg transition-shadow">
        <p class="font-bold text-gray-900 mb-1">رحلة العميل وتوزيع الـ Leads</p>
        <p class="text-sm text-gray-600">استلام من التسويق، توزيع على المبيعات، تقليل الفقد.</p>
    </a>
    <a href="{{ route('operations.crm.index') }}" class="p-5 rounded-2xl border-2 border-gray-200 bg-white hover:shadow-lg transition-shadow">
        <p class="font-bold text-gray-900 mb-1">متابعة CRM والـ Pipeline</p>
        <p class="text-sm text-gray-600">جودة البيانات، الصفقات المتعثرة، المتابعات الفائتة.</p>
    </a>
    <a href="{{ route('operations.inventory.index') }}" class="p-5 rounded-2xl border-2 border-gray-200 bg-white hover:shadow-lg transition-shadow">
        <p class="font-bold text-gray-900 mb-1">المخزون العقاري</p>
        <p class="text-sm text-gray-600">الوحدات المتاحة والمحجوزة ودقة الأسعار.</p>
    </a>
    <a href="{{ route('operations.attendance-reviews.index') }}" class="p-5 rounded-2xl border-2 border-gray-200 bg-white hover:shadow-lg transition-shadow">
        <p class="font-bold text-gray-900 mb-1">مراجعة الغياب</p>
        <p class="text-sm text-gray-600">تأكيد حضور/غياب الفريق قبل الخصومات.</p>
    </a>
</div>
@endsection
