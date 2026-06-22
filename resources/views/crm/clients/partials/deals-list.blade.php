@php
    $filterId = 'client-deals-filter-' . $client->id;
    $listId = 'client-deals-list-' . $client->id;
    $sales = $client->sales->sortByDesc('updated_at');
@endphp
<div id="client-deals-section" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <div>
            <h3 class="font-bold text-gray-900 font-tajawal">صفقات العميل</h3>
            <p class="text-xs text-gray-500 mt-0.5 font-tajawal">{{ $sales->count() }} صفقة · {{ $money($sales->sum('estimated_value')) }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <select id="{{ $filterId }}" class="border-2 border-gray-200 rounded-xl px-3 py-1.5 text-xs font-semibold font-tajawal text-gray-700 bg-white">
                <option value="">كل المراحل</option>
                @foreach($stageLabels as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <a href="{{ route('crm.pipeline.create', ['client_id' => $client->id]) }}" class="text-xs font-semibold font-tajawal px-3 py-1.5 rounded-lg text-white"
               style="background: {{ $themeColor }};">+ صفقة جديدة</a>
        </div>
    </div>

    <div id="{{ $listId }}" class="p-5 sm:p-6 space-y-3">
        @forelse($sales as $sale)
        <a href="{{ route('crm.pipeline.show', $sale) }}" data-deal-stage="{{ $sale->stage }}"
           class="client-deal-row block p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-gray-50/80 transition-all">
            <div class="flex flex-col gap-3">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-gray-900 font-tajawal">{{ $sale->product_service }}</div>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1.5 text-xs text-gray-500 font-tajawal">
                            @if($sale->project)
                            <span>
                                مشروع:
                                <span class="font-medium" style="color: {{ $themeColor }};">{{ $sale->project->name }}</span>
                            </span>
                            @endif
                            @if($sale->salesRep)
                            <span class="text-gray-300 hidden sm:inline">·</span>
                            <span>مندوب: {{ $sale->salesRep->name }}</span>
                            @endif
                            @if($sale->updated_at)
                            <span class="text-gray-300 hidden sm:inline">·</span>
                            <span>آخر تحديث: {{ $sale->updated_at->format('Y/m/d') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal bg-gray-100 text-gray-700">
                            {{ $stageLabels[$sale->stage] ?? $sale->stage }}
                        </span>
                        <span class="font-bold text-sm font-tajawal whitespace-nowrap" style="color: {{ $themeColor }};">{{ $money($sale->estimated_value) }}</span>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-10">
            <p class="text-gray-400 font-tajawal mb-4">لا توجد صفقات لهذا العميل بعد</p>
            <a href="{{ route('crm.pipeline.create', ['client_id' => $client->id]) }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
               style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                إنشاء أول صفقة
            </a>
        </div>
        @endforelse
        <p id="{{ $listId }}-empty" class="hidden text-center text-sm text-gray-400 font-tajawal py-6">لا توجد صفقات في هذه المرحلة.</p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filter = document.getElementById('{{ $filterId }}');
    const rows = document.querySelectorAll('#{{ $listId }} .client-deal-row');
    const emptyMsg = document.getElementById('{{ $listId }}-empty');
    if (!filter || !rows.length) return;

    filter.addEventListener('change', function () {
        const stage = filter.value;
        let visible = 0;
        rows.forEach(function (row) {
            const show = !stage || row.dataset.dealStage === stage;
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        if (emptyMsg) {
            emptyMsg.classList.toggle('hidden', visible > 0 || !stage);
        }
    });
});
</script>
@endpush
