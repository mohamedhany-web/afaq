@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-tajawal font-bold text-gray-900 flex items-center gap-2"
             style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold text-white" style="background: {{ $themeColor }};">7</span>
            التحديات
        </div>
        <dl class="p-5 space-y-4 text-sm font-tajawal">
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1">عقبات اليوم</dt>
                <dd class="text-gray-800 whitespace-pre-wrap rounded-xl bg-gray-50 p-4 min-h-[80px]">{{ $report->obstacles ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1">دعم مطلوب</dt>
                <dd class="text-gray-800 whitespace-pre-wrap rounded-xl bg-gray-50 p-4 min-h-[60px]">{{ $report->support_required ?: '—' }}</dd>
            </div>
        </dl>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-tajawal font-bold text-gray-900 flex items-center gap-2"
             style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold text-white" style="background: {{ $themeColor }};">8</span>
            خطة الغد
        </div>
        <div class="p-5">
            <div class="grid grid-cols-3 gap-3 mb-4">
                @foreach(['مكالمات' => $report->tomorrow_planned_calls, 'اجتماعات' => $report->tomorrow_planned_meetings, 'معاينات' => $report->tomorrow_planned_visits] as $lbl => $val)
                <div class="rounded-xl bg-gray-50 p-3 text-center">
                    <dt class="text-xs font-bold text-gray-500 font-tajawal">{{ $lbl }}</dt>
                    <dd class="text-2xl font-bold text-gray-900 mt-1 tabular-nums">{{ $val ?? '—' }}</dd>
                </div>
                @endforeach
            </div>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">عملاء أولوية</dt>
                <dd class="text-gray-800 whitespace-pre-wrap rounded-xl bg-gray-50 p-4 min-h-[80px] text-sm font-tajawal">{{ $report->tomorrow_priority_leads ?: '—' }}</dd>
            </div>
        </div>
    </div>
</div>
