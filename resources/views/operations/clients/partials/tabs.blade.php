@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $dataTabUrl = route('operations.clients.index', array_filter(['bucket' => $bucket ?? 'all', 'search' => ($search ?? '') ?: null]));
    $distributionTabUrl = route('operations.clients.index', ['view' => 'distribution', 'filter' => $filter ?? 'unassigned']);
@endphp
<div class="mb-4 font-tajawal">
    <div class="flex flex-wrap gap-2 mb-3">
        <a href="{{ $dataTabUrl }}#page-data"
           class="text-sm font-bold px-4 py-2.5 rounded-xl border transition-colors {{ ($view ?? 'data') === 'data' ? 'text-white border-transparent' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
           @if(($view ?? 'data') === 'data') style="background:{{ $themeColor }}" @endif>
            {{ __('operations.clients.tab_data') }}
        </a>
        <a href="{{ $distributionTabUrl }}#page-data"
           class="text-sm font-bold px-4 py-2.5 rounded-xl border transition-colors inline-flex items-center gap-2 {{ ($view ?? 'data') === 'distribution' ? 'text-white border-transparent' : 'text-gray-700 bg-white hover:bg-gray-50' }}"
           @if(($view ?? 'data') === 'distribution') style="background:{{ $themeColor }}" @endif>
            {{ __('operations.clients.tab_distribution') }}
            @if(($unassignedCount ?? 0) > 0)
            <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ ($view ?? 'data') === 'distribution' ? 'bg-white/25' : 'bg-amber-100 text-amber-800' }}">{{ $unassignedCount }}</span>
            @endif
        </a>
    </div>
    <div class="rounded-xl border border-blue-100 bg-blue-50/80 px-4 py-3 text-xs text-blue-900 leading-relaxed">
        <p class="font-bold mb-1">{{ __('operations.clients.roles_title') }}</p>
        <p><span class="font-semibold">{{ __('operations.clients.tab_data') }}:</span> {{ __('operations.clients.role_data') }}</p>
        <p class="mt-1"><span class="font-semibold">{{ __('operations.clients.tab_distribution') }}:</span> {{ __('operations.clients.role_distribution') }}</p>
    </div>
</div>
