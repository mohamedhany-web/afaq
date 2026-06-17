@php
    $ownershipType = \App\Models\Project::normalizeOwnershipType(old('ownership_type', $project->ownership_type ?? 'developer')) ?? 'developer';
    $details = old('ownership_details', $project->ownership_details ?? []);
    $input = $input ?? 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = $label ?? 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = $sectionHeader ?? 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $contactLabels = [
        'direct_owner' => 'اسم المالك',
        'trader' => 'اسم التاجر',
        'broker' => 'اسم الوسيط',
        'investor' => 'اسم المستثمر',
        'property_management' => 'اسم مدير الممتلكات',
    ];
    $simpleTypes = array_keys($contactLabels);
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full" id="ownership-section">
    <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        نوع الملكية *
    </div>
    <div class="p-5 sm:p-6 space-y-5">
        <div>
            <label class="{{ $label }}">نوع الملكية</label>
            <select name="ownership_type" id="ownership-type-select" required class="{{ $input }}">
                @foreach(\App\Models\Project::OWNERSHIP_TYPES as $val => $txt)
                <option value="{{ $val }}" @selected($ownershipType === $val)>{{ $txt }}</option>
                @endforeach
            </select>
            @error('ownership_type')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>

        <div class="ownership-pane {{ !in_array($ownershipType, $simpleTypes, true) ? 'hidden' : '' }}" data-pane-group="simple">
            <p class="text-xs text-gray-500 mb-3 font-tajawal" id="simple-ownership-hint">بيانات إضافية حسب نوع الملكية</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="{{ $label }}" id="contact-name-label">{{ $contactLabels[$ownershipType] ?? 'اسم الجهة' }}</label>
                    <input name="ownership_details[contact_name]" value="{{ $details['contact_name'] ?? '' }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">رقم التواصل</label>
                    <input name="ownership_details[contact_phone]" value="{{ $details['contact_phone'] ?? '' }}" class="{{ $input }}" dir="ltr"></div>
                <div class="commission-field {{ in_array($ownershipType, ['trader', 'broker'], true) ? '' : 'hidden' }}"><label class="{{ $label }}">نسبة العمولة %</label>
                    <input type="number" min="0" max="100" step="0.01" name="ownership_details[commission_percent]" value="{{ $details['commission_percent'] ?? '' }}" class="{{ $input }}"></div>
                <div class="investor-field {{ $ownershipType === 'investor' ? '' : 'hidden' }}"><label class="{{ $label }}">قيمة الاستثمار (ج.م)</label>
                    <input type="number" min="0" step="0.01" name="ownership_details[investment_amount]" value="{{ $details['investment_amount'] ?? '' }}" class="{{ $input }}"></div>
                <div class="investor-field {{ $ownershipType === 'investor' ? '' : 'hidden' }}"><label class="{{ $label }}">نسبة الحصة %</label>
                    <input type="number" min="0" max="100" name="ownership_details[share_percent]" value="{{ $details['share_percent'] ?? '' }}" class="{{ $input }}"></div>
                <div class="management-field {{ $ownershipType === 'property_management' ? '' : 'hidden' }}"><label class="{{ $label }}">نسبة الإدارة %</label>
                    <input type="number" min="0" max="100" step="0.01" name="ownership_details[fee_percent]" value="{{ $details['fee_percent'] ?? '' }}" class="{{ $input }}"></div>
                <div><label class="{{ $label }}">مرجع العقد</label>
                    <input name="ownership_details[contract_ref]" value="{{ $details['contract_ref'] ?? '' }}" class="{{ $input }}"></div>
                <div class="sm:col-span-2"><label class="{{ $label }}">ملاحظات</label>
                    <textarea name="ownership_details[notes]" rows="2" class="{{ $input }}">{{ $details['notes'] ?? $details['management_notes'] ?? '' }}</textarea></div>
            </div>
        </div>

        <div class="ownership-pane {{ $ownershipType !== 'afaq_private' ? 'hidden' : '' }}" data-pane="afaq_private">
            <p class="text-xs text-gray-500 mb-3 font-tajawal">بيانات المشاريع الخاصة بأفاق</p>
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
            <p class="text-xs text-gray-500 mb-3 font-tajawal">بيانات مشاريع المشاركات</p>
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

        <div class="ownership-pane {{ $ownershipType !== 'developer' ? 'hidden' : '' }}" data-pane="developer">
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
    const select = document.getElementById('ownership-type-select');
    if (!select) return;

    const simpleTypes = @json($simpleTypes);
    const contactLabels = @json($contactLabels);
    const simplePane = document.querySelector('#ownership-section [data-pane-group="simple"]');
    const namedPanes = document.querySelectorAll('#ownership-section .ownership-pane[data-pane]');

    function syncOwnershipUI() {
        const type = select.value || 'developer';

        if (simplePane) {
            simplePane.classList.toggle('hidden', !simpleTypes.includes(type));
        }

        namedPanes.forEach(p => p.classList.toggle('hidden', p.dataset.pane !== type));

        const contactLabel = document.getElementById('contact-name-label');
        if (contactLabel) {
            contactLabel.textContent = contactLabels[type] || 'اسم الجهة';
        }

        document.querySelectorAll('#ownership-section .commission-field').forEach(el => {
            el.classList.toggle('hidden', !['trader', 'broker'].includes(type));
        });
        document.querySelectorAll('#ownership-section .investor-field').forEach(el => {
            el.classList.toggle('hidden', type !== 'investor');
        });
        document.querySelectorAll('#ownership-section .management-field').forEach(el => {
            el.classList.toggle('hidden', type !== 'property_management');
        });
    }

    select.addEventListener('change', syncOwnershipUI);
    syncOwnershipUI();
});
</script>
@endpush
