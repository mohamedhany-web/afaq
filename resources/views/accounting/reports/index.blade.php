@extends('layouts.app')
@section('page-title', 'التقارير المالية')

@section('content')
@include('accounting.partials.context')
@include('crm.partials.page-header', [
    'title' => 'التقارير المالية',
    'subtitle' => 'تقارير مالية شاملة — ميزانية، دخل، تدفق نقدي',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />',
    'actionUrl' => route('accounting.index'),
    'actionLabel' => 'لوحة المحاسبة',
])
@include('accounting.partials.nav')

@php
    $reports = [
        ['route' => 'accounting.reports.balance-sheet', 'title' => 'الميزانية العمومية', 'desc' => 'الأصول والخصوم وحقوق الملكية', 'accent' => 'blue', 'badge' => 'أساسي'],
        ['route' => 'accounting.reports.income-statement', 'title' => 'قائمة الدخل', 'desc' => 'الإيرادات والمصروفات وصافي الدخل', 'accent' => 'green', 'badge' => 'أساسي'],
        ['route' => 'accounting.reports.trial-balance', 'title' => 'ميزان المراجعة', 'desc' => 'أرصدة الحسابات مدين ودائن', 'accent' => 'purple', 'badge' => 'تحليلي'],
        ['route' => 'accounting.reports.cash-flow', 'title' => 'التدفق النقدي', 'desc' => 'حركة النقد التشغيلية والاستثمارية', 'accent' => 'amber', 'badge' => 'تحليلي'],
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    @foreach($reports as $report)
    <a href="{{ route($report['route']) }}" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 hover:shadow-xl transition-all font-tajawal group">
        <div class="flex items-start justify-between mb-3">
            <h3 class="font-bold text-gray-900 text-lg group-hover:underline">{{ $report['title'] }}</h3>
            <span class="text-xs font-bold px-2 py-1 rounded-lg bg-gray-100 text-gray-600">{{ $report['badge'] }}</span>
        </div>
        <p class="text-sm text-gray-600 mb-4">{{ $report['desc'] }}</p>
        <span class="text-sm font-bold" style="color:{{ $themeColor }}">عرض التقرير ←</span>
    </a>
    @endforeach
</div>
@endsection
