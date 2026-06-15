@extends('layouts.app')
@section('page-title', 'تعويضات الفريق')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $myKpi = $myRun->breakdown['kpi']['level']['label'] ?? '—';
@endphp

@include('crm.partials.page-header', [
    'title' => 'تعويضات الفريق',
    'subtitle' => $period->starts_at->locale('ar')->translatedFormat('F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'راتبي الأساسي', 'value' => $money($myRun->base_salary), 'compact' => true, 'href' => route('crm.compensation.dashboard') . '#payroll-details', 'linkLabel' => 'عرض الراتب'])
    @include('crm.partials.stat-card', ['label' => 'درجة KPI', 'value' => round($myRun->kpi_score ?? 0, 1) . '%', 'compact' => true, 'accent' => 'purple', 'href' => route('crm.compensation.dashboard') . '#payroll-details', 'linkLabel' => 'عرض الراتب'])
    @include('crm.partials.stat-card', ['label' => 'أداء الفريق', 'value' => round($myRun->team_score ?? 0, 1) . '%', 'compact' => true, 'accent' => 'green', 'href' => route('crm.compensation.dashboard') . '#payroll-details', 'linkLabel' => 'عرض الراتب'])
    @include('crm.partials.stat-card', ['label' => 'صافي راتبي', 'value' => $money($myRun->net_pay), 'compact' => true, 'accent' => 'theme', 'href' => route('crm.compensation.dashboard') . '#payroll-details', 'linkLabel' => 'عرض الراتب'])
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3" style="{{ $headerStyle }}">
        <h3 class="font-bold text-lg font-tajawal">أداء وتعويضات الفريق</h3>
        <form method="POST" action="{{ route('crm.compensation.payroll.recalculate') }}">@csrf<button type="submit" class="text-sm px-3 py-1.5 rounded-lg border">إعادة حساب الفريق</button></form>
    </div>
    <div class="p-4 overflow-x-auto">
        <table class="min-w-full text-sm font-tajawal">
            <thead><tr class="text-gray-500 border-b"><th class="text-right py-2">الموظف</th><th class="text-center">KPI</th><th class="text-center">عمولة</th><th class="text-center">مكافآت</th><th class="text-center">خصومات</th><th class="text-center">الصافي</th><th></th></tr></thead>
            <tbody>
            @forelse($teamRuns as $tr)
                <tr class="border-b border-gray-100">
                    <td class="py-2">{{ $tr->user?->name }}</td>
                    <td class="text-center">{{ round($tr->kpi_score ?? 0, 1) }}%</td>
                    <td class="text-center">{{ $money($tr->commission_total) }}</td>
                    <td class="text-center">{{ $money($tr->bonus_total) }}</td>
                    <td class="text-center">{{ $money($tr->deduction_total) }}</td>
                    <td class="text-center font-semibold">{{ $money($tr->net_pay) }}</td>
                    <td class="text-left"><a href="{{ route('crm.compensation.payroll.show', $tr) }}" class="text-sm" style="color:{{ $themeColor }}">تفاصيل</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-6 text-center text-gray-500">لا توجد بيانات للفريق بعد</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('crm.compensation.partials.adjustment-form', ['users' => $teamRuns->pluck('user')->filter()])

@if($pendingAdjustments->isNotEmpty())
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 font-tajawal text-sm">
    <strong>طلبات قيد الاعتماد:</strong> {{ $pendingAdjustments->count() }}
</div>
@endif
@endsection
