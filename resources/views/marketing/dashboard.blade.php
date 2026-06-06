@extends('layouts.app')
@section('page-title', 'لوحة التسويق')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); $k = $kpis; @endphp

@include('crm.partials.page-header', [
    'title' => $isManager ? 'لوحة مدير التسويق' : 'لوحة التسويق',
    'subtitle' => $role . ' — ' . now()->locale('ar')->translatedFormat('l، d F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />',
    'actionUrl' => route('marketing.reports.index'),
    'actionLabel' => 'التقارير الدورية',
])

@if(!empty($reportPending) && count($reportPending))
<div class="mb-6 p-5 rounded-2xl border-2 border-red-200 bg-red-50 font-tajawal">
    <p class="font-bold text-red-900 mb-2">تقارير إلزامية مطلوبة</p>
    <p class="text-sm text-red-800 mb-3">يجب رفع التقارير التالية قبل نهاية اليوم/الأسبوع/الشهر.</p>
    <a href="{{ route('marketing.reports.index') }}" class="inline-flex px-5 py-2 rounded-xl text-white text-sm font-bold" style="background:#7c3aed">رفع التقارير الآن</a>
</div>
@endif

@if($isManager && !empty($teamDailyStatus))
@php $missingTeam = collect($teamDailyStatus)->where('submitted', false)->count(); @endphp
@if($missingTeam > 0)
<div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm font-tajawal">
    <strong>{{ $missingTeam }}</strong> من فريق التسويق لم يرفعوا تقرير اليوم بعد.
    <a href="{{ route('marketing.reports.index', ['period' => 'daily']) }}" class="text-purple-700 font-bold mr-2">متابعة</a>
</div>
@endif
@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'حملات نشطة', 'value' => $k['active_campaigns'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 4h10m-10 0a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V10a2 2 0 00-2-2" />'])
    @include('crm.partials.stat-card', ['label' => 'Leads الشهر', 'value' => $k['leads_month'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'])
    @include('crm.partials.stat-card', ['label' => 'مهام اليوم', 'value' => $k['activities_today'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'])
    @include('crm.partials.stat-card', ['label' => 'مهام متأخرة', 'value' => $k['activities_overdue'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'])
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">أحدث الحملات</div>
        <div class="divide-y divide-gray-100">
            @forelse($recentCampaigns as $campaign)
            <a href="{{ route('marketing.campaigns.show', $campaign) }}" class="block px-5 py-4 hover:bg-gray-50 font-tajawal">
                <div class="flex justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $campaign->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $campaign->channelLabel() }} · {{ $campaign->statusLabel() }}</p>
                    </div>
                    <span class="text-sm font-bold" style="color: {{ $themeColor }};">{{ $campaign->leads_count }} lead</span>
                </div>
            </a>
            @empty
            <p class="p-5 text-sm text-gray-500 font-tajawal">لا توجد حملات بعد.</p>
            @endforelse
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">مهام قادمة</div>
        <div class="divide-y divide-gray-100">
            @forelse($upcomingActivities as $activity)
            <div class="px-5 py-4 font-tajawal">
                <p class="font-semibold text-gray-900">{{ $activity->title }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $activity->due_at?->locale('ar')->translatedFormat('d M — H:i') }} · {{ $activity->assignee?->name }}</p>
            </div>
            @empty
            <p class="p-5 text-sm text-gray-500 font-tajawal">لا مهام قادمة.</p>
            @endforelse
        </div>
    </div>
</div>

@if($overdueActivities->isNotEmpty())
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 font-tajawal">
    <p class="font-bold text-amber-900 mb-2">مهام متأخرة ({{ $overdueActivities->count() }})</p>
    <ul class="space-y-1 text-sm text-amber-800">
        @foreach($overdueActivities as $activity)
        <li>{{ $activity->title }} — {{ $activity->due_at?->diffForHumans() }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection
