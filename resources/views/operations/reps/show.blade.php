@extends('layouts.app')
@section('page-title', __('operations.rep_workspace.title'))

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => $rep->name,
    'subtitle' => __('operations.rep_workspace.under_management'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
    'actionUrl' => route('operations.dashboard'),
    'actionLabel' => __('operations.dashboard_title'),
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6 font-tajawal">
    @include('crm.partials.stat-card', [
        'label' => __('operations.sections.all'),
        'value' => $clientStats['all'],
        'accent' => 'theme',
        'href' => route('operations.clients.index', ['view' => 'data', 'bucket' => 'all', 'sales_rep' => $rep->id]) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ])
    @include('crm.partials.stat-card', [
        'label' => __('operations.sections.new'),
        'value' => $clientStats['new'] ?? 0,
        'accent' => 'blue',
        'href' => route('operations.clients.index', ['view' => 'data', 'bucket' => 'new', 'sales_rep' => $rep->id]) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ])
    @include('crm.partials.stat-card', [
        'label' => __('operations.sections.follow_up'),
        'value' => $clientStats['follow_up'],
        'accent' => 'blue',
        'href' => route('operations.clients.index', ['view' => 'data', 'bucket' => 'follow_up', 'sales_rep' => $rep->id]) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ])
    @include('crm.partials.stat-card', [
        'label' => __('operations.sections.interested'),
        'value' => $clientStats['interested'],
        'accent' => 'purple',
        'href' => route('operations.clients.index', ['view' => 'data', 'bucket' => 'interested', 'sales_rep' => $rep->id]) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ])
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 font-tajawal" id="page-data">
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold">{{ __('operations.rep_workspace.clients') }}</div>
        <ul class="divide-y">
            @forelse($recentClients as $client)
            <li class="px-5 py-3 flex items-center justify-between gap-2">
                <a href="{{ $client->profileUrl() }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $client->name }}</a>
                <span class="text-xs text-gray-500">{{ $client->lead_stage }}</span>
            </li>
            @empty
            <li class="px-5 py-8 text-center text-gray-500 text-sm">{{ __('operations.clients.empty') }}</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold">{{ __('operations.rep_workspace.tasks') }}</div>
        <ul class="divide-y">
            @forelse($tasks as $task)
            <li class="px-5 py-3">
                <a href="{{ route('crm.tasks.show', $task) }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $task->title }}</a>
                <p class="text-xs text-gray-500 mt-1">{{ $task->due_at?->format('Y-m-d H:i') }} · {{ $task->statusLabel() }}</p>
            </li>
            @empty
            <li class="px-5 py-8 text-center text-gray-500 text-sm">—</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
