@extends('layouts.app')
@section('page-title', 'انضباط الموظفين')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $statusColors = ['green' => 'bg-green-100 text-green-800', 'blue' => 'bg-blue-100 text-blue-800', 'amber' => 'bg-amber-100 text-amber-800', 'red' => 'bg-red-100 text-red-800'];
@endphp

@include('crm.partials.page-header', array_filter([
    'title' => $mode === 'manager' ? 'انضباط الفريق والالتزام' : 'التزامي بالنظام',
    'subtitle' => 'التقارير · الحضور · الإجازات · المهام · العقوبات التلقائية',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'actionUrl' => auth()->user()?->hasRole(['super_admin', 'admin']) ? route('admin.auto-penalties.index') : null,
    'actionLabel' => auth()->user()?->hasRole(['super_admin', 'admin']) ? 'قواعد العقوبات' : null,
]))

@if($mode === 'manager')
@include('crm.partials.filter-bar')
@else
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end font-tajawal">
        <div><label class="text-xs font-bold text-gray-500 mb-1 block">من</label><input type="date" name="from" value="{{ $start->toDateString() }}" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm"></div>
        <div><label class="text-xs font-bold text-gray-500 mb-1 block">إلى</label><input type="date" name="to" value="{{ $end->toDateString() }}" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm"></div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold" style="background:linear-gradient(135deg,{{ $themeColor }} 0%,{{ $themeColor }}dd 100%);">تطبيق</button>
    </form>
</div>
@endif

@if($mode === 'manager')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'حجم الفريق', 'value' => $stats['team_size'], 'accent' => 'theme', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
    @include('crm.partials.stat-card', ['label' => 'ملتزمون', 'value' => $stats['excellent'], 'accent' => 'green', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
    @include('crm.partials.stat-card', ['label' => 'يحتاج متابعة', 'value' => $stats['critical'], 'accent' => 'amber', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
    @include('crm.partials.stat-card', ['label' => 'عقوبات الشهر', 'value' => $money($stats['penalties_month']), 'accent' => 'red', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
</div>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">ملخص الفريق</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px] font-tajawal">
            <thead class="bg-gray-50 border-b"><tr class="text-gray-600">
                <th class="text-right p-4">الموظف</th>
                <th class="text-center p-4">التقييم</th>
                <th class="text-center p-4">تقارير</th>
                <th class="text-center p-4">حضور</th>
                <th class="text-center p-4">إجازات</th>
                <th class="text-center p-4">مهام متأخرة</th>
                <th class="text-center p-4">عقوبات</th>
                <th class="text-center p-4"></th>
            </tr></thead>
            <tbody class="divide-y">
                @foreach($overview as $row)
                <tr class="hover:bg-gray-50/80">
                    <td class="p-4 font-semibold">{{ $row['name'] }}</td>
                    <td class="p-4 text-center">
                        <span class="text-xs px-2 py-1 rounded-full font-bold {{ $statusColors[$row['status']['color']] ?? 'bg-gray-100' }}">{{ $row['overall_score'] }}% — {{ $row['status']['label'] }}</span>
                    </td>
                    <td class="p-4 text-center tabular-nums">{{ $row['reports']['percent'] }}%</td>
                    <td class="p-4 text-center tabular-nums">{{ $row['attendance_compliance'] }}%</td>
                    <td class="p-4 text-center tabular-nums">{{ $row['period']['leave_days'] }}</td>
                    <td class="p-4 text-center tabular-nums">{{ $row['overdue_tasks'] + $row['overdue_follow_ups'] }}</td>
                    <td class="p-4 text-center tabular-nums">{{ $money($row['penalties_total']) }}</td>
                    <td class="p-4 text-center"><a href="{{ route('crm.employee-compliance.show', $row['user']) }}" class="text-xs font-bold" style="color:{{ $themeColor }}">تفاصيل</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
@php $s = $self; @endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'التقييم الإجمالي', 'value' => $s['overall_score'].'%', 'accent' => $s['status']['color'] === 'green' ? 'green' : 'amber', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
    @include('crm.partials.stat-card', ['label' => 'التقارير', 'value' => $s['reports']['submitted'].' / '.$s['reports']['expected'], 'accent' => 'blue', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
    @include('crm.partials.stat-card', ['label' => 'الحضور', 'value' => $s['attendance_compliance'].'%', 'accent' => 'purple', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
    @include('crm.partials.stat-card', ['label' => 'أيام إجازة', 'value' => $s['period']['leave_days'], 'accent' => 'theme', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border p-5 font-tajawal text-sm">
        <h3 class="font-bold mb-3">ملاحظات الالتزام</h3>
        @forelse($s['flags'] as $flag)
        <div class="py-2 border-b text-amber-800">• {{ $flag }}</div>
        @empty
        <p class="text-gray-500">لا ملاحظات — أداء جيد</p>
        @endforelse
    </div>
    <div class="bg-white rounded-2xl border p-5 font-tajawal text-sm">
        <h3 class="font-bold mb-3">إجازات قادمة</h3>
        @forelse($leaves as $leave)
        <div class="py-2 border-b">{{ $leave->leave_type_name }}: {{ $leave->start_date->format('Y/m/d') }} — {{ $leave->end_date->format('Y/m/d') }}</div>
        @empty
        <p class="text-gray-500">لا إجازات معتمدة قادمة</p>
        @endforelse
    </div>
</div>
@endif

<div class="mt-6 p-4 rounded-2xl border text-xs text-gray-500 font-tajawal" style="border-color:{{ $themeColor }}25;background:{{ $themeColor }}05;">
    <strong>كيف يعمل النظام:</strong> أيام الإجازة المعتمدة لا تُحسب ضمنك في التقارير ولا تُطبّق عليك عقوبات التأخر. العقوبات التلقائية تُسجّل في كشف الرواتب وتُطبّق كل ساعة عبر النظام.
</div>
@endsection
