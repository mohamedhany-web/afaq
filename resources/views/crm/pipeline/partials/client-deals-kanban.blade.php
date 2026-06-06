<div id="client-deals-kanban" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">
        <h3 class="font-bold text-gray-900 font-tajawal">صفقات العميل — اسحب بين المراحل</h3>
        <p class="text-xs text-gray-500 mt-1 font-tajawal">كل عمود = مرحلة الصفقة. اسحب البطاقة لتحديث الحالة.</p>
    </div>

    <div class="p-4 sm:p-5">
        <div class="mb-4">
            <p class="text-xs font-bold text-gray-500 mb-2 font-tajawal">مراحل نشطة</p>
            <div class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory">
                @foreach($activeStages as $stage)
                @include('crm.pipeline.partials.client-deal-column', [
                    'stage' => $stage,
                    'deals' => $dealColumns[$stage] ?? collect(),
                    'total' => $dealStageTotals[$stage] ?? ['count' => 0, 'value' => 0],
                ])
                @endforeach
            </div>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 mb-2 font-tajawal">النتيجة</p>
            <div class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory">
                @foreach($closedStages as $stage)
                @include('crm.pipeline.partials.client-deal-column', [
                    'stage' => $stage,
                    'deals' => $dealColumns[$stage] ?? collect(),
                    'total' => $dealStageTotals[$stage] ?? ['count' => 0, 'value' => 0],
                ])
                @endforeach
            </div>
        </div>
        @if($dealsCount === 0)
        <div class="text-center py-8 mt-2">
            <p class="text-gray-400 font-tajawal mb-3">لا توجد صفقات بعد</p>
            <a href="{{ route('crm.pipeline.create', ['client_id' => $client->id]) }}"
               class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
               style="background: {{ $themeColor }};">إنشاء أول صفقة</a>
        </div>
        @endif
    </div>
</div>
