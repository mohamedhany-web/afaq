@extends('layouts.app')

@php
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';
@endphp

@section('page-title', 'قائمة الدخل')

@section('content')
@include('accounting.partials.report-header', [
    'title' => 'قائمة الدخل',
    'subtitle' => 'من ' . $reportStartDate->format('Y/m/d') . ' إلى ' . $reportEndDate->format('Y/m/d'),
])

@include('accounting.partials.report-toolbar', [
    'filterType' => 'range',
    'startDate' => $startDate,
    'endDate' => $endDate,
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 no-print">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الإيرادات', 'value' => $money($totalRevenue), 'accent' => 'green', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'إجمالي المصروفات', 'value' => $money($totalExpenses), 'accent' => 'red', 'compact' => true])
    @include('crm.partials.stat-card', [
        'label' => $netIncome >= 0 ? 'صافي الربح' : 'صافي الخسارة',
        'value' => $money(abs($netIncome)),
        'accent' => $netIncome >= 0 ? 'blue' : 'red',
        'compact' => true,
        'footer' => $totalRevenue > 0 ? '<span class="text-gray-500">هامش: ' . number_format($profitMargin, 1) . '%</span>' : null,
    ])
    @include('crm.partials.stat-card', ['label' => 'الفترة', 'value' => $reportStartDate->format('Y/m/d') . ' — ' . $reportEndDate->format('Y/m/d'), 'accent' => 'theme', 'compact' => true])
</div>

<div id="report-document" class="font-tajawal">
    <div class="report-print-header text-center mb-6 pb-4 border-b-2 border-gray-900">
        <h2 class="text-xl font-bold">@include('accounting.partials.company-name')</h2>
        <h3 class="text-lg font-bold mt-3">قائمة الدخل</h3>
        <p class="text-sm text-gray-700">من {{ $reportStartDate->format('Y/m/d') }} إلى {{ $reportEndDate->format('Y/m/d') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 no-print">
        <div class="px-6 py-5 text-white text-center" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);">
            <h2 class="text-xl font-bold">@include('accounting.partials.company-name')</h2>
            <h3 class="text-base font-semibold mt-1 opacity-95">قائمة الدخل</h3>
            <p class="text-sm opacity-90 mt-1">من {{ $reportStartDate->format('Y/m/d') }} إلى {{ $reportEndDate->format('Y/m/d') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="border-l border-gray-100">
                <div class="{{ $sectionHeader }} bg-green-50 text-green-900">الإيرادات</div>
                <div class="p-5 sm:p-6">
                    @if($revenues->count() > 0)
                        @include('accounting.partials.report-account-tree', ['accounts' => $revenues])
                        <div class="flex justify-between py-3 mt-4 px-4 rounded-xl bg-green-50 border border-green-100">
                            <span class="font-bold text-green-800">إجمالي الإيرادات</span>
                            <span class="font-bold text-green-800 tabular-nums">{{ $money($totalRevenue) }}</span>
                        </div>
                    @else
                        <p class="text-center py-10 text-gray-500 text-sm">لا توجد إيرادات للفترة المحددة.</p>
                    @endif
                </div>
            </div>
            <div>
                <div class="{{ $sectionHeader }} bg-red-50 text-red-900">المصروفات</div>
                <div class="p-5 sm:p-6">
                    @if($expenses->count() > 0)
                        @include('accounting.partials.report-account-tree', ['accounts' => $expenses])
                        <div class="flex justify-between py-3 mt-4 px-4 rounded-xl bg-red-50 border border-red-100">
                            <span class="font-bold text-red-800">إجمالي المصروفات</span>
                            <span class="font-bold text-red-800 tabular-nums">{{ $money($totalExpenses) }}</span>
                        </div>
                    @else
                        <p class="text-center py-10 text-gray-500 text-sm">لا توجد مصروفات للفترة المحددة.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6 border-t-2 border-gray-200 {{ $netIncome >= 0 ? 'bg-green-50' : 'bg-red-50' }}">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <span class="text-xl font-bold {{ $netIncome >= 0 ? 'text-green-900' : 'text-red-900' }}">
                    {{ $netIncome >= 0 ? 'صافي الربح' : 'صافي الخسارة' }}
                </span>
                <span class="text-2xl font-bold tabular-nums {{ $netIncome >= 0 ? 'text-green-900' : 'text-red-900' }}">
                    {{ $money(abs($netIncome)) }}
                </span>
            </div>
            @if($totalRevenue > 0)
            <p class="text-sm text-center mt-3 text-gray-600">
                هامش الربح: <strong class="{{ $netIncome >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ number_format($profitMargin, 2) }}%</strong>
            </p>
            @endif
        </div>
    </div>
</div>

@include('accounting.partials.report-styles')
@endsection
