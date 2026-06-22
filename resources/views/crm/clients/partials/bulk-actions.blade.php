@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $clientsRoutePrefix = $clientsRoutePrefix ?? 'crm.clients';
    $canTransfer = ($assignableReps ?? collect())->isNotEmpty() && auth()->user()->can('bulkUpdate', \App\Models\Client::class);
    $canUpdateMeta = auth()->user()->can('bulkUpdate', \App\Models\Client::class);
    $canDelete = auth()->user()->can('bulkDelete', \App\Models\Client::class);
@endphp
@if($canTransfer || $canUpdateMeta || $canDelete)
<div id="client-bulk-bar" class="hidden mb-4 p-4 rounded-2xl border-2 bg-white shadow-sm font-tajawal" style="border-color: {{ $themeColor }}40;">
    <div class="flex flex-col gap-4">
        <div>
            <p class="text-sm font-bold text-gray-900 mb-1">إجراءات جماعية</p>
            <p class="text-xs text-gray-500"><span id="bulk-selected-count">0</span> عميل/رقم محدّد</p>
            <div id="bulk-selected-preview" class="mt-2 flex flex-wrap gap-1 max-h-16 overflow-y-auto text-[11px] text-gray-600"></div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-end gap-3 flex-wrap">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">اختيار إجراء</label>
                <select id="bulk-action-select" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm min-w-[200px]">
                    <option value="">— اختر إجراء —</option>
                    @if($canTransfer)<option value="transfer">تحويل إلى سيلز</option>@endif
                    @if($canUpdateMeta)<option value="meta">تعديل الحالة والمصدر</option>@endif
                    @if($canDelete)<option value="delete">حذف المحدد</option>@endif
                </select>
            </div>
        </div>

        <div id="bulk-panel-transfer" class="hidden">
            @if($canTransfer)
            <form method="POST" action="{{ route($clientsRoutePrefix . '.bulk-transfer') }}" class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-end flex-wrap" id="bulk-transfer-form">
                @csrf
                <div id="bulk-transfer-ids"></div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">تحويل / سحب إلى سيلز</label>
                    <select name="employee_id" required class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm min-w-[180px]">
                        @foreach($assignableReps as $rep)
                        <option value="{{ $rep->employee?->id }}">{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer sm:pb-2">
                    <input type="checkbox" name="transfer_tasks" value="1" checked class="rounded border-gray-300" style="accent-color: {{ $themeColor }};">
                    تحويل المهام المرتبطة
                </label>
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white" style="background: {{ $themeColor }};"
                        onclick="return confirm('تحويل العملاء المحددين إلى السيلز؟')">تنفيذ التحويل</button>
            </form>
            @endif
        </div>

        <div id="bulk-panel-meta" class="hidden">
            @if($canUpdateMeta)
            <form method="POST" action="{{ route($clientsRoutePrefix . '.bulk-update-meta') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end" id="bulk-meta-form">
                @csrf
                <div id="bulk-meta-ids"></div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
                    <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <option value="">— بدون تغيير —</option>
                        @foreach(['prospect' => 'محتمل', 'active' => 'نشط', 'inactive' => 'غير نشط', 'suspended' => 'موقوف'] as $val => $txt)
                        <option value="{{ $val }}">{{ $txt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">مصدر العميل</label>
                    <select name="lead_source" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <option value="">— بدون تغيير —</option>
                        @foreach(\App\Models\Client::leadSourceLabels() as $val => $txt)
                        <option value="{{ $val }}">{{ $txt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">مرحلة الرحلة</label>
                    <select name="lead_stage" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <option value="">— بدون تغيير —</option>
                        @foreach(\App\Services\CrmScopeService::leadStageLabels() as $val => $txt)
                        <option value="{{ $val }}">{{ $txt }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white h-[42px]" style="background: {{ $themeColor }};"
                        onclick="return confirm('تطبيق التعديلات على العملاء المحددين؟')">تطبيق التعديل</button>
            </form>
            @endif
        </div>

        <div id="bulk-panel-delete" class="hidden">
            @can('bulkDelete', \App\Models\Client::class)
            <form method="POST" action="{{ route('crm.clients.bulk-destroy') }}" class="flex flex-col gap-2 max-w-lg" id="bulk-delete-form"
                  onsubmit="return confirm('هل أنت متأكد من حذف العملاء المحددين؟ لا يمكن التراجع.')">
                @csrf
                <div id="bulk-delete-ids"></div>
                <label class="block text-xs font-bold text-gray-500">سبب الحذف (مطلوب)</label>
                <textarea name="delete_reason" rows="2" required minlength="10" maxlength="2000" placeholder="اشرح سبب الحذف الجماعي..."
                          class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm resize-none"></textarea>
                <button type="submit" class="self-start px-4 py-2 rounded-xl text-sm font-semibold bg-red-50 text-red-600 hover:bg-red-100">حذف المحدد</button>
            </form>
            @endcan
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checks = document.querySelectorAll('.client-bulk-check');
    const bar = document.getElementById('client-bulk-bar');
    const countEl = document.getElementById('bulk-selected-count');
    const preview = document.getElementById('bulk-selected-preview');
    const master = document.getElementById('client-bulk-check-all');
    const actionSelect = document.getElementById('bulk-action-select');
    const panels = {
        transfer: document.getElementById('bulk-panel-transfer'),
        meta: document.getElementById('bulk-panel-meta'),
        delete: document.getElementById('bulk-panel-delete'),
    };
    const idWraps = {
        transfer: document.getElementById('bulk-transfer-ids'),
        meta: document.getElementById('bulk-meta-ids'),
        delete: document.getElementById('bulk-delete-ids'),
    };

    function syncIds(wrap, selected) {
        if (!wrap) return;
        wrap.innerHTML = '';
        selected.forEach(function (c) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'client_ids[]';
            input.value = c.value;
            wrap.appendChild(input);
        });
    }

    function syncPanels() {
        const action = actionSelect?.value || '';
        Object.entries(panels).forEach(([key, el]) => {
            if (el) el.classList.toggle('hidden', action !== key);
        });
    }

    function sync() {
        const selected = Array.from(checks).filter(c => c.checked);
        const n = selected.length;
        if (bar) bar.classList.toggle('hidden', n === 0);
        if (countEl) countEl.textContent = String(n);
        if (preview) {
            preview.innerHTML = selected.slice(0, 12).map(c =>
                '<span class="px-2 py-0.5 rounded bg-gray-100">' + (c.dataset.name || '') + ' · ' + (c.dataset.phone || '') + '</span>'
            ).join('') + (n > 12 ? '<span class="text-gray-400"> +' + (n - 12) + '</span>' : '');
        }
        syncIds(idWraps.transfer, selected);
        syncIds(idWraps.meta, selected);
        syncIds(idWraps.delete, selected);
        if (master) master.checked = n > 0 && n === checks.length;
    }

    actionSelect?.addEventListener('change', syncPanels);
    checks.forEach(c => c.addEventListener('change', sync));
    master?.addEventListener('change', function () {
        checks.forEach(c => { c.checked = master.checked; });
        sync();
    });
    syncPanels();
});
</script>
@endif
