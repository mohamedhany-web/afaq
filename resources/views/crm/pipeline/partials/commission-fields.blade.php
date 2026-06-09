@php
    $input = $input ?? 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mt-6">
    <div class="px-5 sm:px-6 py-4 border-b font-bold font-tajawal" style="background:linear-gradient(135deg,{{ $themeColor }}08 0%,{{ $themeColor }}03 100%);">
        بيانات العمولة (هيكل الوكيل المستقل)
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="{{ $label }}">نوع العملية العقارية</label>
            <select name="transaction_type" class="{{ $input }}">
                <option value="">— تلقائي —</option>
                @foreach($transactionTypes ?? config('freelance_agents.transaction_types', []) as $k => $t)
                <option value="{{ $k }}" @selected(old('transaction_type', $sale->transaction_type ?? '')===$k)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">إجمالي عمولة الشركة (جنيه)</label>
            <input type="number" step="0.01" min="0" name="company_commission_amount" value="{{ old('company_commission_amount', $sale->company_commission_amount ?? '') }}" class="{{ $input }}" placeholder="المبلغ المحصّل من المطور/البائع">
        </div>
        <div>
            <label class="{{ $label }}">وكيل الجلب (Listing)</label>
            <select name="listing_agent_id" class="{{ $input }}">
                <option value="">— لا يوجد —</option>
                @foreach($agents ?? [] as $a)
                <option value="{{ $a->id }}" @selected(old('listing_agent_id', $sale->listing_agent_id ?? '')==$a->id)>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">تاريخ الإغلاق الفعلي</label>
            <input type="date" name="actual_close_date" value="{{ old('actual_close_date', optional($sale->actual_close_date)->format('Y-m-d')) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">القيمة الفعلية للصفقة</label>
            <input type="number" step="0.01" min="0" name="actual_value" value="{{ old('actual_value', $sale->actual_value ?? '') }}" class="{{ $input }}">
        </div>
        <div class="flex items-center gap-2 pt-6">
            <input type="checkbox" name="commission_collected" value="1" id="commission_collected" @checked(old('commission_collected', $sale->commission_collected ?? false)) class="w-4 h-4 rounded" style="accent-color:{{ $themeColor }};">
            <label for="commission_collected" class="text-sm font-semibold font-tajawal">تم تحصيل عمولة الشركة (جاهزة للصرف)</label>
        </div>
        <div class="sm:col-span-2">
            <label class="{{ $label }}">ملاحظات العمولة</label>
            <textarea name="commission_notes" rows="2" class="{{ $input }}">{{ old('commission_notes', $sale->commission_notes ?? '') }}</textarea>
        </div>
    </div>
    <p class="px-5 pb-4 text-[11px] text-gray-400 font-tajawal">النسب تُحسب من عمولة الشركة وليس قيمة الصفقة — <a href="{{ route('crm.freelance-agents.scheme') }}" class="underline" style="color:{{ $themeColor }}">جدول الهيكل</a></p>
</div>
