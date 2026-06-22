@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $input = $input ?? 'w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm font-tajawal';
    $directions = config('project_inventory.directions', []);
    $useTypes = config('project_units.use_types', []);
    $existing = old('manual_units');
    if ($existing === null && isset($project)) {
        $existing = $project->units()
            ->with('paymentPlans')
            ->whereHas('floor', fn ($q) => $q->where('label', 'يدوي'))
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'use_type' => $u->use_type,
                'area_m2' => $u->area_m2,
                'direction' => $u->direction,
                'floor_number' => $u->floor_number,
                'floor_label' => $u->floor_label,
                'apartment_number' => $u->apartment_number,
                'unit_price_total' => $u->unit_price_total ?? $u->price_cash,
                'building_percent' => $u->paymentPlans->first()?->building_percent,
                'discount_percent' => $u->paymentPlans->first()?->discount_percent,
                'loading_percent' => $u->paymentPlans->first()?->loading_percent,
                'maintenance_deposit' => $u->paymentPlans->first()?->maintenance_deposit,
                'down_percent' => $u->paymentPlans->first()?->down_percent,
                'years' => $u->paymentPlans->first()?->years,
            ])->values()->all();
    }
    $existing = is_array($existing) ? $existing : [];
    if ($existing === []) {
        $existing = [[]];
    }
    $inventorySource = old('inventory_source', $project->inventory_source ?? 'developer');
    $manualFieldsDisabled = $inventorySource === 'developer';
@endphp

<div id="manual-units-section" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full {{ $manualFieldsDisabled ? 'hidden' : '' }}">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900 flex flex-wrap items-center justify-between gap-2"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <div>
            جدول الوحدات (يدوي)
            <p class="text-xs font-normal text-gray-500 mt-1">مساحة · اتجاه · طابق · دور · رقم الشقة · السعر · خطة السداد</p>
        </div>
        <button type="button" id="add-manual-unit-row" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white" style="background:{{ $themeColor }}">+ وحدة</button>
    </div>
    <div class="p-3 overflow-x-auto">
        <table class="w-full text-xs font-tajawal min-w-[1100px]" id="manual-units-table">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-2 text-right">تصنيف</th>
                    <th class="p-2 text-right">مساحة</th>
                    <th class="p-2 text-right">اتجاه</th>
                    <th class="p-2 text-right">رقم الطابق</th>
                    <th class="p-2 text-right">الدور</th>
                    <th class="p-2 text-right">رقم الشقة</th>
                    <th class="p-2 text-right">سعر الوحدة</th>
                    <th class="p-2 text-right">بناء%</th>
                    <th class="p-2 text-right">خصم%</th>
                    <th class="p-2 text-right">تحميل%</th>
                    <th class="p-2 text-right">صيانة</th>
                    <th class="p-2 text-right">مقدم%</th>
                    <th class="p-2 text-right">سنوات</th>
                    <th class="p-2"></th>
                </tr>
            </thead>
            <tbody id="manual-units-body">
                @foreach($existing as $i => $row)
                <tr class="manual-unit-row border-t border-gray-100">
                    @if(!empty($row['id']))<input type="hidden" name="manual_units[{{ $i }}][id]" value="{{ $row['id'] }}">@endif
                    <td class="p-1"><select name="manual_units[{{ $i }}][use_type]" class="{{ $input }}" @disabled($manualFieldsDisabled)>@foreach($useTypes as $k=>$t)<option value="{{ $k }}" @selected(($row['use_type']??'residential')===$k)>{{ $t }}</option>@endforeach</select></td>
                    <td class="p-1"><input type="number" step="0.01" min="0" name="manual_units[{{ $i }}][area_m2]" value="{{ $row['area_m2'] ?? '' }}" class="{{ $input }} manual-unit-area" data-required-when-visible="1" @disabled($manualFieldsDisabled) @if(!$manualFieldsDisabled) required @endif></td>
                    <td class="p-1"><select name="manual_units[{{ $i }}][direction]" class="{{ $input }}" @disabled($manualFieldsDisabled)><option value="">—</option>@foreach($directions as $k=>$t)<option value="{{ $k }}" @selected(($row['direction']??'')===$k)>{{ $t }}</option>@endforeach</select></td>
                    <td class="p-1"><input name="manual_units[{{ $i }}][floor_number]" value="{{ $row['floor_number'] ?? '' }}" class="{{ $input }}" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><input name="manual_units[{{ $i }}][floor_label]" value="{{ $row['floor_label'] ?? '' }}" class="{{ $input }}" placeholder="أول / ثاني" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><input name="manual_units[{{ $i }}][apartment_number]" value="{{ $row['apartment_number'] ?? '' }}" class="{{ $input }}" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><input type="number" step="0.01" min="0" name="manual_units[{{ $i }}][unit_price_total]" value="{{ $row['unit_price_total'] ?? '' }}" class="{{ $input }}" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><input type="number" step="0.01" name="manual_units[{{ $i }}][building_percent]" value="{{ $row['building_percent'] ?? '' }}" class="{{ $input }} w-16" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><input type="number" step="0.01" name="manual_units[{{ $i }}][discount_percent]" value="{{ $row['discount_percent'] ?? '' }}" class="{{ $input }} w-16" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><input type="number" step="0.01" name="manual_units[{{ $i }}][loading_percent]" value="{{ $row['loading_percent'] ?? '' }}" class="{{ $input }} w-16" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><input type="number" step="0.01" name="manual_units[{{ $i }}][maintenance_deposit]" value="{{ $row['maintenance_deposit'] ?? '' }}" class="{{ $input }} w-20" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><input type="number" step="0.01" name="manual_units[{{ $i }}][down_percent]" value="{{ $row['down_percent'] ?? '' }}" class="{{ $input }} w-16" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><input type="number" min="0" max="40" name="manual_units[{{ $i }}][years]" value="{{ $row['years'] ?? '' }}" class="{{ $input }} w-14" @disabled($manualFieldsDisabled)></td>
                    <td class="p-1"><button type="button" class="remove-manual-row text-red-500 font-bold px-2">×</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.getElementById('manual-units-body');
    const addBtn = document.getElementById('add-manual-unit-row');
    if (!body || !addBtn) return;

    let rowIndex = body.querySelectorAll('.manual-unit-row').length;

    function bindRemove(btn) {
        btn.addEventListener('click', function () {
            const rows = body.querySelectorAll('.manual-unit-row');
            if (rows.length > 1) this.closest('tr').remove();
        });
    }

    body.querySelectorAll('.remove-manual-row').forEach(bindRemove);

    addBtn.addEventListener('click', function () {
        const first = body.querySelector('.manual-unit-row');
        if (!first) return;
        const clone = first.cloneNode(true);
        clone.querySelectorAll('input, select').forEach(el => {
            if (el.type === 'hidden') { el.remove(); return; }
            const name = el.getAttribute('name');
            if (name) el.setAttribute('name', name.replace(/\[\d+\]/, '[' + rowIndex + ']'));
            if (el.tagName === 'SELECT') el.selectedIndex = 0;
            else el.value = '';
        });
        body.appendChild(clone);
        clone.querySelector('.remove-manual-row')?.addEventListener('click', function () {
            if (body.querySelectorAll('.manual-unit-row').length > 1) clone.remove();
        });
        rowIndex++;
    });
});
</script>
@endpush
