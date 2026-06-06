@php
    $accent = $accentColor ?? '#6366f1';
@endphp
<div class="kanban-card group bg-white rounded-md p-2 border border-gray-200 shadow-sm hover:border-gray-300 hover:shadow transition-all cursor-grab active:cursor-grabbing"
     data-deal-id="{{ $deal->id }}">
    <div class="flex items-start gap-1">
        <span class="kanban-drag-handle shrink-0 p-0.5 rounded text-gray-300 group-hover:text-gray-500 pointer-events-none" aria-hidden="true">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
        </span>
        <a href="{{ route('crm.pipeline.show', $deal) }}" class="flex-1 min-w-0 block" draggable="false">
            <div class="flex items-center justify-between gap-1">
                <p class="font-semibold text-[11px] text-gray-900 font-tajawal truncate leading-tight">{{ $deal->client?->name ?? '—' }}</p>
                <span class="shrink-0 text-[9px] font-bold px-1 py-px rounded bg-gray-100 text-gray-600 tabular-nums">{{ $deal->probability_percentage }}%</span>
            </div>
            <p class="text-[10px] text-gray-500 truncate leading-tight mt-0.5">{{ $deal->project?->name ?? \Illuminate\Support\Str::limit($deal->product_service, 28) }}</p>
            <div class="flex items-center justify-between mt-1 pt-1 border-t border-gray-50 gap-1">
                <span class="text-[10px] font-bold tabular-nums truncate" style="color: {{ $accent }};">{{ \App\Helpers\SettingsHelper::formatMoney($deal->estimated_value) }}</span>
                @if($deal->salesRep)
                <span class="text-[9px] text-gray-400 truncate max-w-[3.5rem]" title="{{ $deal->salesRep->name }}">{{ $deal->salesRep->name }}</span>
                @endif
            </div>
        </a>
    </div>
</div>
