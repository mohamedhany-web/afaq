@extends('layouts.app')
@section('page-title', 'تحليلات التسويق')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', ['title' => 'تحليلات التسويق', 'subtitle' => 'أداء الحملات والقنوات', 'actionUrl' => route('marketing.reports.index'), 'actionLabel' => 'التقارير الدورية'])

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'الحملات', 'value' => $summary['campaigns'], 'accent' => 'purple'])
    @include('crm.partials.stat-card', ['label' => 'Leads', 'value' => $summary['leads'], 'accent' => 'blue'])
    @include('crm.partials.stat-card', ['label' => 'نشطة', 'value' => $summary['active'], 'accent' => 'green'])
    @include('crm.partials.stat-card', ['label' => 'الميزانية', 'value' => number_format($summary['budget']), 'accent' => 'amber'])
    @include('crm.partials.stat-card', ['label' => 'المصروف', 'value' => number_format($summary['spent']), 'accent' => 'theme'])
    @include('crm.partials.stat-card', ['label' => 'عملاء نشطون', 'value' => $summary['conversion_hint'], 'accent' => 'green'])
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl shadow-lg border p-5 font-tajawal">
        <h3 class="font-bold mb-4">حسب القناة</h3>
        @forelse($byChannel as $row)
        <div class="flex justify-between py-2 border-b text-sm"><span>{{ $row['channel'] }}</span><span>{{ $row['campaigns'] }} حملة · {{ number_format($row['budget']) }} ج.م</span></div>
        @empty<p class="text-gray-500 text-sm">لا بيانات</p>@endforelse
    </div>
    <div class="bg-white rounded-2xl shadow-lg border p-5 font-tajawal">
        <h3 class="font-bold mb-4">أفضل الحملات (Leads)</h3>
        @forelse($topCampaigns as $c)
        <div class="flex justify-between py-2 border-b text-sm"><span>{{ $c->name }}</span><span class="font-bold" style="color:{{ $themeColor }}">{{ $c->leads_count }}</span></div>
        @empty<p class="text-gray-500 text-sm">لا بيانات</p>@endforelse
    </div>
</div>
@endsection
