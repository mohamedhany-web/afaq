@php
    $classificationTypes = \App\Models\Project::CLASSIFICATION_TYPES;
    $concreteTypes = config('project_inventory.developer_tables', config('project_classifications.concrete', []));
    $classColors = config('project_classifications.colors', []);
    $selectedTypes = old(
        'property_types',
        isset($project) ? $project->resolvedPropertyTypes() : ['residential']
    );
    if (! is_array($selectedTypes)) {
        $selectedTypes = \App\Models\Project::normalizePropertyTypes($selectedTypes);
    }
    $storedPricing = old(
        'classification_pricing',
        isset($project) ? ($project->classificationPricing() ?? []) : []
    );
    $input = $input ?? 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = $label ?? 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $inventorySource = old('inventory_source', $project->inventory_source ?? 'developer');
    $pricingFieldsDisabled = $inventorySource !== 'developer';
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full {{ $pricingFieldsDisabled ? 'hidden' : '' }}" id="classification-pricing-section">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        الأسعار وخطط السيلز حسب التصنيف (تجاري · سكني · طبي)
        <p class="text-xs font-normal text-gray-500 mt-1">لكل تصنيف: السعر والمساحة + نسبة البناء والخصم والتحميل ووديعة الصيانة وخطة السداد</p>
    </div>
    <div class="p-5 sm:p-6 space-y-4" id="classification-pricing-rows">
        @foreach($concreteTypes as $key => $txt)
        @php
            $row = $storedPricing[$key] ?? [];
            $color = $classColors[$key] ?? $themeColor;
        @endphp
        <div class="classification-pricing-row rounded-xl border-2 border-gray-200 p-4 transition-all"
             data-classification="{{ $key }}"
             style="border-color: {{ $color }}30;">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-3 h-3 rounded-full shrink-0" style="background: {{ $color }}"></span>
                <h3 class="text-sm font-bold text-gray-900 font-tajawal">{{ $txt }}</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="{{ $label }}">السعر من (ج.م)</label>
                    <input type="number" name="classification_pricing[{{ $key }}][price_from]" min="0" step="0.01"
                           value="{{ old("classification_pricing.{$key}.price_from", $row['price_from'] ?? '') }}"
                           class="{{ $input }} classification-price-input" placeholder="مثال: 1500000">
                </div>
                <div>
                    <label class="{{ $label }}">السعر إلى (ج.م)</label>
                    <input type="number" name="classification_pricing[{{ $key }}][price_to]" min="0" step="0.01"
                           value="{{ old("classification_pricing.{$key}.price_to", $row['price_to'] ?? '') }}"
                           class="{{ $input }} classification-price-input" placeholder="مثال: 4500000">
                </div>
                <div>
                    <label class="{{ $label }}">المساحة من (م²)</label>
                    <input type="number" name="classification_pricing[{{ $key }}][area_from]" min="0" step="0.01"
                           value="{{ old("classification_pricing.{$key}.area_from", $row['area_from'] ?? '') }}"
                           class="{{ $input }}" placeholder="مثال: 90">
                </div>
                <div>
                    <label class="{{ $label }}">المساحة إلى (م²)</label>
                    <input type="number" name="classification_pricing[{{ $key }}][area_to]" min="0" step="0.01"
                           value="{{ old("classification_pricing.{$key}.area_to", $row['area_to'] ?? '') }}"
                           class="{{ $input }}" placeholder="مثال: 170">
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-3 pt-3 border-t border-gray-100">
                <div>
                    <label class="{{ $label }}">نسبة البناء %</label>
                    <input type="number" name="classification_pricing[{{ $key }}][building_percent]" min="0" max="100" step="0.01"
                           value="{{ old("classification_pricing.{$key}.building_percent", $row['building_percent'] ?? '') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">نسبة الخصم %</label>
                    <input type="number" name="classification_pricing[{{ $key }}][discount_percent]" min="0" max="100" step="0.01"
                           value="{{ old("classification_pricing.{$key}.discount_percent", $row['discount_percent'] ?? '') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">نسبة التحميل %</label>
                    <input type="number" name="classification_pricing[{{ $key }}][loading_percent]" min="0" max="100" step="0.01"
                           value="{{ old("classification_pricing.{$key}.loading_percent", $row['loading_percent'] ?? '') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">وديعة الصيانة (ج.م)</label>
                    <input type="number" name="classification_pricing[{{ $key }}][maintenance_deposit]" min="0" step="0.01"
                           value="{{ old("classification_pricing.{$key}.maintenance_deposit", $row['maintenance_deposit'] ?? '') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">مقدم افتراضي %</label>
                    <input type="number" name="classification_pricing[{{ $key }}][default_down_percent]" min="0" max="100" step="0.01"
                           value="{{ old("classification_pricing.{$key}.default_down_percent", $row['default_down_percent'] ?? '') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">سنوات التقسيط</label>
                    <input type="number" name="classification_pricing[{{ $key }}][default_installment_years]" min="0" max="40"
                           value="{{ old("classification_pricing.{$key}.default_installment_years", $row['default_installment_years'] ?? '') }}" class="{{ $input }}">
                </div>
            </div>
            @error("classification_pricing.{$key}.price_to")<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
            @error("classification_pricing.{$key}.area_to")<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        @endforeach
        <p id="classification-pricing-hint" class="text-xs text-gray-400 font-tajawal hidden">
            اختر تصنيفاً واحداً على الأقل أعلاه لإدخال الأسعار والمساحات.
        </p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('.classification-pricing-row');
    const hint = document.getElementById('classification-pricing-hint');
    const typeChecks = document.querySelectorAll('input[name="property_types[]"]');

    function syncPricingRows() {
        const selected = Array.from(typeChecks).filter(c => c.checked).map(c => c.value);
        const isMixed = selected.includes('mixed');
        const concrete = @json(array_keys($concreteTypes));
        let visible = 0;

        rows.forEach(row => {
            const key = row.dataset.classification;
            const show = isMixed || selected.includes(key);
            row.classList.toggle('hidden', !show);
            row.querySelectorAll('input').forEach(inp => { inp.disabled = !show; });
            if (show) visible++;
        });

        if (hint) hint.classList.toggle('hidden', visible > 0);
    }

    typeChecks.forEach(c => c.addEventListener('change', syncPricingRows));
    syncPricingRows();
});
</script>
@endpush
