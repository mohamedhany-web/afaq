@extends('layouts.app')

@section('page-title', 'تقريري اليومي')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'التقرير اليومي',
    'subtitle' => 'أنشئ تقريرك اليومي من بيانات النظام ثم ارفعه لمدير المبيعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
])

@include('crm.daily-reports.partials.alerts')

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'تقاريري', 'value' => $reports->total(), 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'href' => route('crm.daily-reports.index') . '#page-data', 'linkLabel' => 'عرض التقارير'])
    @include('crm.partials.stat-card', ['label' => 'مرفوعة', 'value' => $stats['submitted'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'href' => route('crm.daily-reports.index') . '#page-data', 'linkLabel' => 'عرض التقارير'])
    @include('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['today'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>', 'href' => route('crm.daily-reports.index') . '#page-data', 'linkLabel' => 'عرض التقارير'])
</div>

@include('crm.daily-reports.partials.create-form')

@include('crm.daily-reports.partials.filters', ['showStatusFilter' => true])

@include('crm.daily-reports.partials.list-table', [
    'tableTitle' => 'تقاريري',
    'showEmployeeColumn' => false,
    'emptyMessage' => 'لم تنشئ أي تقرير بعد. اختر التاريخ واضغط «إنشاء التقرير».',
])
@endsection
