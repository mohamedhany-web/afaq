@extends('layouts.app')
@section('page-title', $campaign->name)

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => $campaign->name,
    'subtitle' => $campaign->channelLabel() . ' — ' . $campaign->statusLabel(),
    'actionUrl' => route('marketing.leads.create', ['campaign_id' => $campaign->id]),
    'actionLabel' => 'إضافة Lead',
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'Leads', 'value' => $campaign->leads_count, 'accent' => 'blue'])
    @include('crm.partials.stat-card', ['label' => 'المهام', 'value' => $campaign->activities_count, 'accent' => 'purple'])
    @include('crm.partials.stat-card', ['label' => 'الميزانية', 'value' => number_format($campaign->budget ?? 0), 'accent' => 'amber'])
    @include('crm.partials.stat-card', ['label' => 'المصروف', 'value' => number_format($campaign->spent_amount ?? 0), 'accent' => 'theme'])
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border p-5 font-tajawal space-y-3">
        <p><strong>المدير:</strong> {{ $campaign->manager?->name ?? '—' }}</p>
        <p><strong>المشروع:</strong> {{ $campaign->project?->name ?? '—' }}</p>
        <p><strong>الفترة:</strong> {{ $campaign->start_date?->format('Y-m-d') ?? '—' }} → {{ $campaign->end_date?->format('Y-m-d') ?? '—' }}</p>
        @if($campaign->description)<p class="text-sm text-gray-600">{{ $campaign->description }}</p>@endif
        <div class="flex gap-2 pt-2">
            @can('edit-marketing')<a href="{{ route('marketing.campaigns.edit', $campaign) }}" class="px-4 py-2 rounded-xl border text-sm">تعديل</a>@endcan
            <a href="{{ route('marketing.activities.create', ['campaign_id' => $campaign->id]) }}" class="px-4 py-2 rounded-xl text-white text-sm" style="background:{{ $themeColor }}">مهمة جديدة</a>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border p-5 font-tajawal">
        <h3 class="font-bold mb-3">آخر المهام</h3>
        @forelse($activities as $act)
        <p class="text-sm mb-2">{{ $act->title }} <span class="text-gray-400">· {{ $act->statusLabel() }}</span></p>
        @empty
        <p class="text-sm text-gray-500">لا مهام.</p>
        @endforelse
    </div>
</div>
@endsection
