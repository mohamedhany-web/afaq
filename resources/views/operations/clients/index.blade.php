@extends('layouts.app')
@section('page-title', __('operations.clients.hub_title'))

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $clientsRoutePrefix = $clientsRoutePrefix ?? 'operations.clients';
    $cr = fn (string $action, mixed $params = []) => route($clientsRoutePrefix . '.' . $action, $params);
@endphp

@include('crm.partials.page-header', [
    'title' => __('operations.clients.hub_title'),
    'subtitle' => __('operations.clients.hub_subtitle'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
    'actionUrl' => $cr('create'),
    'actionLabel' => __('operations.clients.new_client'),
])

@include('operations.clients.partials.tabs')

<div class="mb-4 flex flex-wrap gap-2 font-tajawal">
    <a href="{{ $cr('create', ['tab' => 'import']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold border-2 hover:bg-gray-50"
       style="border-color: {{ $themeColor }}40; color: {{ $themeColor }};">
        {{ __('operations.clients.import_excel') }} / CSV
    </a>
    <a href="{{ $cr('import.template') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100">
        {{ __('operations.clients.download_template') }}
    </a>
</div>

@if(($view ?? 'data') === 'distribution')
    @include('operations.clients.partials.distribution-panel')
@else
    @include('operations.clients.partials.data-panel')
@endif
@endsection
