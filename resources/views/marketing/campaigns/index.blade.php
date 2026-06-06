@extends('layouts.app')
@section('page-title', 'حملات التسويق')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'حملات التسويق',
    'subtitle' => 'إدارة الحملات والقنوات والميزانيات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 4h10m-10 0a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V10a2 2 0 00-2-2" />',
    'actionUrl' => auth()->user()->can('create-marketing') ? route('marketing.campaigns.create') : null,
    'actionLabel' => 'حملة جديدة',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الحملات', 'value' => $stats['total'], 'accent' => 'purple'])
    @include('crm.partials.stat-card', ['label' => 'نشطة', 'value' => $stats['active'], 'accent' => 'green'])
    @include('crm.partials.stat-card', ['label' => 'Leads', 'value' => $stats['leads'], 'accent' => 'blue'])
    @include('crm.partials.stat-card', ['label' => 'الميزانية', 'value' => number_format($stats['budget']), 'accent' => 'amber'])
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..." class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
    <select name="status" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
        <option value="">كل الحالات</option>
        @foreach(config('marketing.campaign_statuses') as $k => $l)
        <option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-tajawal" style="background: {{ $themeColor }};">تصفية</button>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($campaigns as $campaign)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 font-tajawal">
        <div class="flex justify-between items-start gap-2 mb-3">
            <h3 class="font-bold text-gray-900">{{ $campaign->name }}</h3>
            <span class="text-xs px-2 py-1 rounded-lg bg-purple-50 text-purple-700">{{ $campaign->statusLabel() }}</span>
        </div>
        <p class="text-xs text-gray-500 mb-3">{{ $campaign->channelLabel() }} @if($campaign->project) · {{ $campaign->project->name }} @endif</p>
        <div class="flex justify-between text-sm mb-4">
            <span>{{ $campaign->leads_count }} lead</span>
            <span>{{ number_format($campaign->budget ?? 0) }} ج.م</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('marketing.campaigns.show', $campaign) }}" class="flex-1 text-center py-2 rounded-xl text-xs font-bold text-white" style="background: {{ $themeColor }};">عرض</a>
            @can('edit-marketing')
            <a href="{{ route('marketing.campaigns.edit', $campaign) }}" class="px-3 py-2 rounded-xl text-xs border border-gray-200">تعديل</a>
            @endcan
        </div>
    </div>
    @empty
    <p class="col-span-full text-center text-gray-500 font-tajawal py-10">لا توجد حملات.</p>
    @endforelse
</div>
<div class="mt-6">{{ $campaigns->links() }}</div>
@endsection
