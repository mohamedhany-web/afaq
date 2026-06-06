@php
    $color = $stageColors[$stage] ?? ['bg' => $themeColor, 'light' => '#f3f4f6'];
    $deals = $deals ?? collect();
@endphp
<div class="pipeline-column w-[200px] sm:w-[212px] shrink-0 snap-start rounded-xl border border-gray-200 overflow-hidden flex flex-col bg-white max-h-[min(50vh,400px)]">
    <div class="px-2.5 py-2 border-b border-gray-100 shrink-0" style="background: {{ $color['light'] }};">
        <div class="flex items-center justify-between gap-1">
            <h4 class="font-bold text-[10px] text-gray-900 font-tajawal truncate">{{ $stageLabels[$stage] }}</h4>
            <span class="deals-kanban-count text-[9px] font-bold text-white px-1.5 py-px rounded-full tabular-nums"
                  style="background: {{ $color['bg'] }};" data-deal-stage="{{ $stage }}">{{ $deals->count() }}</span>
        </div>
        <p class="text-[9px] text-gray-500 font-tajawal truncate">{{ \App\Helpers\SettingsHelper::formatMoney($total['value'] ?? 0) }}</p>
    </div>
    <div class="deals-kanban-zone flex-1 p-1.5 space-y-1 overflow-y-auto bg-gray-50/80 min-h-[72px]"
         data-deal-stage="{{ $stage }}">
        @forelse($deals as $deal)
        @include('crm.pipeline.partials.deal-card-compact', ['deal' => $deal, 'accentColor' => $color['bg']])
        @empty
        <div class="kanban-empty flex items-center justify-center py-4 rounded-md border border-dashed border-gray-200">
            <span class="text-[9px] text-gray-400 font-tajawal">أفلت هنا</span>
        </div>
        @endforelse
    </div>
</div>
