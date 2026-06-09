@php
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $useColors = config('project_units.use_colors', []);
    $statusColors = config('project_units.status_colors', []);
    $floors = $project->buildingFloors ?? collect();
    $hasUnits = $floors->isNotEmpty() && $floors->sum(fn ($f) => $f->units->count()) > 0;
    $unitUpdateUrl = $unitUpdateUrl ?? ($hasUnits
        ? preg_replace('/\/0(\?|$)/', '/__ID__$1', route('crm.projects.units.update', ['project' => $project, 'unit' => 0]))
        : '');
    $unitsGenerateRoute = $unitsGenerateRoute ?? route('crm.projects.units.generate', $project);
    $showDealButton = $showDealButton ?? true;
    $canEdit = $canEdit ?? auth()->user()?->can('edit-projects');
    $unitsPayload = $hasUnits
        ? $floors->flatMap(fn ($floor) => $floor->units->map(fn ($unit) => [
            'id' => $unit->id,
            'code' => $unit->code,
            'floor_id' => $floor->id,
            'floor_label' => $floor->label,
            'floor_level' => $floor->level,
            'use_type' => $unit->use_type,
            'use_label' => $unit->useTypeLabel(),
            'area_m2' => (float) $unit->area_m2,
            'price_cash' => (float) $unit->price_cash,
            'price_installment' => $unit->price_installment ? (float) $unit->price_installment : null,
            'status' => $unit->status,
            'status_label' => $unit->statusLabel(),
            'color' => $unit->meshColor(),
        ]))->values()
        : collect();
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full mb-6" id="building-units-root"
     data-units='@json($unitsPayload)'
     data-update-url="{{ $unitUpdateUrl }}"
     data-deal-url="{{ route('crm.pipeline.create', ['project_id' => $project->id]) }}"
     data-csrf="{{ csrf_token() }}"
     data-can-edit="{{ $canEdit ? '1' : '0' }}"
     data-theme="{{ $themeColor }}">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <div>
            <h2 class="font-bold font-tajawal text-gray-900">هيكل المبنى والوحدات</h2>
            <p class="text-xs text-gray-500 mt-1 font-tajawal">
                @if($hasUnits)
                    {{ $buildingSummary['floors_count'] ?? 0 }} طوابق · {{ $buildingSummary['units_count'] ?? 0 }} وحدة
                    · <span class="text-gray-700">انقر على أي وحدة للتحكم</span>
                @else
                    لم تُولَّد الوحدات بعد — استخدم التوليد التلقائي من بيانات المشروع
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($canEdit)
            <form method="POST" action="{{ $unitsGenerateRoute }}"
                  onsubmit="return confirm('{{ $hasUnits ? 'إعادة توليد الوحدات؟ سيتم حذف الوحدات الحالية (ما عدا المباعة).' : 'توليد الوحدات تلقائياً من إعدادات المبنى؟' }}')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-white font-tajawal"
                        style="background: {{ $themeColor }};">
                    {{ $hasUnits ? 'إعادة توليد الوحدات' : 'توليد الوحدات تلقائياً' }}
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($project->land_area_m2)
    <div class="px-5 sm:px-6 py-3 border-b border-gray-100 bg-gray-50 text-sm font-tajawal text-gray-600">
        مساحة الأرض: <span class="font-bold text-gray-900">{{ number_format($project->land_area_m2) }} م²</span>
        @if($project->building_config['template'] ?? null)
            <span class="text-gray-300 mx-2">|</span>
            قالب: <span class="font-semibold">{{ $project->building_config['template'] }}</span>
        @endif
    </div>
    @endif

    @if($hasUnits)
    <div class="p-5 sm:p-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4" id="unit-stats-strip">
            @foreach(config('project_units.use_types') as $key => $label)
                @php $count = $buildingSummary['by_use'][$key] ?? 0; @endphp
                @if($count > 0)
                <div class="rounded-xl border border-gray-200 p-3 text-center">
                    <div class="text-xs text-gray-500 font-tajawal">{{ $label }}</div>
                    <div class="text-xl font-bold font-tajawal" style="color: {{ $useColors[$key] ?? $themeColor }}">{{ $count }}</div>
                </div>
                @endif
            @endforeach
            <div class="rounded-xl border border-gray-200 p-3 text-center">
                <div class="text-xs text-gray-500 font-tajawal">متاح</div>
                <div class="text-xl font-bold text-green-600 font-tajawal" id="stat-available">{{ $buildingSummary['by_status']['available'] ?? 0 }}</div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-4 font-tajawal">
            <button type="button" class="floor-filter px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200 bg-white text-gray-700 active-floor-filter"
                    data-floor-id="all" style="--active-bg: {{ $themeColor }}">كل الطوابق</button>
            @foreach($floors as $floor)
            <button type="button" class="floor-filter px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                    data-floor-id="{{ $floor->id }}">{{ $floor->label }}</button>
            @endforeach
            <span class="w-px h-6 bg-gray-200 mx-1 self-center"></span>
            @foreach(config('project_units.statuses') as $key => $label)
            <button type="button" class="status-filter px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 bg-white text-gray-600"
                    data-status="{{ $key }}">{{ $label }}</button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
            <div class="xl:col-span-2 rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-2 bg-gray-50 text-xs font-bold text-gray-600 font-tajawal flex justify-between">
                    <span>المقطع العمودي التفاعلي</span>
                    <span class="text-gray-400 font-normal">انقر وحدة · مرّر للتفاصيل</span>
                </div>
                <div class="p-4 space-y-2 bg-slate-50" id="building-stack">
                    @foreach($floors->sortByDesc('sort_order') as $floor)
                    <div class="building-floor-row flex items-stretch gap-2 transition-opacity" data-floor-id="{{ $floor->id }}">
                        <div class="w-14 shrink-0 flex items-center justify-end text-[10px] font-bold text-gray-500 font-tajawal pe-1">
                            {{ $floor->label }}
                        </div>
                        <div class="flex-1 flex gap-1.5 p-1.5 rounded-lg border border-slate-200 bg-white min-h-[2.5rem]">
                            @foreach($floor->units as $unit)
                            @php
                                $fill = $statusColors[$unit->status] ?? ($useColors[$unit->use_type] ?? '#94a3b8');
                            @endphp
                            <button type="button"
                                    class="unit-chip flex-1 min-w-0 rounded-md text-white text-[10px] sm:text-xs font-bold font-tajawal py-2 px-1 truncate transition transform hover:scale-[1.03] hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-offset-1"
                                    data-unit-id="{{ $unit->id }}"
                                    data-floor-id="{{ $floor->id }}"
                                    data-status="{{ $unit->status }}"
                                    style="background: {{ $fill }}; focus-ring-color: {{ $themeColor }};"
                                    title="{{ $unit->code }} — {{ $unit->useTypeLabel() }} — {{ $unit->statusLabel() }}">
                                {{ $unit->code }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border-2 border-gray-200 p-4 font-tajawal sticky top-4 self-start" id="unit-detail-panel">
                <div id="unit-detail-empty" class="text-center py-8 text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0v-2a2 2 0 00-2-2H9a2 2 0 00-2 2v2m14 0H5"/></svg>
                    <p class="text-sm font-semibold">اختر وحدة من المخطط</p>
                    <p class="text-xs mt-1">أو من الجدول بالأسفل</p>
                </div>
                <div id="unit-detail-content" class="hidden space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="text-lg font-extrabold text-gray-900" id="detail-code">—</div>
                            <div class="text-xs text-gray-500" id="detail-floor">—</div>
                        </div>
                        <span id="detail-status-badge" class="text-xs font-bold px-2 py-1 rounded-full"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="rounded-lg bg-gray-50 p-2">
                            <div class="text-[10px] text-gray-500">الاستخدام</div>
                            <div class="font-bold" id="detail-use">—</div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-2">
                            <div class="text-[10px] text-gray-500">المساحة</div>
                            <div class="font-bold" id="detail-area">—</div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-2 col-span-2">
                            <div class="text-[10px] text-gray-500">سعر الكاش</div>
                            <div class="font-bold text-base" id="detail-cash" style="color: {{ $themeColor }}">—</div>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-2 col-span-2" id="detail-installment-wrap">
                            <div class="text-[10px] text-gray-500">سعر القسط</div>
                            <div class="font-bold" id="detail-installment">—</div>
                        </div>
                    </div>
                    @if($canEdit)
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">تغيير الحالة</label>
                        <select id="detail-status-select" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                            @foreach(config('project_units.statuses') as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p id="detail-save-msg" class="text-xs mt-1 hidden"></p>
                    </div>
                    @endif
                    @if($showDealButton)
                    <a href="#" id="detail-deal-link"
                       class="block w-full text-center py-2.5 rounded-xl text-sm font-bold text-white font-tajawal"
                       style="background: {{ $themeColor }};">
                        إنشاء صفقة على هذه الوحدة
                    </a>
                    @endif
                </div>
            </div>
        </div>

        @foreach($floors as $floor)
        <div class="mb-4 last:mb-0 floor-table-block" data-floor-id="{{ $floor->id }}">
            <h3 class="text-sm font-bold text-gray-800 font-tajawal mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" style="background: {{ $themeColor }}"></span>
                {{ $floor->label }}
                <span class="text-gray-400 font-normal">({{ $floor->units->count() }} وحدة)</span>
            </h3>
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full text-sm font-tajawal">
                    <thead class="bg-gray-50 text-xs text-gray-500">
                        <tr>
                            <th class="px-4 py-2 text-right">الكود</th>
                            <th class="px-4 py-2 text-right">الاستخدام</th>
                            <th class="px-4 py-2 text-center">المساحة</th>
                            <th class="px-4 py-2 text-center">كاش</th>
                            <th class="px-4 py-2 text-center">قسط</th>
                            <th class="px-4 py-2 text-center">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($floor->units as $unit)
                        <tr class="unit-table-row hover:bg-gray-50 cursor-pointer transition"
                            data-unit-id="{{ $unit->id }}"
                            data-floor-id="{{ $floor->id }}"
                            data-status="{{ $unit->status }}">
                            <td class="px-4 py-2 font-semibold text-gray-900">{{ $unit->code }}</td>
                            <td class="px-4 py-2">
                                <span class="text-xs font-bold px-2 py-0.5 rounded unit-use-badge" style="color: {{ $useColors[$unit->use_type] ?? '#64748b' }}; background: {{ ($useColors[$unit->use_type] ?? '#64748b') }}18">
                                    {{ $unit->useTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">{{ number_format($unit->area_m2) }} م²</td>
                            <td class="px-4 py-2 text-center font-semibold unit-cash-cell">{{ $money($unit->price_cash) }}</td>
                            <td class="px-4 py-2 text-center text-gray-600 unit-installment-cell">{{ $unit->price_installment ? $money($unit->price_installment) : '—' }}</td>
                            <td class="px-4 py-2 text-center">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full unit-status-badge" style="color: {{ $statusColors[$unit->status] ?? '#64748b' }}; background: {{ ($statusColors[$unit->status] ?? '#64748b') }}18">
                                    {{ $unit->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="p-8 text-center text-gray-400 font-tajawal">
        <p class="mb-2">لا توجد وحدات مفصّلة لهذا المشروع.</p>
        <p class="text-sm">أضف <code class="text-gray-500">building_config</code> أو شغّل بذرة مشروع 5B ثم اضغط «توليد الوحدات».</p>
    </div>
    @endif
</div>

@if($hasUnits)
@push('scripts')
<script>
(function () {
    const root = document.getElementById('building-units-root');
    if (!root) return;

    const units = JSON.parse(root.dataset.units || '[]');
    const unitMap = Object.fromEntries(units.map(u => [String(u.id), u]));
    const updateUrlTemplate = root.dataset.updateUrl || '';
    const dealBaseUrl = root.dataset.dealUrl || '';
    const csrf = root.dataset.csrf || '';
    const canEdit = root.dataset.canEdit === '1';
    const themeColor = root.dataset.theme || '#3b82f6';

    const statusColors = @json($statusColors);
    const useColors = @json($useColors);
    const moneyFmt = (n) => new Intl.NumberFormat('ar-EG', { maximumFractionDigits: 0 }).format(n) + ' ج.م';

    let selectedId = null;
    let activeFloor = 'all';
    let activeStatus = null;

    const panelEmpty = document.getElementById('unit-detail-empty');
    const panelContent = document.getElementById('unit-detail-content');

    window.__buildingSelectUnit = function (id) { selectUnit(id); };

    function selectUnit(id) {
        const unit = unitMap[String(id)];
        if (!unit) return;
        selectedId = String(id);

        document.querySelectorAll('.unit-chip').forEach(el => {
            const on = el.dataset.unitId === selectedId;
            el.style.outline = on ? `3px solid ${themeColor}` : '';
            el.style.outlineOffset = on ? '2px' : '';
            el.style.transform = on ? 'scale(1.05)' : '';
        });
        document.querySelectorAll('.unit-table-row').forEach(el => {
            el.classList.toggle('bg-blue-50', el.dataset.unitId === selectedId);
            el.classList.toggle('ring-2', el.dataset.unitId === selectedId);
            el.style.setProperty('--tw-ring-color', themeColor);
        });

        panelEmpty.classList.add('hidden');
        panelContent.classList.remove('hidden');

        document.getElementById('detail-code').textContent = unit.code;
        document.getElementById('detail-floor').textContent = unit.floor_label;
        document.getElementById('detail-use').textContent = unit.use_label;
        document.getElementById('detail-area').textContent = unit.area_m2.toLocaleString('ar-EG') + ' م²';
        document.getElementById('detail-cash').textContent = moneyFmt(unit.price_cash);

        const instWrap = document.getElementById('detail-installment-wrap');
        if (unit.price_installment) {
            instWrap.classList.remove('hidden');
            document.getElementById('detail-installment').textContent = moneyFmt(unit.price_installment);
        } else {
            instWrap.classList.add('hidden');
        }

        const badge = document.getElementById('detail-status-badge');
        badge.textContent = unit.status_label;
        badge.style.color = statusColors[unit.status] || '#64748b';
        badge.style.background = (statusColors[unit.status] || '#64748b') + '22';

        const sel = document.getElementById('detail-status-select');
        if (sel) sel.value = unit.status;

        const dealLink = document.getElementById('detail-deal-link');
        const dealUrl = new URL(dealBaseUrl, window.location.origin);
        dealUrl.searchParams.set('product_service', unit.code + ' — ' + unit.use_label + ' (' + unit.area_m2 + ' م²)');
        dealUrl.searchParams.set('estimated_value', String(unit.price_cash || 0));
        dealLink.href = dealUrl.toString();

        const row = document.querySelector(`.unit-table-row[data-unit-id="${selectedId}"]`);
        if (row) row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function applyFilters() {
        document.querySelectorAll('.building-floor-row').forEach(row => {
            const show = activeFloor === 'all' || row.dataset.floorId === String(activeFloor);
            row.style.opacity = show ? '1' : '0.25';
            row.style.pointerEvents = show ? '' : 'none';
        });
        document.querySelectorAll('.unit-chip, .unit-table-row').forEach(el => {
            const status = el.dataset.status;
            const match = !activeStatus || status === activeStatus;
            el.style.display = match ? '' : 'none';
        });
        document.querySelectorAll('.floor-table-block').forEach(block => {
            const show = activeFloor === 'all' || block.dataset.floorId === String(activeFloor);
            block.style.display = show ? '' : 'none';
        });
    }

    root.querySelectorAll('.unit-chip').forEach(btn => {
        btn.addEventListener('click', () => selectUnit(btn.dataset.unitId));
    });
    root.querySelectorAll('.unit-table-row').forEach(row => {
        row.addEventListener('click', () => selectUnit(row.dataset.unitId));
    });

    root.querySelectorAll('.floor-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            activeFloor = btn.dataset.floorId;
            root.querySelectorAll('.floor-filter').forEach(b => {
                b.classList.remove('active-floor-filter', 'text-white');
                b.classList.add('text-gray-600');
                b.style.background = '';
            });
            btn.classList.add('active-floor-filter', 'text-white');
            btn.classList.remove('text-gray-600');
            btn.style.background = themeColor;
            applyFilters();
        });
    });

    root.querySelectorAll('.status-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            if (activeStatus === btn.dataset.status) {
                activeStatus = null;
                btn.style.background = '';
                btn.classList.remove('text-white');
            } else {
                activeStatus = btn.dataset.status;
                root.querySelectorAll('.status-filter').forEach(b => {
                    b.style.background = '';
                    b.classList.remove('text-white');
                });
                btn.style.background = statusColors[activeStatus] || themeColor;
                btn.classList.add('text-white');
            }
            applyFilters();
        });
    });

    const statusSelect = document.getElementById('detail-status-select');
    if (statusSelect && canEdit) {
        statusSelect.addEventListener('change', async () => {
            if (!selectedId) return;
            const msg = document.getElementById('detail-save-msg');
            const url = updateUrlTemplate.replace('__ID__', selectedId);
            msg.classList.remove('hidden', 'text-green-600', 'text-red-600');
            msg.textContent = 'جاري الحفظ...';
            msg.classList.add('text-gray-500');

            try {
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ status: statusSelect.value }),
                });
                const data = await res.json();
                if (!res.ok || !data.ok) throw new Error('فشل التحديث');

                const u = data.unit;
                unitMap[selectedId].status = u.status;
                unitMap[selectedId].status_label = u.status_label;
                unitMap[selectedId].color = u.color;

                document.querySelectorAll(`[data-unit-id="${selectedId}"]`).forEach(el => {
                    el.dataset.status = u.status;
                    if (el.classList.contains('unit-chip')) {
                        el.style.background = u.color;
                    }
                    if (el.classList.contains('unit-table-row')) {
                        const badge = el.querySelector('.unit-status-badge');
                        if (badge) {
                            badge.textContent = u.status_label;
                            badge.style.color = u.color;
                            badge.style.background = u.color + '22';
                        }
                    }
                });

                selectUnit(selectedId);
                const statAvail = document.getElementById('stat-available');
                if (statAvail && data.project) statAvail.textContent = data.project.available_units;

                msg.textContent = 'تم تحديث الحالة';
                msg.classList.remove('text-gray-500');
                msg.classList.add('text-green-600');
            } catch (e) {
                msg.textContent = 'تعذر الحفظ — حاول مرة أخرى';
                msg.classList.add('text-red-600');
            }
        });
    }

    const first = units[0];
    if (first) selectUnit(first.id);
})();
</script>
<style>
    .active-floor-filter { color: #fff !important; }
    #building-units-root .unit-chip { cursor: pointer; }
    #building-units-root .unit-table-row.ring-2 { --tw-ring-color: {{ $themeColor }}; }
</style>
@endpush
@endif
