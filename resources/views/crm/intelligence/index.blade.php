@extends('layouts.app')
@section('page-title', 'تحليلات أداء المبيعات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
@endphp

@include('crm.partials.page-header', [
    'title' => 'تحليلات أداء المبيعات',
    'subtitle' => 'معدلات التحويل · أسباب خسارة الصفقات · أداء الفريق · توقعات الإيرادات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
])

@include('crm.partials.filter-bar')

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'معدل التحويل', 'value' => $funnel['conversion']['lead_to_won'] . '%', 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />', 'href' => '#funnel-section', 'linkLabel' => 'عرض القمع'])
    @include('crm.partials.stat-card', ['label' => 'معدل الإغلاق', 'value' => $funnel['conversion']['deal_close_rate'] . '%', 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won']), 'linkLabel' => 'عرض الصفقات'])
    @include('crm.partials.stat-card', ['label' => 'صفقات خاسرة', 'value' => $funnel['lost_breakdown']['total_lost'], 'accent' => 'red', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_lost', 'show_closed' => 1]), 'linkLabel' => 'عرض الخاسرة'])
    @include('crm.partials.stat-card', ['label' => 'توقع الشهر القادم', 'value' => $money($forecast['forecast'][0]['revenue_forecast'] ?? 0), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />', 'href' => '#forecast-section', 'linkLabel' => 'عرض التوقعات'])
</div>

<div id="funnel-section" class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    {{-- مسار العملاء --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">مسار العملاء (Lead Funnel)</div>
        <div class="p-5 space-y-3">
            @foreach($funnel['client_funnel'] as $step)
                <div>
                    <div class="flex justify-between text-sm font-tajawal mb-1">
                        <span class="text-gray-700">{{ $step['label'] }}</span>
                        <span class="font-bold" style="color: {{ $themeColor }};">{{ $step['count'] }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: {{ $step['percent'] }}%; background: {{ $themeColor }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- أسباب الخسارة --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">توزيع أسباب الخسارة</div>
        <div class="p-5 space-y-3">
            @forelse($funnel['lost_breakdown']['reasons'] as $reason)
                <div>
                    <div class="flex justify-between text-sm font-tajawal mb-1">
                        <span class="text-gray-700">{{ $reason['label'] }}</span>
                        <span class="font-bold text-red-600">{{ $reason['count'] }} <span class="text-gray-400 font-normal">({{ $reason['share'] }}%)</span></span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-red-400" style="width: {{ $reason['percent'] }}%;"></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-6 font-tajawal">لا توجد خسائر مسجّلة بالسبب في هذه الفترة</p>
            @endforelse
        </div>
    </div>
</div>

{{-- أداء المديرين والفرق --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">ذكاء إدارة المبيعات — لكل فريق ومدير</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm font-tajawal">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="text-right px-4 py-3">الفريق</th>
                    <th class="text-right px-4 py-3">المدير</th>
                    <th class="text-right px-4 py-3">سرعة الرد (ساعة)</th>
                    <th class="text-right px-4 py-3">متابعة %</th>
                    <th class="text-right px-4 py-3">معاينات</th>
                    <th class="text-right px-4 py-3">إغلاق %</th>
                    <th class="text-right px-4 py-3">قيمة المسار</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($management['teams'] as $team)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $team['team_name'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $team['manager_name'] }}</td>
                        <td class="px-4 py-3">{{ $team['avg_response_hours'] }}</td>
                        <td class="px-4 py-3">{{ $team['follow_up_rate'] }}%</td>
                        <td class="px-4 py-3">{{ $team['viewings_month'] }}</td>
                        <td class="px-4 py-3">{{ $team['close_rate'] }}%</td>
                        <td class="px-4 py-3 font-bold" style="color: {{ $themeColor }};">{{ $money($team['pipeline_value']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">لا توجد فرق مبيعات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="forecast-section" class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    {{-- التنبؤ --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">محرك التنبؤ — ماذا سيحدث</div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-500 font-tajawal">قيمة المسار الحالي</p>
                    <p class="text-lg font-bold font-tajawal" style="color: {{ $themeColor }};">{{ $money($forecast['pipeline_value']) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-500 font-tajawal">توقع مرجّح (احتمالية)</p>
                    <p class="text-lg font-bold font-tajawal text-green-600">{{ $money($forecast['weighted_forecast']) }}</p>
                </div>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-2 font-tajawal">توقعات الأشهر القادمة</p>
                @foreach($forecast['forecast'] as $month)
                    <div class="flex justify-between py-2 border-b border-gray-50 text-sm font-tajawal">
                        <span>{{ $month['label'] }}</span>
                        <span class="font-bold">{{ $money($month['revenue_forecast']) }} · {{ $month['deals_forecast'] }} صفقة</span>
                    </div>
                @endforeach
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-2 font-tajawal">تحصيلات متوقعة (30 يوم)</p>
                <p class="text-lg font-bold text-blue-600 font-tajawal">{{ $money($forecast['upcoming_collections']) }}</p>
            </div>
        </div>
    </div>

    {{-- صفقات معرّضة للخطر --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">مشاريع وصفقات معرّضة للخطر</div>
        <div class="p-5 space-y-3 max-h-96 overflow-y-auto">
            @forelse($forecast['at_risk_deals'] as $deal)
                <a href="{{ route('crm.pipeline.show', $deal['id']) }}" class="block p-3 rounded-xl border border-amber-100 bg-amber-50/50 hover:bg-amber-50 transition">
                    <div class="flex justify-between gap-2">
                        <span class="font-semibold text-gray-900 text-sm font-tajawal">{{ $deal['client'] ?? '—' }}</span>
                        <span class="text-sm font-bold text-amber-700 font-tajawal">{{ $money($deal['value']) }}</span>
                    </div>
                    <p class="text-xs text-amber-700 mt-1 font-tajawal">{{ $deal['reason'] }}</p>
                    @if($deal['project'])
                        <p class="text-xs text-gray-500 mt-0.5 font-tajawal">{{ $deal['project'] }}</p>
                    @endif
                </a>
            @empty
                <p class="text-gray-400 text-sm text-center py-6 font-tajawal">لا توجد صفقات معرّضة للخطر حالياً</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ما بعد البيع --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <span class="font-bold font-tajawal text-gray-900">ما بعد البيع — شكاوى وصيانة وتسليم</span>
        <div class="flex gap-2 text-xs font-tajawal">
            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800">{{ $postSales['open'] }} مفتوح</span>
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800">{{ $postSales['resolved_month'] }} حُلّ هذا الشهر</span>
        </div>
    </div>
    <div class="p-5">
        @forelse($postSales['recent'] as $case)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <a href="{{ $case->client?->profileUrl() ?? '#' }}" class="font-semibold text-sm font-tajawal hover:underline" style="color: {{ $themeColor }};">{{ $case->client?->name }}</a>
                    <p class="text-sm text-gray-700 font-tajawal">{{ $case->title }}</p>
                    <p class="text-xs text-gray-400 font-tajawal">{{ $case->typeLabel() }} · {{ $case->statusLabel() }}</p>
                </div>
                <span class="text-xs text-gray-400 font-tajawal">{{ $case->created_at->format('Y/m/d') }}</span>
            </div>
        @empty
            <p class="text-gray-400 text-sm text-center py-6 font-tajawal">لا توجد حالات ما بعد البيع — جاهز لتسجيل الشكاوى والصيانة</p>
        @endforelse
    </div>
</div>
@endsection
