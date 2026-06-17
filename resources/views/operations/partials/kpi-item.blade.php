@php
    $statusColors = ['excellent' => 'text-green-700 bg-green-50', 'good' => 'text-blue-700 bg-blue-50', 'warning' => 'text-amber-700 bg-amber-50', 'critical' => 'text-red-700 bg-red-50'];
    $badge = $statusColors[$item['status']] ?? 'text-gray-700 bg-gray-50';
    $itemHref = $item['href'] ?? ($itemLinks[$item['slug'] ?? ''] ?? null);
    $detailArrow = app()->getLocale() === 'en' ? '→' : '←';
@endphp
@if($itemHref)
<a href="{{ $itemHref }}" class="block p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 hover:shadow-sm transition-all group text-start operations-kpi-card">
    <p class="text-xs text-gray-500 mb-1">{{ $item['label'] }}</p>
    <div class="flex items-end justify-between gap-2">
        <p class="text-lg font-extrabold text-gray-900">{{ number_format($item['value'], 1) }} <span class="text-xs font-normal text-gray-500">{{ $item['unit'] }}</span></p>
        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $badge }}">{{ number_format($item['achievement'], 0) }}%</span>
    </div>
    <p class="text-[10px] text-gray-400 mt-1">{{ __('operations.kpi.target') }}: {{ number_format($item['target'], 1) }} {{ $item['unit'] }}</p>
    <span class="inline-flex items-center gap-1 text-[10px] font-bold mt-2 opacity-70 group-hover:opacity-100" style="color:{{ $themeColor }}">{{ __('operations.actions.view_details') }} {{ $detailArrow }}</span>
</a>
@else
<div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-start operations-kpi-card">
    <p class="text-xs text-gray-500 mb-1">{{ $item['label'] }}</p>
    <div class="flex items-end justify-between gap-2">
        <p class="text-lg font-extrabold text-gray-900">{{ number_format($item['value'], 1) }} <span class="text-xs font-normal text-gray-500">{{ $item['unit'] }}</span></p>
        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $badge }}">{{ number_format($item['achievement'], 0) }}%</span>
    </div>
    <p class="text-[10px] text-gray-400 mt-1">{{ __('operations.kpi.target') }}: {{ number_format($item['target'], 1) }} {{ $item['unit'] }}</p>
</div>
@endif
