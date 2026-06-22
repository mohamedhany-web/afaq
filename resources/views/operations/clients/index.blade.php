@extends('layouts.app')
@section('page-title', __('operations.clients.hub_title'))

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => __('operations.clients.hub_title'),
    'subtitle' => __('operations.clients.hub_subtitle'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
    'actionUrl' => route('crm.clients.create'),
    'actionLabel' => __('operations.clients.new_client'),
])

@include('operations.clients.partials.tabs')

<div class="mb-4 flex flex-wrap gap-2 font-tajawal">
    <a href="{{ route('crm.clients.create', ['tab' => 'import']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold border-2 hover:bg-gray-50"
       style="border-color: {{ $themeColor }}40; color: {{ $themeColor }};">
        {{ __('operations.clients.import_excel') }} / CSV
    </a>
    <a href="{{ route('crm.clients.import.template') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100">
        {{ __('operations.clients.download_template') }}
    </a>
</div>

<div id="page-data">
@if(($view ?? 'data') === 'distribution')
    @include('operations.clients.partials.distribution-panel')
@else
    <div class="flex flex-wrap gap-2 mb-4 font-tajawal">
        @foreach($bucketLabels as $key => $label)
        <a href="{{ route('operations.clients.index', array_filter(['bucket' => $key, 'search' => $search ?: null, 'employee_id' => request('employee_id')])) }}#page-data"
           class="text-xs font-bold px-3 py-2 rounded-xl border transition-colors {{ $bucket === $key ? 'text-white border-transparent' : 'text-gray-600 bg-white hover:bg-gray-50' }}"
           @if($bucket === $key) style="background:{{ $themeColor }}" @endif>
            {{ $label }}
            <span class="opacity-80">({{ number_format($bucketCounts[$key] ?? 0) }})</span>
        </a>
        @endforeach
    </div>

    <form method="GET" class="mb-4 flex gap-2 font-tajawal">
        <input type="hidden" name="bucket" value="{{ $bucket }}">
        @if(request('employee_id'))
        <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
        @endif
        <input type="search" name="search" value="{{ $search }}" placeholder="{{ __('operations.actions.search') }}..."
               class="flex-1 border rounded-xl px-4 py-2.5 text-sm">
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">
            {{ __('operations.actions.search') }}
        </button>
    </form>

    <div class="bg-white rounded-2xl border overflow-hidden font-tajawal">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <p class="font-bold">{{ $bucketLabels[$bucket] ?? $bucket }}</p>
            <p class="text-xs text-gray-500">{{ number_format($clients->total()) }} {{ __('operations.clients.results') }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-right">{{ __('operations.clients.client') }}</th>
                        <th class="p-3 text-right">{{ __('operations.clients.phone') }}</th>
                        <th class="p-3 text-right">{{ __('operations.clients.stage') }}</th>
                        <th class="p-3 text-right">{{ __('operations.clients.assigned') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($clients as $client)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">
                            <a href="{{ $client->profileUrl() }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $client->name }}</a>
                        </td>
                        <td class="p-3 text-gray-600" dir="ltr">{{ $client->phone }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded-lg bg-gray-100">{{ $client->lead_stage ?? '—' }}</span>
                        </td>
                        <td class="p-3 text-gray-600">
                            {{ $client->assignedEmployee ? trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name) : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">{{ __('operations.clients.empty') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($clients->hasPages())
        <div class="p-4 border-t">{{ $clients->links() }}</div>
        @endif
    </div>
@endif
</div>
@endsection
