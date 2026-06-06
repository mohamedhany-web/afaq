@extends('layouts.app')
@section('page-title', 'كشف الراتب')

@section('content')
@php
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $kpi = $run->breakdown['kpi'] ?? [];
@endphp

@include('crm.partials.page-header', [
    'title' => 'كشف راتب — ' . ($run->user?->name ?? ''),
    'subtitle' => ($run->period?->month ?? '') . '/' . ($run->period?->year ?? ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
])

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 font-tajawal">
    <div class="bg-white p-4 rounded-2xl border"><div class="text-gray-500 text-sm">أساسي</div><div class="font-bold">{{ $money($run->base_salary) }}</div></div>
    <div class="bg-white p-4 rounded-2xl border"><div class="text-gray-500 text-sm">عمولة</div><div class="font-bold">{{ $money($run->commission_total) }}</div></div>
    <div class="bg-white p-4 rounded-2xl border"><div class="text-gray-500 text-sm">مكافآت − خصومات</div><div class="font-bold">{{ $money($run->bonus_total - $run->deduction_total) }}</div></div>
    <div class="bg-white p-4 rounded-2xl border"><div class="text-gray-500 text-sm">الصافي</div><div class="font-bold text-lg" style="color:{{ $themeColor }}">{{ $money($run->net_pay) }}</div></div>
</div>

<div class="bg-white rounded-2xl border shadow-lg p-5 mb-6">
    <h3 class="font-bold mb-3">بنود الكشف</h3>
    <table class="min-w-full text-sm font-tajawal">
        <thead><tr class="text-gray-500 border-b"><th class="text-right py-2">البند</th><th class="text-center">الفئة</th><th class="text-left">المبلغ</th></tr></thead>
        <tbody>
        @foreach($run->lineItems as $line)
            <tr class="border-b"><td class="py-2">{{ $line->label }}</td><td class="text-center">{{ $line->category }}</td><td class="text-left {{ $line->amount < 0 ? 'text-red-600' : '' }}">{{ $money($line->amount) }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>

@if(!empty($kpi['items']))
<div class="bg-white rounded-2xl border p-5 font-tajawal text-sm">
    <h3 class="font-bold mb-2">KPI — {{ round($kpi['overall_score'] ?? 0, 1) }}% ({{ $kpi['level']['label'] ?? '' }})</h3>
    @foreach($kpi['items'] as $item)
        <div class="flex justify-between py-1 border-b border-gray-50"><span>{{ $item['name'] }}</span><span>{{ round($item['achievement'] ?? 0, 1) }}%</span></div>
    @endforeach
</div>
@endif

<a href="{{ route('crm.compensation.dashboard') }}" class="inline-block mt-6 text-sm font-tajawal" style="color:{{ $themeColor }}">← العودة</a>
@endsection
