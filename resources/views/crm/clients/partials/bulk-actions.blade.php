@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
@if(($assignableReps ?? collect())->isNotEmpty() || auth()->user()->can('bulkDelete', \App\Models\Client::class))
<div id="client-bulk-bar" class="hidden mb-4 p-4 rounded-2xl border-2 bg-white shadow-sm font-tajawal" style="border-color: {{ $themeColor }}40;">
    <div class="flex flex-col lg:flex-row lg:items-end gap-4">
        <div class="flex-1">
            <p class="text-sm font-bold text-gray-900 mb-1">إجراءات جماعية</p>
            <p class="text-xs text-gray-500"><span id="bulk-selected-count">0</span> عميل/رقم محدّد</p>
            <div id="bulk-selected-preview" class="mt-2 flex flex-wrap gap-1 max-h-16 overflow-y-auto text-[11px] text-gray-600"></div>
        </div>
        @if(($assignableReps ?? collect())->isNotEmpty() && auth()->user()->can('bulkUpdate', \App\Models\Client::class))
        <form method="POST" action="{{ route(($clientsRoutePrefix ?? 'crm.clients') . '.bulk-transfer') }}" class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-end flex-wrap" id="bulk-transfer-form">
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
                    onclick="return confirm('تحويل العملاء والأرقام المحددة إلى السيلز؟ سيتم تسجيل العملية في سجل الحركات.')">تحويل المحدد</button>
        </form>
        @endif
        @can('bulkDelete', \App\Models\Client::class)
        <form method="POST" action="{{ route('crm.clients.bulk-destroy') }}" class="flex flex-col gap-2 min-w-[240px]" id="bulk-delete-form"
              onsubmit="return confirm('هل أنت متأكد من حذف العملاء المحددين؟ لا يمكن التراجع.')">
            @csrf
            <div id="bulk-delete-ids"></div>
            <label class="block text-xs font-bold text-gray-500">سبب الحذف (مطلوب)</label>
            <textarea name="delete_reason" rows="2" required minlength="10" maxlength="2000" placeholder="اشرح سبب الحذف الجماعي..."
                      class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm resize-none"></textarea>
            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-red-50 text-red-600 hover:bg-red-100">حذف المحدد</button>
        </form>
        @endcan
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checks = document.querySelectorAll('.client-bulk-check');
    const bar = document.getElementById('client-bulk-bar');
    const countEl = document.getElementById('bulk-selected-count');
    const preview = document.getElementById('bulk-selected-preview');
    const master = document.getElementById('client-bulk-check-all');
    const transferIds = document.getElementById('bulk-transfer-ids');
    const deleteIds = document.getElementById('bulk-delete-ids');

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
        [transferIds, deleteIds].forEach(function (wrap) {
            if (!wrap) return;
            wrap.innerHTML = '';
            selected.forEach(function (c) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'client_ids[]';
                input.value = c.value;
                wrap.appendChild(input);
            });
        });
        if (master) master.checked = n > 0 && n === checks.length;
    }

    checks.forEach(c => c.addEventListener('change', sync));
    master?.addEventListener('change', function () {
        checks.forEach(c => { c.checked = master.checked; });
        sync();
    });
});
</script>
@endif
