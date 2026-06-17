@extends('layouts.app')
@section('page-title', __('operations.actions.search_sales_rep'))

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => __('operations.actions.search_sales_rep'),
    'subtitle' => __('operations.rep_workspace.under_management'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
    'actionUrl' => route('operations.dashboard'),
    'actionLabel' => __('operations.dashboard_title'),
])

<form method="GET" action="{{ route('operations.reps.search') }}" class="mb-6 flex gap-2 font-tajawal">
    <input type="search" name="q" value="{{ $q }}" autofocus
           placeholder="{{ __('operations.actions.search_sales_rep_placeholder') }}"
           class="flex-1 border rounded-xl px-4 py-3 text-sm">
    <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">
        {{ __('operations.actions.search') }}
    </button>
</form>

<div class="bg-white rounded-2xl border divide-y font-tajawal" id="page-data">
    @forelse($reps as $rep)
    <a href="{{ route('operations.reps.show', $rep) }}"
       class="flex items-center justify-between gap-4 px-5 py-4 hover:bg-gray-50 transition-colors">
        <div>
            <p class="font-bold text-gray-900">{{ $rep->name }}</p>
            <p class="text-xs text-gray-500">{{ $rep->employee?->department?->name ?? '—' }}</p>
        </div>
        <span class="text-xs font-bold" style="color:{{ $themeColor }}">{{ __('operations.actions.open_rep_workspace') }} ←</span>
    </a>
    @empty
    <p class="p-8 text-center text-gray-500">{{ __('operations.rep_workspace.no_results') }}</p>
    @endforelse
</div>
@endsection
