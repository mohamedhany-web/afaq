@php
    $isEdit = isset($client);
    $clientTypeValue = old('client_type', $isEdit
        ? \App\Models\Client::normalizeType($client->client_type)
        : 'individual');
    $leadSourceValue = old('lead_source', $isEdit ? $client->lead_source : '');
@endphp

{{-- البيانات الأساسية --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        البيانات الأساسية
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="sm:col-span-2 lg:col-span-2">
            <label class="{{ $label }}">الاسم الكامل *</label>
            <input name="name" value="{{ old('name', $client->name ?? '') }}" required class="{{ $input }}" placeholder="اسم العميل">
            @error('name')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">رقم الهاتف *</label>
            <input name="phone" value="{{ old('phone', $client->phone ?? '') }}" required class="{{ $input }}" placeholder="01xxxxxxxxx" dir="ltr">
            @error('phone')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">البريد الإلكتروني</label>
            <input name="email" type="email" value="{{ old('email', $client->email ?? '') }}" class="{{ $input }}" placeholder="email@example.com" dir="ltr">
            @error('email')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">تصنيف العميل</label>
            <select name="client_type" id="client_type" class="{{ $input }}">
                @foreach(\App\Models\Client::typeLabels() as $value => $labelText)
                    <option value="{{ $value }}" @selected($clientTypeValue === $value)>{{ $labelText }}</option>
                @endforeach
            </select>
            @error('client_type')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        <div id="id_number_wrap">
            <label class="{{ $label }}">رقم البطاقة <span id="id_number_required_mark" class="text-red-600 hidden">*</span></label>
            <input name="id_number" id="id_number" value="{{ old('id_number', $client->id_number ?? '') }}" class="{{ $input }}" placeholder="إلزامي لعملاء فري لانس" dir="ltr" maxlength="50">
            @error('id_number')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">مصدر العميل</label>
            <select name="lead_source" class="{{ $input }}">
                <option value="">— غير محدد —</option>
                @foreach(\App\Models\Client::leadSourceLabels() as $value => $labelText)
                    <option value="{{ $value }}" @selected($leadSourceValue === $value)>{{ $labelText }}</option>
                @endforeach
            </select>
            @error('lead_source')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">الحالة *</label>
            <select name="status" class="{{ $input }}">
                @foreach(['prospect' => 'محتمل', 'active' => 'نشط', 'inactive' => 'غير نشط', 'suspended' => 'موقوف'] as $val => $txt)
                    <option value="{{ $val }}" @selected(old('status', $client->status ?? 'prospect') === $val)>{{ $txt }}</option>
                @endforeach
            </select>
            @error('status')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- بيانات التواصل --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        بيانات التواصل والشركة
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <div>
            <label class="{{ $label }}">اسم الشركة / المنشأة</label>
            <input name="company" value="{{ old('company', $client->company_name ?? '') }}" class="{{ $input }}" placeholder="اختياري — للعملاء من نوع شركة">
            @error('company')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">العنوان</label>
            <input name="address" value="{{ old('address', $client->address ?? '') }}" class="{{ $input }}" placeholder="المدينة، الحي، الشارع...">
            @error('address')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- ملاحظات --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        ملاحظات
    </div>
    <div class="p-5 sm:p-6">
        <label class="{{ $label }}">ملاحظات إضافية</label>
        <textarea name="notes" rows="4" class="{{ $input }} resize-none" placeholder="اهتمامات العميل، متطلبات الوحدة، مصدر التواصل...">{{ old('notes', $client->notes ?? '') }}</textarea>
        @error('notes')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
    </div>
</div>

@push('scripts')
<script>
(function () {
    const typeSelect = document.getElementById('client_type');
    const idInput = document.getElementById('id_number');
    const mark = document.getElementById('id_number_required_mark');
    if (!typeSelect || !idInput) return;

    function syncIdRequirement() {
        const freelance = typeSelect.value === 'freelance';
        idInput.required = freelance;
        mark?.classList.toggle('hidden', !freelance);
    }

    typeSelect.addEventListener('change', syncIdRequirement);
    syncIdRequirement();
})();
</script>
@endpush
