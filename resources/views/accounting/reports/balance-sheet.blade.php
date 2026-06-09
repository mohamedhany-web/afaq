@extends('layouts.app')

@php
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';
    $isBalanced = abs($totalAssets - $totalLiabilitiesEquity) < 0.01;
@endphp

@section('page-title', 'الميزانية العمومية')

@section('content')
@include('accounting.partials.report-header', [
    'title' => 'الميزانية العمومية',
    'subtitle' => 'حتى تاريخ ' . $reportDate->format('Y/m/d'),
])

@include('accounting.partials.report-toolbar', ['filterType' => 'date', 'date' => $date])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 no-print">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الأصول', 'value' => $money($totalAssets), 'accent' => 'green', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'إجمالي الخصوم', 'value' => $money($totalLiabilities), 'accent' => 'amber', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'حقوق الملكية', 'value' => $money($totalEquity), 'accent' => 'blue', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'التوازن', 'value' => $isBalanced ? 'متوازنة' : 'غير متوازنة', 'accent' => $isBalanced ? 'green' : 'red', 'compact' => true])
</div>

<div id="report-document" class="font-tajawal">
    <div class="report-print-header text-center mb-6 pb-4 border-b-2 border-gray-900">
        <h2 class="text-xl font-bold">@include('accounting.partials.company-name')</h2>
        <p class="text-sm text-gray-600 mt-1">{{ \App\Helpers\SettingsHelper::getCompanyAddress() }}</p>
        <h3 class="text-lg font-bold mt-3">الميزانية العمومية</h3>
        <p class="text-sm text-gray-700">حتى تاريخ: {{ $reportDate->format('Y/m/d') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 no-print">
        <div class="px-6 py-5 text-white text-center" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);">
            <h2 class="text-xl font-bold">@include('accounting.partials.company-name')</h2>
            <h3 class="text-base font-semibold mt-1 opacity-95">الميزانية العمومية</h3>
            <p class="text-sm opacity-90 mt-1">حتى تاريخ: {{ $reportDate->format('Y/m/d') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }} bg-green-50 text-green-900">الأصول</div>
            <div class="p-5 sm:p-6">
                @if($assets->count() > 0)
                    @include('accounting.partials.report-account-tree', ['accounts' => $assets])
                    <div class="flex justify-between py-3 mt-4 px-4 rounded-xl bg-green-50 border border-green-100">
                        <span class="font-bold text-green-800">إجمالي الأصول</span>
                        <span class="font-bold text-green-800 tabular-nums">{{ $money($totalAssets) }}</span>
                    </div>
                @else
                    <p class="text-center py-10 text-gray-500 text-sm">لا توجد أصول مسجّلة.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }} bg-blue-50 text-blue-900">الخصوم وحقوق الملكية</div>
            <div class="p-5 sm:p-6">
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">الخصوم</h4>
                @if($liabilities->count() > 0)
                    @include('accounting.partials.report-account-tree', ['accounts' => $liabilities])
                    <div class="flex justify-between py-2 px-3 mb-4 rounded-lg bg-amber-50 border border-amber-100 text-sm">
                        <span class="font-bold text-amber-800">إجمالي الخصوم</span>
                        <span class="font-bold text-amber-800 tabular-nums">{{ $money($totalLiabilities) }}</span>
                    </div>
                @else
                    <p class="text-sm text-gray-500 mb-4">لا توجد خصوم.</p>
                @endif

                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">حقوق الملكية</h4>
                @if($equity->count() > 0)
                    @include('accounting.partials.report-account-tree', ['accounts' => $equity])
                @endif
                <div class="flex justify-between py-2 border-b border-gray-100 text-sm">
                    <span class="text-gray-700">الأرباح المحتجزة</span>
                    <span class="font-bold tabular-nums">{{ $money($retainedEarnings) }}</span>
                </div>
                <div class="flex justify-between py-2 px-3 mt-2 mb-4 rounded-lg bg-blue-50 border border-blue-100 text-sm">
                    <span class="font-bold text-blue-800">إجمالي حقوق الملكية</span>
                    <span class="font-bold text-blue-800 tabular-nums">{{ $money($totalEquity) }}</span>
                </div>

                <div class="flex justify-between py-3 px-4 rounded-xl bg-blue-50 border border-blue-100">
                    <span class="font-bold text-blue-900">إجمالي الخصوم وحقوق الملكية</span>
                    <span class="font-bold text-blue-900 tabular-nums">{{ $money($totalLiabilitiesEquity) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 p-6 text-center">
        @if($isBalanced)
        <div class="inline-flex items-center gap-2 text-green-700 font-bold text-lg">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            الميزانية متوازنة
        </div>
        <p class="text-sm text-gray-600 mt-2">إجمالي الأصول = إجمالي الخصوم وحقوق الملكية</p>
        @else
        <div class="inline-flex items-center gap-2 text-red-700 font-bold text-lg">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            الميزانية غير متوازنة
        </div>
        <p class="text-sm text-gray-600 mt-2">الفرق: {{ $money(abs($totalAssets - $totalLiabilitiesEquity)) }}</p>
        @endif
    </div>
</div>

@include('accounting.partials.report-styles')
@endsection
