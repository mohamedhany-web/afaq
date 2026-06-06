@php
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900 flex items-center gap-2"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold text-white" style="background: {{ $themeColor }};">7</span>
        <div>
            <span class="block">Challenges</span>
            <span class="text-xs font-normal text-gray-500">التحديات — إدخال يدوي</span>
        </div>
    </div>
    <div class="p-5 space-y-4">
        <div>
            <label class="{{ $label }}">عقبات اليوم</label>
            <textarea name="obstacles" rows="5" class="{{ $input }} resize-y min-h-[120px]"
                      placeholder="صف العقبات التي واجهتها اليوم...">{{ old('obstacles', $report->obstacles) }}</textarea>
        </div>
        <div>
            <label class="{{ $label }}">دعم مطلوب من الإدارة</label>
            <textarea name="support_required" rows="4" class="{{ $input }} resize-y min-h-[100px]"
                      placeholder="ما الذي تحتاجه من مدير المبيعات أو الإدارة؟">{{ old('support_required', $report->support_required) }}</textarea>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mt-4">
    <div class="px-5 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900 flex items-center gap-2"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold text-white" style="background: {{ $themeColor }};">8</span>
        <div>
            <span class="block">Tomorrow Plan</span>
            <span class="text-xs font-normal text-gray-500">خطة الغد — إدخال يدوي</span>
        </div>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div>
                <label class="{{ $label }}">مكالمات</label>
                <input type="number" name="tomorrow_planned_calls" min="0" placeholder="0"
                       value="{{ old('tomorrow_planned_calls', $report->tomorrow_planned_calls) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">اجتماعات</label>
                <input type="number" name="tomorrow_planned_meetings" min="0" placeholder="0"
                       value="{{ old('tomorrow_planned_meetings', $report->tomorrow_planned_meetings) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">معاينات</label>
                <input type="number" name="tomorrow_planned_visits" min="0" placeholder="0"
                       value="{{ old('tomorrow_planned_visits', $report->tomorrow_planned_visits) }}" class="{{ $input }}">
            </div>
        </div>
        <div>
            <label class="{{ $label }}">عملاء ذوو أولوية لغداً</label>
            <textarea name="tomorrow_priority_leads" rows="4" class="{{ $input }} resize-y min-h-[100px]"
                      placeholder="أسماء العملاء أو المشاريع ذات الأولوية...">{{ old('tomorrow_priority_leads', $report->tomorrow_priority_leads) }}</textarea>
        </div>
    </div>
</div>
