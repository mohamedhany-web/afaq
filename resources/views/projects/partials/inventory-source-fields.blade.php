@php
    $inventorySource = old('inventory_source', $project->inventory_source ?? 'developer');
    $ownershipType = \App\Models\Project::normalizeOwnershipType(old('ownership_type', $project->ownership_type ?? 'developer')) ?? 'developer';
    $developers = $developers ?? collect();
    $input = $input ?? 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = $label ?? 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $sources = \App\Models\Project::inventorySourceLabels();
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full mb-6" id="inventory-source-section">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        الخطوة 1 — نوع المخزون العقاري *
        <p class="text-xs font-normal text-gray-500 mt-1">اختر مصدر الوحدات أولاً — تظهر بعدها الحقول المناسبة</p>
    </div>
    <div class="p-5 sm:p-6">
        <input type="hidden" name="inventory_source" id="inventory_source" value="{{ $inventorySource }}">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach($sources as $key => $txt)
            <button type="button"
                    class="inventory-source-card text-right p-4 rounded-2xl border-2 transition font-tajawal hover:shadow-md"
                    data-source="{{ $key }}"
                    style="{{ $inventorySource === $key ? 'border-color:' . $themeColor . '; background:' . $themeColor . '10;' : '' }}">
                <p class="font-bold text-gray-900">{{ $txt }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    @if($key === 'company') وحدات مملوكة لأفاق — إدخال يدوي للوحدات
                    @elseif($key === 'non_company') وحدات طرف ثالث — نفس بيانات الوحدة
                    @else مشاريع مطورين — جداول سكني / تجاري / طبي
                    @endif
                </p>
            </button>
            @endforeach
        </div>
        @error('inventory_source')<p class="mt-2 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror

        <div id="pane-developer" class="inventory-pane mt-5 {{ $inventorySource !== 'developer' ? 'hidden' : '' }}">
            <label class="{{ $label }}">المطور العقاري *</label>
            <select name="real_estate_developer_id" id="developer_id_select" class="{{ $input }}" @if($inventorySource === 'developer') required @endif @disabled($inventorySource !== 'developer')>
                <option value="">— اختر المطور —</option>
                @foreach($developers as $dev)
                <option value="{{ $dev->id }}" @selected((string) old('real_estate_developer_id', $project->real_estate_developer_id ?? '') === (string) $dev->id)>
                    {{ $dev->name }}@if($dev->city) — {{ $dev->city }}@endif
                </option>
                @endforeach
            </select>
            <input type="hidden" name="developer_name" id="developer_name_hidden" value="{{ old('developer_name', $project->developer_name ?? '') }}">
            @if($developers->isEmpty())
            <p class="text-xs text-amber-700 mt-2 font-tajawal">لا يوجد مطورون بتعاقد نشط — أضفهم من <a href="{{ route('admin.developers.index') }}" class="underline font-bold">إدارة المطورين</a>.</p>
            @endif
            @error('real_estate_developer_id')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>

        <div id="pane-non-company" class="inventory-pane mt-5 {{ $inventorySource !== 'non_company' ? 'hidden' : '' }}">
            <label class="{{ $label }}">نوع ملكية «وحدات الغير»</label>
            <select id="non_company_ownership" class="{{ $input }}">
                @foreach(config('project_inventory.non_company_ownership', []) as $val => $txt)
                <option value="{{ $val }}" @selected($ownershipType === $val)>{{ $txt }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="ownership_type" id="ownership_type_hidden" value="{{ $ownershipType }}">
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hiddenSource = document.getElementById('inventory_source');
    const hiddenOwnership = document.getElementById('ownership_type_hidden');
    const nonCompanySelect = document.getElementById('non_company_ownership');
    const devSelect = document.getElementById('developer_id_select');
    const devNameHidden = document.getElementById('developer_name_hidden');
    const cards = document.querySelectorAll('.inventory-source-card');
    const panes = document.querySelectorAll('.inventory-pane');
    const detailsWrap = document.getElementById('project-details-after-source');
    const manualSection = document.getElementById('manual-units-section');
    const pricingSection = document.getElementById('classification-pricing-section');
    const ownershipSection = document.getElementById('ownership-section');
    const themeColor = @json($themeColor);

    function syncDeveloperName() {
        if (!devSelect || !devNameHidden) return;
        const opt = devSelect.options[devSelect.selectedIndex];
        devNameHidden.value = opt && opt.value ? opt.textContent.split('—')[0].trim() : '';
    }

    function setSectionFieldsEnabled(section, enabled) {
        if (!section) return;
        section.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.type === 'hidden') return;
            el.disabled = !enabled;
            if (el.classList.contains('manual-unit-area') && el.dataset.requiredWhenVisible === '1') {
                if (enabled) el.setAttribute('required', 'required');
                else el.removeAttribute('required');
            }
        });
    }

    function setSource(source) {
        if (!hiddenSource) return;
        hiddenSource.value = source;
        cards.forEach(c => {
            const active = c.dataset.source === source;
            c.style.borderColor = active ? themeColor : '';
            c.style.background = active ? themeColor + '10' : '';
        });
        panes.forEach(p => p.classList.add('hidden'));
        const pane = document.getElementById('pane-' + source);
        if (pane) pane.classList.remove('hidden');

        if (hiddenOwnership) {
            if (source === 'company') hiddenOwnership.value = 'afaq_private';
            else if (source === 'developer') hiddenOwnership.value = 'developer';
            else if (nonCompanySelect) hiddenOwnership.value = nonCompanySelect.value;
        }

        const showDetails = !!source;
        const showManual = source === 'company' || source === 'non_company';
        const showPricing = source === 'developer';

        if (detailsWrap) detailsWrap.classList.toggle('hidden', !showDetails);
        if (manualSection) manualSection.classList.toggle('hidden', !showManual);
        if (pricingSection) pricingSection.classList.toggle('hidden', !showPricing);
        if (ownershipSection) ownershipSection.classList.toggle('hidden', true);

        setSectionFieldsEnabled(manualSection, showManual);
        setSectionFieldsEnabled(pricingSection, showPricing);
        const devPane = document.getElementById('pane-developer');
        setSectionFieldsEnabled(devPane, source === 'developer');

        const devSelectEl = document.getElementById('developer_id_select');
        if (devSelectEl) {
            devSelectEl.disabled = source !== 'developer';
            if (source === 'developer') devSelectEl.setAttribute('required', 'required');
            else devSelectEl.removeAttribute('required');
        }
    }

    document.querySelector('form[action*="projects"]')?.addEventListener('submit', function () {
        const source = hiddenSource?.value || 'developer';
        const devPane = document.getElementById('pane-developer');
        setSectionFieldsEnabled(manualSection, source === 'company' || source === 'non_company');
        setSectionFieldsEnabled(pricingSection, source === 'developer');
        setSectionFieldsEnabled(devPane, source === 'developer');
        const devSelectEl = document.getElementById('developer_id_select');
        if (devSelectEl) devSelectEl.disabled = source !== 'developer';
    });

    cards.forEach(c => c.addEventListener('click', () => setSource(c.dataset.source)));
    nonCompanySelect?.addEventListener('change', () => {
        if (hiddenOwnership && hiddenSource?.value === 'non_company') {
            hiddenOwnership.value = nonCompanySelect.value;
        }
    });
    devSelect?.addEventListener('change', syncDeveloperName);

    setSource(hiddenSource?.value || 'developer');
    syncDeveloperName();
});
</script>
@endpush
