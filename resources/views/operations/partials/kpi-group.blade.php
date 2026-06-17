@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $group = $group ?? null;
    $timingSlugs = config('operations_kpis.timing_slugs', [
        'lead_response_time',
        'lead_distribution_time',
        'contact_rate',
        'sales_cycle_duration',
        'report_delivery_time',
    ]);
    $itemLinks = [
        'lead_response_time' => route('operations.clients.index', ['view' => 'distribution']) . '#page-data',
        'lead_distribution_time' => route('operations.clients.index', ['view' => 'distribution']) . '#page-data',
        'lead_leakage_rate' => route('operations.clients.index', ['view' => 'distribution', 'filter' => 'stale']) . '#page-data',
        'contact_rate' => route('operations.crm.index') . '#page-data',
        'crm_compliance_rate' => route('operations.crm.index') . '#page-data',
        'data_accuracy_rate' => route('operations.crm.index') . '#page-data',
        'duplicate_records_rate' => route('operations.crm.index') . '#page-data',
        'pipeline_update_rate' => route('crm.pipeline.index'),
        'lead_to_meeting_conversion' => route('crm.pipeline.index'),
        'meeting_to_reservation_conversion' => route('crm.pipeline.index'),
        'reservation_to_contract_conversion' => route('crm.pipeline.index'),
        'sales_cycle_duration' => route('operations.crm.index') . '#page-data',
        'revenue_growth_support' => route('operations.crm.index') . '#page-data',
        'lost_opportunity_recovery' => route('operations.crm.index') . '#page-data',
        'inventory_accuracy' => route('operations.inventory.index') . '#page-data',
        'unit_availability_accuracy' => route('operations.inventory.index', ['status' => 'available']) . '#page-data',
        'double_booking_incidents' => route('operations.inventory.index') . '#page-data',
        'active_inventory_units' => route('operations.inventory.index', ['status' => 'available']) . '#page-data',
        'sales_activity_compliance' => route('operations.team.index') . '#page-data',
        'follow_up_compliance' => route('operations.team.index') . '#page-data',
        'employee_productivity_score' => route('operations.team.index') . '#page-data',
        'report_accuracy' => route('operations.reports.index') . '#page-data',
        'report_delivery_time' => route('operations.reports.index') . '#page-data',
        'reports_submitted' => route('operations.reports.index', ['status' => 'submitted']) . '#page-data',
    ];
    $allItems = collect($group['items'] ?? []);
    $primaryItems = $allItems->reject(fn ($item) => in_array($item['slug'] ?? '', $timingSlugs, true));
    $timingItems = $allItems->filter(fn ($item) => in_array($item['slug'] ?? '', $timingSlugs, true));
@endphp
@if($group)
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden font-tajawal ui-compact-hidden operations-kpi-card text-start">
    <div class="px-5 py-4 border-b flex items-center justify-between gap-3" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">
        <div>
            <p class="font-bold text-gray-900">{{ $group['label'] }}</p>
            <p class="text-xs text-gray-500">{{ __('operations.kpi.total_score') }}: {{ number_format($group['score'], 1) }}%</p>
        </div>
        @if(!empty($link))
        <a href="{{ $link }}" class="text-xs font-bold px-3 py-1.5 rounded-lg border hover:bg-gray-50" style="color:{{ $themeColor }}">{{ __('operations.actions.view_details') }}</a>
        @endif
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($primaryItems as $item)
            @include('operations.partials.kpi-item', ['item' => $item, 'itemLinks' => $itemLinks, 'themeColor' => $themeColor])
        @endforeach
    </div>

    @if($timingItems->isNotEmpty())
    <div class="px-4 pb-4" x-data="{ open: false }">
        <button type="button" @click="open = !open"
                class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl border border-dashed border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors text-start">
            <span class="flex items-center gap-2 text-sm font-bold text-gray-700">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white shrink-0" style="background:{{ $themeColor }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                {{ __('operations.kpi.timing_toggle') }}
                <span class="text-xs font-normal text-gray-500">({{ $timingItems->count() }})</span>
            </span>
            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-cloak class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($timingItems as $item)
                @include('operations.partials.kpi-item', ['item' => $item, 'itemLinks' => $itemLinks, 'themeColor' => $themeColor])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
