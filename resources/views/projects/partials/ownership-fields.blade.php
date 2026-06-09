@php
    $ownershipType = old('ownership_type', $project->ownership_type ?? 'developer_third_party');
    $details = old('ownership_details', $project->ownership_details ?? []);
    $input = $input ?? 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = $label ?? 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = $sectionHeader ?? 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full" id="ownership-section">
    <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        نوع ملكية المشروع *
    </div>
    <div class="p-5 sm:p-6 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach(\App\Models\Project::OWNERSHIP_TYPES as $val => $txt)
            <label class="ownership-type-option cursor-pointer">
                <input type="radio" name="ownership_type" value="{{ $val }}" class="sr-only peer"
                       @checked($ownershipType === $val) data-ownership-type="{{ $val }}">
                <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-current transition text-center font-tajawal text-sm font-bold text-gray-600 peer-checked:text-gray-900"
                     style="--tw-ring-color: {{ $themeColor }};" onchange="">
                    {{ $txt }}
                </div>
            </label>
            @endforeach
        </div>
        @error('ownership_type')<p class="text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror

        <div class="ownership-pane {{ $ownershipType !== 'owned' ? 'hidden' : '' }}" data-pane="owned">
            <p class="text-xs text-gray-500 mb-3 font-tajawal">بيانات المشاريع المملوكة لشركتنا</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="{{ $label }}">الجهة الداخلية المالكة</label>
                    <input name="ownership_details[internal_entity]" value="{{ $details['internal_entity'] ?? '' }}" class="{{ $input }}" placeholder="مثال: شركة أفاق للتطوير"></div>
                <div><label class="{{ $label }}">تاريخ الاستحواذ</label>
                    <input type="date" name="ownership_details[acquisition_date]" value="{{ $details['acquisition_date'] ?? '' }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">قيمة الاستثمار (ج.م)</label>
                    <input type="number" min="0" step="0.01" name="ownership_details[investment_amount]" value="{{ $details['investment_amount'] ?? '' }}" class="{{ $input }}"></div>
                <div class="sm:col-span-2"><label class="{{ $label }}">ملاحظات إدارية</label>
                    <textarea name="ownership_details[management_notes]" rows="2" class="{{ $input }}">{{ $details['management_notes'] ?? '' }}</textarea></div>
            </div>
        </div>

        <div class="ownership-pane {{ $ownershipType !== 'partnership' ? 'hidden' : '' }}" data-pane="partnership">
            <p class="text-xs text-gray-500 mb-3 font-tajawal">بيانات مشاريع الشراكة مع جهات أخرى</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="{{ $label }}">اسم الشريك *</label>
                    <input name="ownership_details[partner_name]" value="{{ $details['partner_name'] ?? '' }}" class="{{ $input }}" placeholder="اسم شركة الشريك">
                    @error('ownership_details.partner_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="{{ $label }}">هاتف الشريك</label>
                    <input name="ownership_details[partner_phone]" value="{{ $details['partner_phone'] ?? '' }}" class="{{ $input }}" dir="ltr"></div>
                <div><label class="{{ $label }}">مسؤول التواصل</label>
                    <input name="ownership_details[partner_contact]" value="{{ $details['partner_contact'] ?? '' }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">مرجع العقد</label>
                    <input name="ownership_details[contract_ref]" value="{{ $details['contract_ref'] ?? '' }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">حصتنا %</label>
                    <input type="number" min="0" max="100" name="ownership_details[our_share_percent]" value="{{ $details['our_share_percent'] ?? '' }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">حصة الشريك %</label>
                    <input type="number" min="0" max="100" name="ownership_details[partner_share_percent]" value="{{ $details['partner_share_percent'] ?? '' }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">بداية الشراكة</label>
                    <input type="date" name="ownership_details[partnership_start]" value="{{ $details['partnership_start'] ?? '' }}" class="{{ $input }}"></div>
                <div class="sm:col-span-2"><label class="{{ $label }}">ملاحظات الشراكة</label>
                    <textarea name="ownership_details[partnership_notes]" rows="2" class="{{ $input }}">{{ $details['partnership_notes'] ?? '' }}</textarea></div>
            </div>
        </div>

        <div class="ownership-pane {{ $ownershipType !== 'developer_third_party' ? 'hidden' : '' }}" data-pane="developer_third_party">
            <p class="text-xs text-gray-500 mb-3 font-tajawal">اختر مطوراً مسجلاً بتعاقد نشط — بيانات التعاقد تُدار من <a href="{{ route('admin.developers.index') }}" class="font-bold underline" style="color:{{ $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor() }}">إدارة المطورين العقاريين</a></p>
            <div class="mb-2">
                @include('partials.developer-search-select', [
                    'developerId' => old('real_estate_developer_id', $project->real_estate_developer_id ?? null),
                    'developerName' => old('developer_name', $project->developer_name ?? ''),
                    'inputClass' => $input,
                    'required' => true,
                    'contractedOnly' => true,
                    'allowCreate' => false,
                ])
                @error('real_estate_developer_id')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
                @error('developer_name')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const theme = @json($themeColor ?? \App\Helpers\SettingsHelper::getThemeColor());
    const radios = document.querySelectorAll('#ownership-section input[name="ownership_type"]');
    const panes = document.querySelectorAll('#ownership-section .ownership-pane');

    function syncOwnershipUI() {
        const selected = document.querySelector('#ownership-section input[name="ownership_type"]:checked');
        const type = selected?.value || 'developer_third_party';
        panes.forEach(p => p.classList.toggle('hidden', p.dataset.pane !== type));
        document.querySelectorAll('#ownership-section .ownership-type-option > div').forEach((box, i) => {
            const radio = radios[i];
            if (radio?.checked) {
                box.style.borderColor = theme;
                box.style.background = theme + '10';
            } else {
                box.style.borderColor = '';
                box.style.background = '';
            }
        });
    }

    radios.forEach(r => r.addEventListener('change', syncOwnershipUI));
    syncOwnershipUI();
});
</script>
@endpush
