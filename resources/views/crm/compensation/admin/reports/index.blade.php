@extends('layouts.app')
@section('page-title', 'تقارير التعويضات')

@section('content')
@php $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v); $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', ['title' => 'تقارير الرواتب والتعويضات', 'subtitle' => "$month / $year"])

<form method="GET" class="flex gap-2 mb-6 font-tajawal text-sm">
    <input type="number" name="year" value="{{ $year }}" class="w-24 rounded-lg border-gray-300" min="2020">
    <input type="number" name="month" value="{{ $month }}" class="w-20 rounded-lg border-gray-300" min="1" max="12">
    <button class="px-3 py-1 border rounded-lg">عرض</button>
</form>

<div class="grid md:grid-cols-3 gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الرواتب', 'value' => $money($runs->sum('net_pay')), 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'العمولات', 'value' => $money($runs->sum('commission_total')), 'compact' => true, 'accent' => 'green'])
    @include('crm.partials.stat-card', ['label' => 'المكافآت المعتمدة', 'value' => $money($bonuses->sum('amount')), 'compact' => true, 'accent' => 'amber'])
</div>

<div class="bg-white rounded-2xl border p-5 font-tajawal text-sm mb-6">
    <h3 class="font-bold mb-3">تقرير الرواتب الشهري</h3>
    <table class="min-w-full"><thead><tr class="text-gray-500 border-b"><th class="text-right py-2">الموظف</th><th class="text-center">KPI</th><th class="text-center">عمولة</th><th class="text-center">صافي</th></tr></thead>
        <tbody>@foreach($runs as $r)<tr class="border-b"><td class="py-2">{{ $r->user?->name }}</td><td class="text-center">{{ round($r->kpi_score ?? 0,1) }}%</td><td class="text-center">{{ $money($r->commission_total) }}</td><td class="text-center font-bold">{{ $money($r->net_pay) }}</td></tr>@endforeach</tbody>
    </table>
</div>

<div class="grid md:grid-cols-2 gap-4 text-sm font-tajawal">
    <div class="bg-white rounded-2xl border p-4"><h4 class="font-bold mb-2">المكافآت</h4>@forelse($bonuses as $b)<div class="py-1 border-b">{{ $b->user?->name }}: {{ $money($b->amount) }}</div>@empty<p class="text-gray-500">لا يوجد</p>@endforelse</div>
    <div class="bg-white rounded-2xl border p-4"><h4 class="font-bold mb-2">الخصومات</h4>@forelse($deductions as $d)<div class="py-1 border-b">{{ $d->user?->name }}: {{ $money($d->amount) }}</div>@empty<p class="text-gray-500">لا يوجد</p>@endforelse</div>
</div>

<a href="{{ route('crm.compensation.dashboard') }}" class="inline-block mt-6 text-sm" style="color:{{ $themeColor }}">← العودة</a>
@endsection
