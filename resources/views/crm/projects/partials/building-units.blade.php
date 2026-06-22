@php
    use App\Http\Controllers\Crm\CrmProjectUnitController;
    use App\Support\ProjectUnitNumbering;
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $useColors = config('project_units.use_colors', []);
    $statusColors = config('project_units.status_colors', []);
    $floors = $project->buildingFloors ?? collect();
    $hasUnits = $floors->isNotEmpty() && $floors->sum(fn ($f) => $f->units->count()) > 0;
    $projectsRoutePrefix = $projectsRoutePrefix ?? 'crm.projects';
    $pr = fn (string $action, mixed $params = []) => route($projectsRoutePrefix . '.' . $action, $params);
    $unitUpdateUrl = $unitUpdateUrl ?? ($hasUnits
        ? preg_replace('/\/0(\?|$)/', '/__ID__$1', $pr('units.update', ['project' => $project, 'unit' => 0]))
        : '');
    $unitShowUrl = $unitShowUrl ?? ($hasUnits
        ? preg_replace('/\/0(\?|$)/', '/__ID__$1', $pr('units.show', ['project' => $project, 'unit' => 0]))
        : '');
    $unitsGenerateRoute = $unitsGenerateRoute ?? $pr('units.generate', $project);
    $showDealButton = $showDealButton ?? true;
    $canEdit = $canEdit ?? auth()->user()?->can('update', $project);
    $unitsRenumberRoute = $unitsRenumberRoute ?? $pr('units.renumber', $project);
    $unitsPayload = $hasUnits
        ? $floors->flatMap(fn ($floor) => $floor->units->map(function ($unit) use ($unitShowUrl) {
            $payload = CrmProjectUnitController::unitPayload($unit);
            $payload['show_url'] = str_replace('__ID__', (string) $unit->id, $unitShowUrl);

            return $payload;
        }))->values()
        : collect();
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full mb-6" id="building-units-root"
     data-units='@json($unitsPayload)'
     data-update-url="{{ $unitUpdateUrl }}"
     data-show-url="{{ $unitShowUrl }}"
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
                    · <span class="text-gray-700">انقر رقم الوحدة أو «عرض التفاصيل» لبيانات كاملة</span>
                    · <span class="text-gray-500">ترقيم: B · GF · FF · SF · TF</span>
                @else
                    لم تُولَّد الوحدات بعد — استخدم التوليد التلقائي من بيانات المشروع
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($canEdit && $hasUnits)
            <form method="POST" action="{{ $unitsRenumberRoute }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold border-2 font-tajawal hover:bg-gray-50"
                        style="border-color: {{ $themeColor }}40; color: {{ $themeColor }};">
                    تحديث الترقيم (B · GF · FF)
                </button>
            </form>
            @endif
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
                <div class="rounded-xl border border-gray-200 p-3 text-center" data-use-type="{{ $key }}">
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
                    data-floor-id="{{ $floor->id }}" title="{{ ProjectUnitNumbering::floorLabelEn($floor->level) }}">
                {{ $floor->label }}
                <span class="text-gray-400 font-mono" dir="ltr">({{ ProjectUnitNumbering::floorPrefix($floor->level) }})</span>
            </button>
            @endforeach
            <span class="w-px h-6 bg-gray-200 mx-1 self-center"></span>
            @foreach(config('project_units.statuses') as $key => $label)
            <button type="button" class="status-filter px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 bg-white text-gray-600"
                    data-status="{{ $key }}">{{ $label }}</button>
            @endforeach
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-between gap-2 mb-3">
                <h3 class="text-sm font-bold text-gray-800 font-tajawal">بطاقات الوحدات</h3>
                <span class="text-xs text-gray-400 font-tajawal" id="unit-cards-count">{{ $unitsPayload->count() }} وحدة</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3" id="unit-cards-grid">
                @foreach($floors as $floor)
                    @foreach($floor->units as $unit)
                    @php
                        $fill = $statusColors[$unit->status] ?? ($useColors[$unit->use_type] ?? '#94a3b8');
                    @endphp
                    <article class="unit-card rounded-xl border-2 border-gray-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow font-tajawal"
                             data-unit-id="{{ $unit->id }}"
                             data-floor-id="{{ $floor->id }}"
                             data-status="{{ $unit->status }}"
                             data-use-type="{{ $unit->use_type }}"
                             style="border-color: {{ $fill }}33;">
                        <div class="px-3 py-2.5 flex items-center justify-between gap-2" style="background: {{ $fill }}14;">
                            <button type="button"
                                    class="unit-code-link text-lg font-extrabold text-gray-900 hover:underline font-mono tracking-tight"
                                    dir="ltr"
                                    data-unit-id="{{ $unit->id }}"
                                    title="عرض تفاصيل {{ $unit->code }}">
                                {{ $unit->code }}
                            </button>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full unit-card-status"
                                  style="color: {{ $fill }}; background: {{ $fill }}22;">
                                {{ $unit->statusLabel() }}
                            </span>
                        </div>
                        <div class="px-3 py-2 space-y-1.5 text-xs text-gray-600">
                            <div class="flex justify-between gap-2">
                                <span class="text-gray-400">الطابق</span>
                                <span class="font-semibold text-gray-800">{{ $floor->label }}</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-gray-400">الاستخدام</span>
                                <span class="font-semibold" style="color: {{ $useColors[$unit->use_type] ?? '#64748b' }}">{{ $unit->useTypeLabel() }}</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-gray-400">المساحة</span>
                                <span class="font-semibold text-gray-800">{{ number_format($unit->area_m2) }} م²</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-gray-400">كاش</span>
                                <span class="font-bold" style="color: {{ $themeColor }}">{{ $money($unit->price_cash) }}</span>
                            </div>
                            @if($unit->price_installment)
                            <div class="flex justify-between gap-2">
                                <span class="text-gray-400">قسط</span>
                                <span class="font-semibold text-gray-700">{{ $money($unit->price_installment) }}</span>
                            </div>
                            @endif
                        </div>
                        <button type="button"
                                class="unit-details-btn w-full py-2.5 text-xs font-bold text-white font-tajawal hover:brightness-110 transition"
                                data-unit-id="{{ $unit->id }}"
                                style="background: {{ $themeColor }};">
                            عرض التفاصيل
                        </button>
                    </article>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
            <div class="xl:col-span-2 rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-2 bg-gray-50 text-xs font-bold text-gray-600 font-tajawal flex justify-between">
                    <span>المقطع العمودي التفاعلي</span>
                    <span class="text-gray-400 font-normal">انقر رقم الوحدة للتفاصيل الكاملة</span>
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
                                    class="unit-chip unit-code-link flex-1 min-w-0 rounded-md text-white text-[10px] sm:text-xs font-bold font-tajawal py-2 px-1 truncate transition transform hover:scale-[1.03] hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-offset-1"
                                    data-unit-id="{{ $unit->id }}"
                                    data-floor-id="{{ $floor->id }}"
                                    data-status="{{ $unit->status }}"
                                    data-use-type="{{ $unit->use_type }}"
                                    style="background: {{ $fill }}; focus-ring-color: {{ $themeColor }};"
                                    title="{{ $unit->code }} — {{ $unit->useTypeLabel() }} — {{ $unit->statusLabel() }} — انقر للتفاصيل">
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
                            <div class="text-lg font-extrabold text-gray-900" id="detail-code">
                                <button type="button" class="unit-code-link font-mono hover:underline" dir="ltr" id="detail-code-btn">—</button>
                            </div>
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
                    <button type="button"
                            class="unit-details-btn block w-full py-2 rounded-xl text-sm font-bold border-2 font-tajawal mt-2"
                            id="detail-open-modal-btn"
                            data-unit-id=""
                            style="border-color: {{ $themeColor }}40; color: {{ $themeColor }};">
                        عرض التفاصيل الكاملة
                    </button>
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
                            <th class="px-4 py-2 text-center">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($floor->units as $unit)
                        <tr class="unit-table-row hover:bg-gray-50 cursor-pointer transition"
                            data-unit-id="{{ $unit->id }}"
                            data-floor-id="{{ $floor->id }}"
                            data-status="{{ $unit->status }}"
                            data-use-type="{{ $unit->use_type }}">
                            <td class="px-4 py-2 font-semibold text-gray-900">
                                <button type="button"
                                        class="unit-code-link font-mono font-bold hover:underline"
                                        dir="ltr"
                                        data-unit-id="{{ $unit->id }}"
                                        style="color: {{ $themeColor }};">
                                    {{ $unit->code }}
                                </button>
                            </td>
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
                            <td class="px-4 py-2 text-center">
                                <button type="button"
                                        class="unit-details-btn inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold text-white font-tajawal"
                                        data-unit-id="{{ $unit->id }}"
                                        style="background: {{ $themeColor }};">
                                    التفاصيل
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

        <div id="unit-detail-modal" class="fixed inset-0 z-[60] hidden" aria-hidden="true" role="dialog" aria-labelledby="modal-unit-code">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" data-modal-close></div>
            <div class="absolute inset-x-3 sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 top-4 sm:top-10 bottom-4 sm:bottom-auto sm:max-h-[90vh] w-auto sm:w-full sm:max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl border border-gray-200 flex flex-col font-tajawal">
                <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between gap-3 shrink-0"
                     style="background: linear-gradient(135deg, {{ $themeColor }}10 0%, transparent 100%);">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">بيانات الوحدة</p>
                        <h3 id="modal-unit-code" class="text-2xl font-extrabold text-gray-900 font-mono" dir="ltr">—</h3>
                        <p id="modal-unit-floor" class="text-sm text-gray-500 mt-0.5">—</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="modal-unit-status" class="text-xs font-bold px-2.5 py-1 rounded-full"></span>
                        <button type="button" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500" data-modal-close aria-label="إغلاق">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div class="rounded-xl bg-gray-50 p-3">
                            <div class="text-[10px] text-gray-500">الاستخدام</div>
                            <div class="font-bold text-gray-900" id="modal-use">—</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3">
                            <div class="text-[10px] text-gray-500">المساحة</div>
                            <div class="font-bold text-gray-900" id="modal-area">—</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3">
                            <div class="text-[10px] text-gray-500">رمز الطابق</div>
                            <div class="font-bold font-mono text-gray-900" id="modal-floor-prefix" dir="ltr">—</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3">
                            <div class="text-[10px] text-gray-500">الحالة</div>
                            <div class="font-bold text-gray-900" id="modal-status-text">—</div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4">
                        <h4 class="text-sm font-bold text-gray-800 mb-3">الأسعار</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div>
                                <div class="text-xs text-gray-500">سعر الكاش الإجمالي</div>
                                <div class="text-lg font-extrabold" id="modal-cash" style="color: {{ $themeColor }}">—</div>
                                <div class="text-xs text-gray-400 mt-0.5" id="modal-cash-m2"></div>
                            </div>
                            <div id="modal-installment-block">
                                <div class="text-xs text-gray-500">سعر القسط الإجمالي</div>
                                <div class="text-lg font-extrabold text-gray-800" id="modal-installment">—</div>
                                <div class="text-xs text-gray-400 mt-0.5" id="modal-installment-m2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4" id="modal-plans-wrap">
                        <h4 class="text-sm font-bold text-gray-800 mb-3">خطط السداد</h4>
                        <div id="modal-plans-list" class="space-y-2 text-sm"></div>
                        <p id="modal-plans-empty" class="text-sm text-gray-400 hidden">لا توجد خطط سداد مسجّلة لهذه الوحدة.</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4 hidden" id="modal-meta-wrap">
                        <h4 class="text-sm font-bold text-gray-800 mb-3">بيانات إضافية</h4>
                        <div id="modal-meta-scalars" class="grid grid-cols-2 gap-3 text-sm mb-4"></div>
                        <div id="modal-interior-wrap" class="hidden">
                            <p class="text-xs font-bold text-gray-500 mb-2">التخطيط الداخلي</p>
                            <div id="modal-interior-plan" class="relative w-full aspect-[5/4] bg-slate-100 rounded-xl border border-slate-200 overflow-hidden"></div>
                            <div id="modal-interior-legend" class="flex flex-wrap gap-2 mt-3"></div>
                        </div>
                    </div>

                    @if($canEdit)
                    <div class="rounded-xl border border-gray-200 p-4">
                        <label class="block text-xs font-bold text-gray-500 mb-1">تغيير حالة الوحدة</label>
                        <select id="modal-status-select" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                            @foreach(config('project_units.statuses') as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p id="modal-save-msg" class="text-xs mt-1 hidden"></p>
                    </div>
                    @endif
                </div>
                <div class="px-5 py-4 border-t border-gray-100 flex flex-wrap gap-2 shrink-0 bg-gray-50">
                    @if($showDealButton)
                    <a href="#" id="modal-deal-link"
                       class="flex-1 min-w-[10rem] text-center py-2.5 rounded-xl text-sm font-bold text-white font-tajawal"
                       style="background: {{ $themeColor }};">
                        إنشاء صفقة على هذه الوحدة
                    </a>
                    @endif
                    <button type="button" class="px-4 py-2.5 rounded-xl text-sm font-bold border border-gray-200 bg-white text-gray-700" data-modal-close>
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
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
    const showUrlTemplate = root.dataset.showUrl || '';
    const dealBaseUrl = root.dataset.dealUrl || '';
    const csrf = root.dataset.csrf || '';
    const canEdit = root.dataset.canEdit === '1';
    const themeColor = root.dataset.theme || '#3b82f6';

    const statusColors = @json($statusColors);
    const useColors = @json($useColors);
    const floorLevelLabels = @json(collect(config('project_units.floor_levels', []))->mapWithKeys(fn ($v, $k) => [(string) $k => $v['label'] ?? (string) $k])->all());
    const metaFieldLabels = { floor_level: 'مستوى الطابق', slot_index: 'ترتيب الوحدة في الطابق' };
    const roomTypeLabels = {
        commercial: 'تجاري', storage: 'مخزن', bath: 'حمام', office: 'مكتب',
        living: 'صالة', kitchen: 'مطبخ', bedroom: 'غرفة نوم', balcony: 'بلكونة',
    };
    const moneyFmt = (n) => new Intl.NumberFormat('ar-EG', { maximumFractionDigits: 0 }).format(n) + ' ج.م';

    let selectedId = null;
    let activeFloor = 'all';
    let activeStatus = null;
    let activeUseType = document.getElementById('project-classification-panel')?.dataset.defaultClass || null;

    const panelEmpty = document.getElementById('unit-detail-empty');
    const panelContent = document.getElementById('unit-detail-content');
    const modal = document.getElementById('unit-detail-modal');

    window.__buildingSelectUnit = function (id) { openUnitModal(id); };
    window.__buildingOpenUnitModal = function (id) { openUnitModal(id); };

    function highlightSelected(id) {
        const sid = String(id);
        document.querySelectorAll('.unit-chip').forEach(el => {
            const on = el.dataset.unitId === sid;
            el.style.outline = on ? `3px solid ${themeColor}` : '';
            el.style.outlineOffset = on ? '2px' : '';
            el.style.transform = on ? 'scale(1.05)' : '';
        });
        document.querySelectorAll('.unit-card').forEach(el => {
            const on = el.dataset.unitId === sid;
            el.classList.toggle('ring-2', on);
            el.classList.toggle('shadow-lg', on);
            if (on) el.style.setProperty('--tw-ring-color', themeColor);
        });
        document.querySelectorAll('.unit-table-row').forEach(el => {
            el.classList.toggle('bg-blue-50', el.dataset.unitId === sid);
            el.classList.toggle('ring-2', el.dataset.unitId === sid);
            el.style.setProperty('--tw-ring-color', themeColor);
        });
    }

    function selectUnit(id) {
        const unit = unitMap[String(id)];
        if (!unit) return;
        selectedId = String(id);
        highlightSelected(selectedId);

        panelEmpty.classList.add('hidden');
        panelContent.classList.remove('hidden');

        document.getElementById('detail-code-btn').textContent = unit.code;
        document.getElementById('detail-code-btn').dataset.unitId = unit.id;
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
        const openModalBtn = document.getElementById('detail-open-modal-btn');
        if (openModalBtn) openModalBtn.dataset.unitId = unit.id;
        if (dealLink) {
            const dealUrl = new URL(dealBaseUrl, window.location.origin);
            dealUrl.searchParams.set('product_service', unit.code + ' — ' + unit.use_label + ' (' + unit.area_m2 + ' م²)');
            dealUrl.searchParams.set('estimated_value', String(unit.price_cash || 0));
            dealLink.href = dealUrl.toString();
        }
    }

    function renderPaymentPlans(unit) {
        const list = document.getElementById('modal-plans-list');
        const empty = document.getElementById('modal-plans-empty');
        const plans = unit.payment_plans || [];
        list.innerHTML = '';

        if (!plans.length) {
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');

        plans.forEach(plan => {
            const row = document.createElement('div');
            row.className = 'rounded-lg bg-gray-50 px-3 py-2 flex flex-wrap justify-between gap-2';
            const parts = [];
            if (plan.type_label) parts.push(plan.type_label);
            if (plan.down_percent != null) parts.push('مقدم ' + plan.down_percent + '%');
            if (plan.years) parts.push(plan.years + ' سنة');
            if (plan.installment_per_m2) parts.push(moneyFmt(plan.installment_per_m2) + '/م²');
            if (plan.down_payment_amount) parts.push('دفعة أولى ' + moneyFmt(plan.down_payment_amount));
            row.innerHTML = '<span class="font-semibold text-gray-800">' + (parts.join(' · ') || 'خطة سداد') + '</span>'
                + (plan.notes ? '<span class="text-xs text-gray-500 w-full">' + plan.notes + '</span>' : '');
            list.appendChild(row);
        });
    }

    function renderInterior(interior) {
        const wrap = document.getElementById('modal-interior-wrap');
        const plan = document.getElementById('modal-interior-plan');
        const legend = document.getElementById('modal-interior-legend');
        const rooms = interior?.rooms || [];

        plan.innerHTML = '';
        legend.innerHTML = '';

        if (!rooms.length) {
            wrap.classList.add('hidden');
            return;
        }
        wrap.classList.remove('hidden');

        rooms.forEach(room => {
            const block = document.createElement('div');
            block.className = 'absolute rounded-sm border border-white/60 shadow-sm flex items-center justify-center overflow-hidden';
            block.style.left = ((room.x ?? 0) * 100) + '%';
            block.style.top = ((room.z ?? 0) * 100) + '%';
            block.style.width = ((room.w ?? 0.1) * 100) + '%';
            block.style.height = ((room.d ?? 0.1) * 100) + '%';
            block.style.background = room.color || '#e2e8f0';
            block.title = room.label || room.id || '';

            const label = document.createElement('span');
            label.className = 'text-[9px] sm:text-[10px] font-bold text-gray-800 text-center leading-tight px-0.5 pointer-events-none';
            label.textContent = room.label || room.id || '';
            block.appendChild(label);
            plan.appendChild(block);

            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1.5 text-xs font-semibold px-2 py-1 rounded-lg border border-gray-200 bg-white';
            chip.innerHTML = '<span class="w-2.5 h-2.5 rounded-sm shrink-0" style="background:' + (room.color || '#e2e8f0') + '"></span>'
                + '<span class="text-gray-800">' + (room.label || room.id || 'غرفة') + '</span>'
                + (room.type ? '<span class="text-gray-400">(' + (roomTypeLabels[room.type] || room.type) + ')</span>' : '');
            legend.appendChild(chip);
        });
    }

    function formatMetaScalar(key, value) {
        if (key === 'floor_level') {
            const label = floorLevelLabels[String(value)];
            return label ? label + ' (' + value + ')' : String(value);
        }
        if (key === 'slot_index') {
            return String(Number(value) + 1);
        }
        return String(value);
    }

    function renderMeta(unit) {
        const wrap = document.getElementById('modal-meta-wrap');
        const scalars = document.getElementById('modal-meta-scalars');
        const meta = unit.meta || {};
        const scalarKeys = ['floor_level', 'slot_index'];
        const scalarEntries = scalarKeys
            .filter(k => meta[k] !== null && meta[k] !== undefined && meta[k] !== '')
            .map(k => [k, meta[k]]);

        scalars.innerHTML = '';
        renderInterior(meta.interior);

        const hasInterior = Array.isArray(meta.interior?.rooms) && meta.interior.rooms.length > 0;
        if (!scalarEntries.length && !hasInterior) {
            wrap.classList.add('hidden');
            return;
        }
        wrap.classList.remove('hidden');

        scalarEntries.forEach(([key, value]) => {
            const box = document.createElement('div');
            box.className = 'rounded-xl bg-gray-50 px-3 py-2';
            box.innerHTML = '<div class="text-[10px] text-gray-500">' + (metaFieldLabels[key] || key) + '</div>'
                + '<div class="font-bold text-gray-900 mt-0.5">' + formatMetaScalar(key, value) + '</div>';
            scalars.appendChild(box);
        });

        if (!scalarEntries.length) {
            scalars.classList.add('hidden');
        } else {
            scalars.classList.remove('hidden');
        }
    }

    function populateModal(unit) {
        const color = statusColors[unit.status] || unit.color || '#64748b';

        document.getElementById('modal-unit-code').textContent = unit.code;
        document.getElementById('modal-unit-floor').textContent = unit.floor_label || '—';
        document.getElementById('modal-use').textContent = unit.use_label;
        document.getElementById('modal-area').textContent = unit.area_m2.toLocaleString('ar-EG') + ' م²';
        document.getElementById('modal-floor-prefix').textContent = unit.floor_prefix || '—';
        document.getElementById('modal-status-text').textContent = unit.status_label;

        const statusBadge = document.getElementById('modal-unit-status');
        statusBadge.textContent = unit.status_label;
        statusBadge.style.color = color;
        statusBadge.style.background = color + '22';

        document.getElementById('modal-cash').textContent = moneyFmt(unit.price_cash);
        document.getElementById('modal-cash-m2').textContent = unit.price_per_m2_cash
            ? moneyFmt(unit.price_per_m2_cash) + ' / م²'
            : '';

        const instBlock = document.getElementById('modal-installment-block');
        if (unit.price_installment) {
            instBlock.classList.remove('hidden');
            document.getElementById('modal-installment').textContent = moneyFmt(unit.price_installment);
            document.getElementById('modal-installment-m2').textContent = unit.price_per_m2_installment
                ? moneyFmt(unit.price_per_m2_installment) + ' / م²'
                : '';
        } else {
            instBlock.classList.add('hidden');
        }

        renderPaymentPlans(unit);
        renderMeta(unit);

        const modalSel = document.getElementById('modal-status-select');
        if (modalSel) modalSel.value = unit.status;

        const modalDeal = document.getElementById('modal-deal-link');
        if (modalDeal) {
            const dealUrl = new URL(dealBaseUrl, window.location.origin);
            dealUrl.searchParams.set('product_service', unit.code + ' — ' + unit.use_label + ' (' + unit.area_m2 + ' م²)');
            dealUrl.searchParams.set('estimated_value', String(unit.price_cash || 0));
            modalDeal.href = dealUrl.toString();
        }
    }

    function setUnitDeepLink(id) {
        const url = new URL(window.location.href);
        url.searchParams.set('unit', String(id));
        history.replaceState({}, '', url);
    }

    function clearUnitDeepLink() {
        const url = new URL(window.location.href);
        url.searchParams.delete('unit');
        history.replaceState({}, '', url);
    }

    function openUnitModal(id) {
        const unit = unitMap[String(id)];
        if (!unit || !modal) return;

        selectUnit(id);
        populateModal(unit);
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        setUnitDeepLink(id);

        const card = document.querySelector(`.unit-card[data-unit-id="${id}"]`);
        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeUnitModal() {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        clearUnitDeepLink();
    }

    async function saveUnitStatus(status, msgEl, selectEl) {
        if (!selectedId) return;
        const url = updateUrlTemplate.replace('__ID__', selectedId);
        msgEl.classList.remove('hidden', 'text-green-600', 'text-red-600');
        msgEl.textContent = 'جاري الحفظ...';
        msgEl.classList.add('text-gray-500');

        try {
            const res = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ status }),
            });
            const data = await res.json();
            if (!res.ok || !data.ok) throw new Error('فشل التحديث');

            const u = data.unit;
            unitMap[selectedId].status = u.status;
            unitMap[selectedId].status_label = u.status_label;
            unitMap[selectedId].color = u.color;

            document.querySelectorAll(`[data-unit-id="${selectedId}"]`).forEach(el => {
                if (el.dataset.unitId !== selectedId) return;
                if (el.dataset.status !== undefined) el.dataset.status = u.status;
                if (el.classList.contains('unit-chip')) el.style.background = u.color;
                if (el.classList.contains('unit-card')) {
                    el.style.borderColor = u.color + '33';
                    const badge = el.querySelector('.unit-card-status');
                    if (badge) {
                        badge.textContent = u.status_label;
                        badge.style.color = u.color;
                        badge.style.background = u.color + '22';
                    }
                }
            });

            const row = document.querySelector(`.unit-table-row[data-unit-id="${selectedId}"]`);
            if (row) {
                const badge = row.querySelector('.unit-status-badge');
                if (badge) {
                    badge.textContent = u.status_label;
                    badge.style.color = u.color;
                    badge.style.background = u.color + '22';
                }
            }

            selectUnit(selectedId);
            populateModal(unitMap[selectedId]);
            if (selectEl) selectEl.value = u.status;

            const statAvail = document.getElementById('stat-available');
            if (statAvail && data.project) statAvail.textContent = data.project.available_units;

            msgEl.textContent = 'تم تحديث الحالة';
            msgEl.classList.remove('text-gray-500');
            msgEl.classList.add('text-green-600');
        } catch (e) {
            msgEl.textContent = 'تعذر الحفظ — حاول مرة أخرى';
            msgEl.classList.add('text-red-600');
        }
    }

    function applyFilters() {
        document.querySelectorAll('.building-floor-row').forEach(row => {
            const show = activeFloor === 'all' || row.dataset.floorId === String(activeFloor);
            row.style.opacity = show ? '1' : '0.25';
            row.style.pointerEvents = show ? '' : 'none';
        });

        const filterable = '.unit-chip, .unit-table-row, .unit-card';
        let visibleCards = 0;
        document.querySelectorAll(filterable).forEach(el => {
            const floorMatch = activeFloor === 'all' || el.dataset.floorId === String(activeFloor);
            const statusMatch = !activeStatus || el.dataset.status === activeStatus;
            const useMatch = !activeUseType || el.dataset.useType === activeUseType;
            const show = floorMatch && statusMatch && useMatch;
            el.style.display = show ? '' : 'none';
            if (show && el.classList.contains('unit-card')) visibleCards++;
        });
        document.querySelectorAll('.floor-table-block').forEach(block => {
            const show = activeFloor === 'all' || block.dataset.floorId === String(activeFloor);
            block.style.display = show ? '' : 'none';
        });

        const countEl = document.getElementById('unit-cards-count');
        if (countEl) countEl.textContent = visibleCards + ' وحدة';
    }

    root.addEventListener('click', (e) => {
        const trigger = e.target.closest('.unit-code-link, .unit-details-btn, .unit-chip');
        if (!trigger || !root.contains(trigger)) return;
        const id = trigger.dataset.unitId;
        if (!id) return;
        e.stopPropagation();
        openUnitModal(id);
    });

    root.querySelectorAll('.unit-table-row').forEach(row => {
        row.addEventListener('click', (e) => {
            if (e.target.closest('.unit-code-link, .unit-details-btn')) return;
            openUnitModal(row.dataset.unitId);
        });
    });

    modal?.querySelectorAll('[data-modal-close]').forEach(el => {
        el.addEventListener('click', closeUnitModal);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeUnitModal();
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

    document.addEventListener('classification-changed', (e) => {
        activeUseType = e.detail?.key || null;
        document.querySelectorAll('#unit-stats-strip > div[data-use-type]').forEach(box => {
            const show = !activeUseType || box.dataset.useType === activeUseType;
            box.classList.toggle('hidden', !show);
        });
        applyFilters();
    });

    const deepLinkUnit = new URLSearchParams(window.location.search).get('unit');
    if (statusSelect && canEdit) {
        statusSelect.addEventListener('change', () => {
            const msg = document.getElementById('detail-save-msg');
            saveUnitStatus(statusSelect.value, msg, statusSelect);
        });
    }

    const modalStatusSelect = document.getElementById('modal-status-select');
    if (modalStatusSelect && canEdit) {
        modalStatusSelect.addEventListener('change', () => {
            const msg = document.getElementById('modal-save-msg');
            saveUnitStatus(modalStatusSelect.value, msg, modalStatusSelect);
        });
    }

    function applyStatusFromUrl() {
        const statusParam = new URLSearchParams(window.location.search).get('status');
        if (!statusParam) return;
        activeStatus = statusParam;
        const btn = root.querySelector(`.status-filter[data-status="${statusParam}"]`);
        if (btn) {
            root.querySelectorAll('.status-filter').forEach(b => {
                b.style.background = '';
                b.classList.remove('text-white');
            });
            btn.style.background = statusColors[statusParam] || themeColor;
            btn.classList.add('text-white');
        }
        applyFilters();
    }

    applyStatusFromUrl();

    if (activeUseType) {
        document.querySelectorAll('#unit-stats-strip > div[data-use-type]').forEach(box => {
            box.classList.toggle('hidden', box.dataset.useType !== activeUseType);
        });
        applyFilters();
    }

    const deepLinkUnit = new URLSearchParams(window.location.search).get('unit');
    if (deepLinkUnit && unitMap[deepLinkUnit]) {
        openUnitModal(deepLinkUnit);
    } else if (units[0]) {
        selectUnit(units[0].id);
    }
})();
</script>
<style>
    .active-floor-filter { color: #fff !important; }
    #building-units-root .unit-chip,
    #building-units-root .unit-code-link,
    #building-units-root .unit-details-btn { cursor: pointer; }
    #building-units-root .unit-table-row { cursor: pointer; }
    #building-units-root .unit-table-row.ring-2,
    #building-units-root .unit-card.ring-2 { --tw-ring-color: {{ $themeColor }}; }
</style>
@endpush
@endif
