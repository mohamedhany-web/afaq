@php
    $column = $columns[$stage] ?? ['items' => collect(), 'total' => 0, 'has_more' => false, 'deferred' => false];
    $clients = $column['items'];
    $totalInStage = $column['total'];
    $hasMore = $column['has_more'];
    $color = $stageColors[$stage] ?? ['bg' => $themeColor, 'light' => '#f3f4f6'];
    $total = $stageTotals[$stage] ?? ['count' => 0];
    $remaining = max(0, $totalInStage - $clients->count());
@endphp
<div class="pipeline-column w-[280px] sm:w-[300px] shrink-0 snap-start rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col bg-white max-h-[min(72vh,560px)]">
    <div class="px-2.5 py-2 border-b border-gray-100 shrink-0" style="background: {{ $color['light'] }};">
        <div class="flex items-center justify-between gap-1.5">
            <div class="flex items-center gap-1 min-w-0">
                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background: {{ $color['bg'] }};"></span>
                <h3 class="font-bold text-[11px] text-gray-900 font-tajawal truncate">{{ $stageLabels[$stage] }}</h3>
            </div>
            <span class="kanban-count shrink-0 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-[9px] font-bold text-white tabular-nums"
                  style="background: {{ $color['bg'] }};" data-stage="{{ $stage }}">{{ number_format($totalInStage) }}</span>
        </div>
        <p class="text-[9px] text-gray-500 font-tajawal mt-0.5">مرحلة رحلة العميل</p>
    </div>

    <div class="kanban-drop-zone flex-1 p-1.5 space-y-1.5 overflow-y-auto overflow-x-hidden bg-gray-50/80 min-h-[80px] scroll-smooth"
         data-stage="{{ $stage }}"
         data-total="{{ $totalInStage }}"
         data-loaded="{{ $clients->count() }}">
        @forelse($clients as $client)
            @include('crm.pipeline.partials.client-card', [
                'client' => $client,
                'accentColor' => $color['bg'],
                'stageLabels' => $stageLabels,
                'interactionTypes' => $interactionTypes,
            ])
        @empty
        <div class="kanban-empty flex items-center justify-center py-6 px-2 text-center rounded-md border border-dashed border-gray-200 bg-white/80">
            <p class="text-[10px] text-gray-400 font-tajawal">اسحب عميلاً هنا</p>
        </div>
        @endforelse
    </div>

    @if($hasMore)
    <div class="shrink-0 p-1.5 border-t border-gray-100 bg-white" data-load-more-wrap>
        <button type="button"
                class="kanban-load-more w-full py-1.5 rounded-md text-[10px] font-bold font-tajawal transition hover:opacity-90 disabled:opacity-50"
                style="color: {{ $color['bg'] }}; background: {{ $color['light'] }};"
                data-stage="{{ $stage }}"
                data-page="2"
                data-remaining="{{ $remaining }}">
            المزيد ({{ number_format($remaining) }})
        </button>
    </div>
    @endif
</div>
