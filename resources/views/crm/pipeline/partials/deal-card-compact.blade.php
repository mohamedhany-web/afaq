@php $accent = $accentColor ?? '#6366f1'; @endphp
<div class="kanban-card bg-white rounded-md p-2 border border-gray-200 shadow-sm hover:shadow cursor-grab active:cursor-grabbing"
     data-deal-id="{{ $deal->id }}">
    <a href="{{ route('crm.pipeline.show', $deal) }}" class="block font-tajawal" draggable="false" onclick="event.stopPropagation()">
        <p class="font-semibold text-[11px] text-gray-900 truncate">{{ $deal->product_service }}</p>
        @if($deal->project)
        <p class="text-[10px] text-gray-500 truncate">{{ $deal->project->name }}</p>
        @endif
    </a>
    <div class="flex items-center justify-between mt-1 pt-1 border-t border-gray-50 gap-1">
        <span class="text-[10px] font-bold tabular-nums" style="color: {{ $accent }};">{{ \App\Helpers\SettingsHelper::formatMoney($deal->estimated_value) }}</span>
        <span class="text-[9px] text-gray-500 tabular-nums">{{ $deal->probability_percentage }}%</span>
    </div>
</div>
