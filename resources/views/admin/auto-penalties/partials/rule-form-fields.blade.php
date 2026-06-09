@php
    $rule = $rule ?? null;
    $prefix = $prefix ?? '';
@endphp
<div>
    <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">اسم القاعدة</label>
    <input type="text" name="name" value="{{ old('name', $rule?->name) }}" required
           class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm focus:border-gray-300 font-tajawal">
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">القسم</label>
        <select name="department_code" class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
            <option value="">كل الأقسام</option>
            @foreach($departments as $code => $label)
                <option value="{{ $code }}" @selected(old('department_code', $rule?->department_code) === $code)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">نوع المخالفة</label>
        <select name="source_type" required class="rule-source-type w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal" data-prefix="{{ $prefix }}">
            @foreach($sourceTypes as $key => $label)
                <option value="{{ $key }}" @selected(old('source_type', $rule?->source_type) === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="rule-report-period-wrap {{ in_array(old('source_type', $rule?->source_type), ['daily_sales_report', 'marketing_report'], true) ? '' : 'hidden' }}" data-prefix="{{ $prefix }}">
    <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">دورة التقرير</label>
    <select name="report_period_type" class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
        @foreach($reportPeriodTypes as $key => $label)
            <option value="{{ $key }}" @selected(old('report_period_type', $rule?->report_period_type ?? 'daily') === $key)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">ينطبق على</label>
        <select name="applies_to" class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
            @foreach($appliesTo as $key => $label)
                <option value="{{ $key }}" @selected(old('applies_to', $rule?->applies_to ?? 'employee') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">مبلغ الخصم</label>
        <input type="number" name="amount" step="0.01" min="0" value="{{ old('amount', $rule?->amount ?? 50) }}" required
               class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
    </div>
</div>
<div>
    <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">ساعات السماح بعد الموعد</label>
    <input type="number" name="grace_hours" min="0" max="720" value="{{ old('grace_hours', $rule?->grace_hours ?? 2) }}" required
           class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
</div>
<div>
    <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">وصف (اختياري)</label>
    <textarea name="description" rows="2" class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">{{ old('description', $rule?->description) }}</textarea>
</div>
<label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700 font-tajawal">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rule?->is_active ?? true)) class="rounded border-gray-300">
    تفعيل القاعدة
</label>
