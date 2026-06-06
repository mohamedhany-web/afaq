@extends('layouts.app')
@section('page-title', 'التقارير الدورية')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $isManager = $resolver->isManager() || $resolver->isAdmin();
    $periodLabels = config('marketing_reports.period_types');
@endphp

@include('crm.partials.page-header', [
    'title' => 'التقارير الدورية — التسويق',
    'subtitle' => $isManager ? 'تقاريرك الإلزامية ومتابعة فريق التسويق' : 'التقرير اليومي الإلزامي',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    'actionUrl' => route('marketing.analytics.index'),
    'actionLabel' => 'تحليلات الأداء',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>@endif

@if(count($myPending))
<div class="mb-6 p-5 rounded-2xl border-2 border-amber-300 bg-amber-50 font-tajawal">
    <p class="font-bold text-amber-900 mb-3">تقارير إلزامية لم تُرفع بعد</p>
    <div class="flex flex-wrap gap-2">
        @foreach($myPending as $item)
        <a href="{{ $item['report'] ? route('marketing.reports.show', $item['report']) : route('marketing.reports.index', ['period' => $item['type']]) }}"
           class="px-4 py-2 rounded-xl text-sm font-bold {{ $item['status'] === 'missing' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-900' }}">
            تقرير {{ $item['label'] }} — {{ $item['status'] === 'missing' ? 'لم يُنشأ' : 'مسودة' }}
        </a>
        @endforeach
    </div>
</div>
@endif

<div class="mb-4 flex flex-wrap gap-2">
    @foreach($periodLabels as $key => $label)
    <a href="{{ route('marketing.reports.index', ['period' => $key]) }}"
       class="px-5 py-2.5 rounded-xl text-sm font-bold font-tajawal border-2 {{ $periodType === $key ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 bg-white' }}"
       @if($periodType === $key) style="background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);" @endif>
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'مرفوعة', 'value' => $stats['submitted'], 'accent' => 'green'])
    @include('crm.partials.stat-card', ['label' => 'مسودات', 'value' => $stats['draft'], 'accent' => 'amber'])
    @include('crm.partials.stat-card', ['label' => 'القائمة', 'value' => $reports->total(), 'accent' => 'purple'])
</div>

@include('marketing.period-reports.partials.create-form', ['periodType' => $periodType, 'isManager' => $isManager])

@if($isManager && $periodType === 'daily' && count($teamDailyStatus))
<div class="bg-white rounded-2xl shadow-lg border mb-6 overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b font-bold" style="background: linear-gradient(135deg, #7c3aed08 0%, transparent 100%);">التزام الفريق — تقرير اليوم</div>
    <div class="divide-y">
        @foreach($teamDailyStatus as $row)
        <div class="px-5 py-3 flex justify-between items-center gap-3">
            <span class="font-semibold">{{ $row['user']->name }}</span>
            @if($row['submitted'])
            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">مرفوع</span>
            @else
            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800">لم يُرفع</span>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b font-bold">قائمة التقارير — {{ $periodLabels[$periodType] ?? '' }}</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                @if($isManager)<th class="p-3 text-right">الموظف</th>@endif
                <th class="p-3 text-right">الفترة</th>
                <th class="p-3 text-right">الحالة</th>
                <th class="p-3 text-right">إجراء</th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($reports as $r)
                <tr class="hover:bg-gray-50">
                    @if($isManager)<td class="p-3">{{ $r->author?->name }}</td>@endif
                    <td class="p-3">{{ $r->periodRangeLabel() }}</td>
                    <td class="p-3">
                        <span class="text-xs px-2 py-1 rounded-full {{ $r->isSubmitted() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $r->isSubmitted() ? 'مرفوع' : 'مسودة' }}
                        </span>
                    </td>
                    <td class="p-3">
                        <a href="{{ route('marketing.reports.show', $r) }}" class="text-xs font-bold" style="color:#7c3aed">عرض</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $isManager ? 4 : 3 }}" class="p-8 text-center text-gray-500">لا تقارير في هذه الفترة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())<div class="p-4 border-t">{{ $reports->links() }}</div>@endif
</div>
@endsection
