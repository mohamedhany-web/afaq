@php
    $c = $contract ?? null;
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $sectionBg = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $sectionBg }}">بيانات الوكيل</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="{{ $label }}">حساب الوكيل (مندوب مبيعات) *</label>
                <select name="user_id" required class="{{ $input }}" @if($c) disabled @endif>
                    <option value="">— اختر —</option>
                    @foreach($agents as $a)
                    <option value="{{ $a->id }}" @selected(old('user_id', $c->user_id ?? '')==$a->id)>{{ $a->name }} ({{ $a->email }})</option>
                    @endforeach
                </select>
                @if($c)<input type="hidden" name="user_id" value="{{ $c->user_id }}">@endif
            </div>
            <div><label class="{{ $label }}">رقم العقد</label><input name="contract_number" value="{{ old('contract_number', $c->contract_number ?? '') }}" class="{{ $input }}"></div>
            <div><label class="{{ $label }}">الرقم القومي</label><input name="national_id" value="{{ old('national_id', $c->national_id ?? '') }}" class="{{ $input }}" dir="ltr"></div>
            <div><label class="{{ $label }}">الجنسية</label><input name="nationality" value="{{ old('nationality', $c->nationality ?? 'مصري') }}" class="{{ $input }}"></div>
            <div><label class="{{ $label }}">الهاتف</label><input name="phone" value="{{ old('phone', $c->phone ?? '') }}" class="{{ $input }}" dir="ltr"></div>
            <div class="sm:col-span-2"><label class="{{ $label }}">العنوان</label><input name="address" value="{{ old('address', $c->address ?? '') }}" class="{{ $input }}"></div>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }}" style="{{ $sectionBg }}">مدة العقد والتارجت</div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="{{ $label }}">تاريخ البداية *</label><input type="date" name="start_date" required value="{{ old('start_date', optional($c?->start_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">تاريخ النهاية</label><input type="date" name="end_date" value="{{ old('end_date', optional($c?->end_date)->format('Y-m-d')) }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">تارجت ربع سنوي (قيمة مبيعات)</label><input type="number" step="0.01" min="0" name="quarterly_target_amount" value="{{ old('quarterly_target_amount', $c->quarterly_target_amount ?? '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">تارجت ربع سنوي (عدد صفقات)</label><input type="number" min="1" name="quarterly_target_deals" value="{{ old('quarterly_target_deals', $c->quarterly_target_deals ?? '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">حالة العقد *</label>
                    <select name="status" class="{{ $input }}">@foreach($statuses as $k=>$t)<option value="{{ $k }}" @selected(old('status',$c->status??'active')===$k)>{{ $t }}</option>@endforeach</select>
                </div>
                <div><label class="{{ $label }}">تاريخ التوقيع</label><input type="date" name="signed_at" value="{{ old('signed_at', optional($c?->signed_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="{{ $input }}"></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }}" style="{{ $sectionBg }}">توقيع الشركة</div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="{{ $label }}">اسم الموقّع عن الشركة</label><input name="company_signatory_name" value="{{ old('company_signatory_name', $c->company_signatory_name ?? '') }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">الصفة</label><input name="company_signatory_title" value="{{ old('company_signatory_title', $c->company_signatory_title ?? 'المدير التنفيذي') }}" class="{{ $input }}"></div>
                <div class="sm:col-span-2"><label class="{{ $label }}">ملاحظات</label><textarea name="notes" rows="2" class="{{ $input }}">{{ old('notes', $c->notes ?? '') }}</textarea></div>
            </div>
        </div>
    </div>
</div>
