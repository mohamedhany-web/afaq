@php
    $accent = $accentColor ?? '#6366f1';
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $deals = $client->sales ?? collect();
    $dealsValue = $deals->whereNotIn('stage', ['closed_lost'])->sum('estimated_value');
    $dealStageColors = [
        'lead' => '#6366f1', 'prospect' => '#3b82f6', 'proposal' => '#0ea5e9',
        'negotiation' => '#f59e0b', 'closed_won' => '#16a34a', 'closed_lost' => '#ef4444',
    ];
@endphp
<div class="kanban-card group bg-white rounded-lg border border-gray-200 shadow-sm hover:border-gray-300 transition-all cursor-grab active:cursor-grabbing font-tajawal"
     data-client-id="{{ $client->id }}">
    {{-- رأس البطاقة --}}
    <div class="p-2 border-b border-gray-50">
        <div class="flex items-start gap-1">
            <span class="shrink-0 mt-0.5 text-gray-300 group-hover:text-gray-400 pointer-events-none" aria-hidden="true">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
            </span>
            <div class="flex-1 min-w-0">
                <a href="{{ $client->profileUrl() }}" class="font-bold text-[12px] text-gray-900 hover:underline block truncate" draggable="false">{{ $client->name }}</a>
                <p class="text-[10px] text-gray-500 truncate" dir="ltr">{{ $client->phone }}</p>
                <div class="flex flex-wrap items-center gap-1 mt-1">
                    @include('crm.clients.partials.status-badge', ['status' => $client->status])
                    @if($deals->count())
                    <span class="text-[9px] px-1.5 py-px rounded bg-gray-100 text-gray-600 font-semibold">{{ $deals->count() }} صفقة</span>
                    @endif
                </div>
            </div>
        </div>
        @if($dealsValue > 0)
        <p class="text-[10px] font-bold mt-1.5 tabular-nums" style="color: {{ $accent }};">قيمة الصفقات: {{ $money($dealsValue) }}</p>
        @endif
    </div>

    {{-- الصفقات داخل العميل --}}
    <details class="client-deals-panel border-b border-gray-50" open>
        <summary class="px-2 py-1.5 text-[10px] font-bold text-gray-600 cursor-pointer hover:bg-gray-50 select-none list-none flex items-center justify-between">
            <span>الصفقات والحالات</span>
            <span class="text-gray-400">{{ $deals->count() }}</span>
        </summary>
        <div class="px-2 pb-2 space-y-1 max-h-36 overflow-y-auto" onclick="event.stopPropagation()">
            @forelse($deals as $sale)
            <div class="rounded-md border border-gray-100 bg-gray-50/80 p-1.5 text-[10px]">
                <div class="flex items-start justify-between gap-1">
                    <a href="{{ route('crm.pipeline.show', $sale) }}" class="font-semibold text-gray-800 truncate flex-1 hover:underline">{{ $sale->product_service }}</a>
                    <span class="shrink-0 font-bold tabular-nums" style="color: {{ $accent }};">{{ $money($sale->estimated_value) }}</span>
                </div>
                @if($sale->project)
                <p class="text-gray-500 truncate mt-0.5">{{ $sale->project->name }}</p>
                @endif
                <div class="flex items-center gap-1 mt-1">
                    <select class="deal-stage-select flex-1 min-w-0 rounded border border-gray-200 bg-white px-1 py-0.5 text-[9px] font-semibold"
                            data-sale-id="{{ $sale->id }}"
                            data-update-url="{{ route('crm.pipeline.update-stage', $sale) }}"
                            style="color: {{ $dealStageColors[$sale->stage] ?? '#374151' }};">
                        @foreach($stageLabels as $key => $label)
                        <option value="{{ $key }}" @selected($sale->stage === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="shrink-0 text-[9px] text-gray-400 tabular-nums">{{ $sale->probability_percentage }}%</span>
                </div>
                @if($sale->viewing_date)
                <p class="text-[9px] text-amber-700 mt-0.5">اجتماع: {{ \Carbon\Carbon::parse($sale->viewing_date)->format('Y/m/d') }}</p>
                @endif
            </div>
            @empty
            <p class="text-[10px] text-gray-400 text-center py-2">لا توجد صفقات</p>
            <a href="{{ route('crm.pipeline.create', ['client_id' => $client->id]) }}"
               class="block text-center text-[10px] font-bold py-1 rounded-md hover:opacity-90"
               style="color: {{ $accent }}; background: {{ $accent }}12;">+ إضافة صفقة</a>
            @endforelse
            @if($deals->count())
            <a href="{{ route('crm.pipeline.create', ['client_id' => $client->id]) }}"
               class="block text-center text-[9px] font-semibold text-gray-500 hover:text-gray-700 pt-0.5">+ صفقة أخرى</a>
            @endif
        </div>
    </details>

    {{-- تسجيل متابعة --}}
    <details class="client-log-panel" onclick="event.stopPropagation()">
        <summary class="px-2 py-1.5 text-[10px] font-bold cursor-pointer hover:bg-gray-50 select-none list-none"
                 style="color: {{ $accent }};">تسجيل متابعة / بيانات</summary>
        <form class="client-interaction-form px-2 pb-2 space-y-1.5"
              data-url="{{ route('crm.clients.log-interaction', $client) }}"
              onclick="event.stopPropagation()">
            @csrf
            <select name="interaction_type" class="w-full rounded-md border border-gray-200 px-2 py-1 text-[10px] font-tajawal" required>
                @foreach($interactionTypes as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            @if($deals->count())
            <select name="sale_id" class="w-full rounded-md border border-gray-200 px-2 py-1 text-[10px] font-tajawal interaction-sale-select hidden">
                <option value="">— ربط بصفقة (اختياري) —</option>
                @foreach($deals as $sale)
                <option value="{{ $sale->id }}">{{ \Illuminate\Support\Str::limit($sale->product_service, 30) }}</option>
                @endforeach
            </select>
            @endif
            <input type="date" name="viewing_date" class="w-full rounded-md border border-gray-200 px-2 py-1 text-[10px] font-tajawal interaction-viewing-date hidden">
            <textarea name="notes" rows="2" required placeholder="اكتب تفاصيل المكالمة، الاجتماع، أو الملاحظة..."
                      class="w-full rounded-md border border-gray-200 px-2 py-1 text-[10px] font-tajawal resize-none"></textarea>
            <button type="submit" class="w-full py-1.5 rounded-md text-[10px] font-bold text-white transition hover:opacity-90 disabled:opacity-50"
                    style="background: {{ $accent }};">حفظ</button>
            <p class="interaction-msg text-[9px] text-center hidden"></p>
        </form>
    </details>
</div>
