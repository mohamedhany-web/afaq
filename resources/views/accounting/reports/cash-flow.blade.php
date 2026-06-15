@extends('layouts.app')

@php
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';
    $reportStartDate = \Carbon\Carbon::parse($startDate);
    $reportEndDate = \Carbon\Carbon::parse($endDate);
@endphp

@section('page-title', 'قائمة التدفق النقدي')

@section('content')
@include('accounting.partials.report-header', [
    'title' => 'قائمة التدفق النقدي',
    'subtitle' => 'من ' . $reportStartDate->format('Y/m/d') . ' إلى ' . $reportEndDate->format('Y/m/d'),
])

@include('accounting.partials.report-toolbar', [
    'filterType' => 'range',
    'startDate' => $startDate,
    'endDate' => $endDate,
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 no-print">
    @include('crm.partials.stat-card', ['label' => 'تشغيلي', 'value' => ($operatingCashFlow >= 0 ? '+' : '-') . $money(abs($operatingCashFlow)), 'accent' => $operatingCashFlow >= 0 ? 'green' : 'red', 'compact' => true, 'href' => route('accounting.reports.cash-flow') . '#page-data', 'linkLabel' => 'عرض التقرير'])
    @include('crm.partials.stat-card', ['label' => 'استثماري', 'value' => ($investingCashFlow >= 0 ? '+' : '-') . $money(abs($investingCashFlow)), 'accent' => $investingCashFlow >= 0 ? 'purple' : 'red', 'compact' => true, 'href' => route('accounting.reports.cash-flow') . '#page-data', 'linkLabel' => 'عرض التقرير'])
    @include('crm.partials.stat-card', ['label' => 'تمويلي', 'value' => ($financingCashFlow >= 0 ? '+' : '-') . $money(abs($financingCashFlow)), 'accent' => $financingCashFlow >= 0 ? 'blue' : 'red', 'compact' => true, 'href' => route('accounting.reports.cash-flow') . '#page-data', 'linkLabel' => 'عرض التقرير'])
    @include('crm.partials.stat-card', ['label' => 'صافي التغير', 'value' => ($netCashFlow >= 0 ? '+' : '-') . $money(abs($netCashFlow)), 'accent' => $netCashFlow >= 0 ? 'theme' : 'red', 'compact' => true, 'href' => route('accounting.reports.cash-flow') . '#page-data', 'linkLabel' => 'عرض التقرير'])
</div>

<div id="report-document" class="font-tajawal">
    <div class="report-print-header text-center mb-6 pb-4 border-b-2 border-gray-900">
        <h2 class="text-xl font-bold">@include('accounting.partials.company-name')</h2>
        <h3 class="text-lg font-bold mt-3">قائمة التدفق النقدي</h3>
        <p class="text-sm text-gray-700">من {{ $reportStartDate->format('Y/m/d') }} إلى {{ $reportEndDate->format('Y/m/d') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 no-print">
        <div class="px-6 py-5 text-white text-center" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);">
            <h2 class="text-xl font-bold">@include('accounting.partials.company-name')</h2>
            <h3 class="text-base font-semibold mt-1 opacity-95">قائمة التدفق النقدي</h3>
            <p class="text-sm opacity-90 mt-1">من {{ $reportStartDate->format('Y/m/d') }} إلى {{ $reportEndDate->format('Y/m/d') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        @php
            $sections = [
                ['title' => 'الأنشطة التشغيلية', 'amount' => $operatingCashFlow, 'bg' => 'bg-blue-50 text-blue-900', 'totalBg' => 'bg-blue-50 border-blue-100 text-blue-800'],
                ['title' => 'الأنشطة الاستثمارية', 'amount' => $investingCashFlow, 'bg' => 'bg-purple-50 text-purple-900', 'totalBg' => 'bg-purple-50 border-purple-100 text-purple-800'],
                ['title' => 'الأنشطة التمويلية', 'amount' => $financingCashFlow, 'bg' => 'bg-green-50 text-green-900', 'totalBg' => 'bg-green-50 border-green-100 text-green-800'],
            ];
        @endphp

        @foreach($sections as $section)
        <div class="border-b border-gray-100 last:border-b-0">
            <div class="{{ $sectionHeader }} {{ $section['bg'] }}">{{ $section['title'] }}</div>
            <div class="p-5 sm:p-6">
                <div class="flex justify-between py-2 text-sm">
                    <span class="text-gray-700">صافي التدفق</span>
                    <span class="font-bold tabular-nums {{ $section['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $section['amount'] >= 0 ? '+' : '-' }}{{ $money(abs($section['amount'])) }}
                    </span>
                </div>
                <div class="flex justify-between py-3 mt-3 px-4 rounded-xl border {{ $section['totalBg'] }}">
                    <span class="font-bold">صافي {{ $section['title'] }}</span>
                    <span class="font-bold tabular-nums {{ $section['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $section['amount'] >= 0 ? '+' : '-' }}{{ $money(abs($section['amount'])) }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach

        <div class="p-5 sm:p-6" style="background: {{ $themeColor }}08;">
            <div class="space-y-3">
                <div class="flex justify-between py-2">
                    <span class="font-bold text-gray-900">صافي التغير في النقدية</span>
                    <span class="font-bold tabular-nums {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $netCashFlow >= 0 ? '+' : '-' }}{{ $money(abs($netCashFlow)) }}
                    </span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-200 text-sm">
                    <span class="text-gray-600">النقدية في بداية الفترة</span>
                    <span class="font-bold tabular-nums">{{ $money($beginningCash) }}</span>
                </div>
                <div class="flex justify-between py-4 px-4 rounded-xl text-white font-bold"
                     style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                    <span>النقدية في نهاية الفترة</span>
                    <span class="text-lg tabular-nums">{{ $money($endingCash) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@include('accounting.partials.report-styles')
@endsection
