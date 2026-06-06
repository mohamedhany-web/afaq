@extends('layouts.app')
@section('page-title', 'لوحتي — التسويق')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); $k = $kpis; @endphp

@include('crm.partials.page-header', [
    'title' => 'لوحة موظف التسويق',
    'subtitle' => now()->locale('ar')->translatedFormat('l، d F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />',
    'actionUrl' => route('marketing.reports.index'),
    'actionLabel' => 'تقريري اليومي',
])

@if(!empty($reportPending) && count($reportPending))
<div class="mb-6 p-4 rounded-2xl border-2 border-red-200 bg-red-50 font-tajawal">
    <p class="font-bold text-red-900 text-sm">التقرير اليومي إلزامي — لم يُرفع بعد</p>
    <a href="{{ route('marketing.reports.index') }}" class="inline-block mt-2 px-4 py-2 rounded-xl text-white text-xs font-bold" style="background:#7c3aed">رفع الآن</a>
</div>
@endif

<div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'مهام اليوم', 'value' => $k['activities_today'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'])
    @include('crm.partials.stat-card', ['label' => 'Leads اليوم', 'value' => $k['leads_today'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'])
    @include('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $k['activities_overdue'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'])
    @include('crm.partials.stat-card', ['label' => 'دورية نشطة', 'value' => $k['recurring_active'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />'])
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">مهامي القادمة</div>
    <div class="divide-y divide-gray-100">
        @forelse($upcomingActivities as $activity)
        <div class="px-5 py-4 font-tajawal flex justify-between gap-3">
            <div>
                <p class="font-semibold">{{ $activity->title }}</p>
                <p class="text-xs text-gray-500">{{ $activity->due_at?->locale('ar')->translatedFormat('d M — H:i') }}</p>
            </div>
            <a href="{{ route('marketing.activities.index') }}" class="text-xs font-bold" style="color: {{ $themeColor }};">عرض</a>
        </div>
        @empty
        <p class="p-5 text-sm text-gray-500">لا مهام قادمة.</p>
        @endforelse
    </div>
</div>
@endsection
