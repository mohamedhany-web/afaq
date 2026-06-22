@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $salesReps = $salesReps ?? collect();
    $selectedRepId = $selectedRepId ?? optional($selectedSalesRep ?? null)->id;
    $compact = !empty($compact);
    $filterAction = $filterAction ?? route('operations.reps.search');
    $repFieldName = $repFieldName ?? 'rep_id';
    $isDashboardFilter = $filterAction === route('operations.dashboard');
    $inputClass = $compact
        ? 'border rounded-xl px-3 py-2.5 text-sm min-w-0'
        : 'border rounded-xl px-4 py-3 text-sm min-w-0';
    $buttonClass = $compact
        ? 'px-5 py-2.5 rounded-xl text-white text-sm font-bold whitespace-nowrap shrink-0'
        : 'px-6 py-3 rounded-xl text-white text-sm font-bold whitespace-nowrap shrink-0';
@endphp

<div class="flex flex-col sm:flex-row flex-1 min-w-[240px] max-w-2xl gap-2 font-tajawal">
    <form method="GET" action="{{ $filterAction }}" class="flex flex-1 gap-2 min-w-0">
        <select name="{{ $repFieldName }}" required
                class="{{ $inputClass }} flex-1 bg-white"
                aria-label="{{ __('operations.actions.select_sales_rep') }}">
            <option value="" disabled @selected(!$selectedRepId)>{{ __('operations.actions.select_sales_rep') }}</option>
            @foreach($salesReps as $rep)
                <option value="{{ $rep->id }}" @selected((int) $selectedRepId === (int) $rep->id)>
                    {{ $rep->name }}@if($rep->employee?->department) — {{ $rep->employee->department->name }}@endif
                </option>
            @endforeach
        </select>
        <button type="submit" class="{{ $buttonClass }}" style="background:{{ $themeColor }}">
            {{ $isDashboardFilter ? __('operations.actions.apply_filter') : __('operations.actions.open_rep_workspace') }}
        </button>
    </form>

    <form method="GET" action="{{ route('operations.reps.search') }}" class="flex flex-1 gap-2 min-w-0">
        <input type="search" name="q" value="{{ $q ?? '' }}"
               placeholder="{{ __('operations.actions.search_sales_rep_placeholder') }}"
               class="{{ $inputClass }} flex-1">
        <button type="submit" class="{{ $buttonClass }} border border-gray-200 bg-white hover:bg-gray-50" style="color:{{ $themeColor }}">
            {{ __('operations.actions.search') }}
        </button>
    </form>
</div>
