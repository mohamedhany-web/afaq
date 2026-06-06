@php $lostReasons = $lostReasons ?? config('crm_intelligence.lost_reasons'); @endphp
<div id="lost-reason-modal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4" style="background: rgba(0,0,0,.45);">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-md overflow-hidden font-tajawal" dir="rtl">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900 text-lg">سبب الخسارة</h3>
            <p class="text-xs text-gray-500 mt-1">مطلوب عند إغلاق العميل أو الصفقة كـ «خسارة»</p>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-2">السبب <span class="text-red-500">*</span></label>
                <select id="lost-reason-select" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm">
                    <option value="">— اختر السبب —</option>
                    @foreach($lostReasons as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-2">تفاصيل إضافية</label>
                <textarea id="lost-reason-notes" rows="3" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm" placeholder="اختياري — ما الذي حدث بالتحديد؟"></textarea>
            </div>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 flex gap-2 justify-end">
            <button type="button" id="lost-reason-cancel" class="px-4 py-2 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold">إلغاء</button>
            <button type="button" id="lost-reason-confirm" class="px-4 py-2 rounded-xl text-white text-sm font-semibold" style="background: #ef4444;">تأكيد الخسارة</button>
        </div>
    </div>
</div>
<script>
window.crmPromptLostReason = function () {
    return new Promise(function (resolve, reject) {
        const modal = document.getElementById('lost-reason-modal');
        const select = document.getElementById('lost-reason-select');
        const notes = document.getElementById('lost-reason-notes');
        const confirmBtn = document.getElementById('lost-reason-confirm');
        const cancelBtn = document.getElementById('lost-reason-cancel');
        if (!modal) { reject(new Error('modal missing')); return; }

        select.value = '';
        notes.value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        function cleanup() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            confirmBtn.removeEventListener('click', onConfirm);
            cancelBtn.removeEventListener('click', onCancel);
        }

        function onConfirm() {
            if (!select.value) {
                alert('يرجى اختيار سبب الخسارة');
                return;
            }
            cleanup();
            resolve({ lost_reason: select.value, lost_reason_notes: notes.value || null });
        }

        function onCancel() {
            cleanup();
            reject(new Error('cancelled'));
        }

        confirmBtn.addEventListener('click', onConfirm);
        cancelBtn.addEventListener('click', onCancel);
    });
};
</script>
