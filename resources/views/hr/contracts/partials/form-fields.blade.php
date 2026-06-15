<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">الموظف</label>
    <select name="employee_id" required class="w-full border rounded-xl px-3 py-2 text-sm">
        <option value="">اختر الموظف</option>
        @foreach($employees as $emp)
        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">عنوان العقد</label>
    <input type="text" name="title" required class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="مثال: عقد عمل — قسم المبيعات">
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">نوع العقد</label>
    <select name="contract_type" required class="w-full border rounded-xl px-3 py-2 text-sm">
        @foreach($contractTypes as $key => $label)
        <option value="{{ $key }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">تاريخ البداية</label>
        <input type="date" name="start_date" required value="{{ now()->toDateString() }}" class="w-full border rounded-xl px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">تاريخ النهاية</label>
        <input type="date" name="end_date" class="w-full border rounded-xl px-3 py-2 text-sm">
    </div>
</div>
<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">الراتب</label>
        <input type="number" step="0.01" name="salary" class="w-full border rounded-xl px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">الحالة</label>
        <select name="status" class="w-full border rounded-xl px-3 py-2 text-sm">
            @foreach(config('hr_contracts.status_labels', []) as $key => $label)
            <option value="{{ $key }}" @selected($key === 'draft')>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">الشروط</label>
    <textarea name="terms" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">ملاحظات</label>
    <textarea name="notes" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">ملف العقد (PDF / صورة)</label>
    <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm">
</div>
