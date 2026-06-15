@extends('layouts.app')
@section('page-title', 'تقارير العمليات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $periodLabels = config('operations_reports.period_types');
@endphp

@include('crm.partials.page-header', [
    'title' => $resolver->isAdmin() ? 'تقارير مديري العمليات' : 'تقاريري الدورية',
    'subtitle' => 'تقارير تشغيلية يحددها مدير العمليات وتراجعها الإدارة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>@endif

<div class="mb-4 flex flex-wrap gap-2">
    @foreach($periodLabels as $key => $label)
    <a href="{{ route('operations.reports.index', ['period' => $key]) }}"
       class="px-5 py-2.5 rounded-xl text-sm font-bold font-tajawal border-2 {{ $periodType === $key ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 bg-white' }}"
       @if($periodType === $key) style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);" @endif>
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'مرفوعة', 'value' => $stats['submitted'], 'accent' => 'green', 'href' => route('operations.reports.index', ['status' => 'submitted']) . '#page-data', 'linkLabel' => 'عرض المرفوعة'])
    @include('crm.partials.stat-card', ['label' => 'مسودات', 'value' => $stats['draft'], 'accent' => 'amber', 'href' => route('operations.reports.index', ['status' => 'draft']) . '#page-data', 'linkLabel' => 'عرض المسودات'])
    @include('crm.partials.stat-card', ['label' => 'القائمة', 'value' => $reports->total(), 'accent' => 'theme', 'href' => route('operations.reports.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
</div>

@if($resolver->isManager())
<div class="bg-white rounded-2xl shadow-lg border mb-6 p-5 font-tajawal">
    <p class="font-bold text-gray-900 mb-3">إنشاء تقرير {{ $periodLabels[$periodType] ?? '' }}</p>
    <form method="POST" action="{{ route('operations.reports.generate') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <input type="hidden" name="period_type" value="{{ $periodType }}">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">تاريخ الفترة</label>
            <input type="date" name="anchor_date" value="{{ old('anchor_date', today()->toDateString()) }}" max="{{ today()->toDateString() }}" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm" required>
        </div>
        <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-bold" style="background: {{ $themeColor }};">إنشاء / فتح التقرير</button>
    </form>
</div>
@endif

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal" id="page-data">
    <div class="px-5 py-4 border-b font-bold">قائمة التقارير</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                @if($resolver->isAdmin())<th class="p-3 text-right">مدير العمليات</th>@endif
                <th class="p-3 text-right">الفترة</th>
                <th class="p-3 text-right">النوع</th>
                <th class="p-3 text-right">الحالة</th>
                <th class="p-3 text-right">إجراء</th>
            </tr></thead>
            <tbody>
            @forelse($reports as $report)
            <tr class="border-t border-gray-100">
                @if($resolver->isAdmin())<td class="p-3">{{ $report->author?->name }}</td>@endif
                <td class="p-3">{{ $report->periodRangeLabel() }}</td>
                <td class="p-3">{{ $report->periodLabel() }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $report->isSubmitted() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $report->isSubmitted() ? 'مرفوع' : 'مسودة' }}
                    </span>
                </td>
                <td class="p-3"><a href="{{ route('operations.reports.show', $report) }}" class="font-bold" style="color: {{ $themeColor }};">عرض</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="p-6 text-center text-gray-500">لا توجد تقارير بعد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())<div class="p-4">{{ $reports->links() }}</div>@endif
</div>
@endsection
