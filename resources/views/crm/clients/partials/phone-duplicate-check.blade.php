@php
    $ignoreId = $ignoreId ?? ($client->id ?? null);
    $checkUrl = $checkPhoneRoute ?? route(($clientsRoutePrefix ?? 'crm.clients') . '.check-phone');
@endphp
<div id="phone-duplicate-alert" class="hidden mt-2 p-3 rounded-xl border-2 border-amber-300 bg-amber-50 text-sm font-tajawal" role="alert"></div>

@push('scripts')
<script>
(function () {
    const phoneInput = document.querySelector('input[name="phone"]');
    const alertBox = document.getElementById('phone-duplicate-alert');
    if (!phoneInput || !alertBox) return;

    const checkUrl = @json($checkUrl);
    const ignoreId = @json($ignoreId);
    let timer = null;
    let lastChecked = '';

    function hideAlert() {
        alertBox.classList.add('hidden');
        alertBox.innerHTML = '';
        phoneInput.classList.remove('border-amber-400', 'ring-2', 'ring-amber-200');
    }

    function showDuplicate(data) {
        const c = data.client;
        const parts = [
            '<strong class="text-amber-900">تنبيه: رقم مكرر</strong>',
            '<p class="mt-1 text-amber-900">هذا الرقم مسجّل مسبقاً في الـ CRM:</p>',
            '<ul class="mt-2 space-y-1 text-amber-800 text-xs">',
            '<li><span class="font-bold">العميل:</span> <a href="' + c.url + '" class="underline font-bold" target="_blank" rel="noopener">' + c.name + '</a></li>',
            '<li><span class="font-bold">الحالة:</span> ' + c.status_label + '</li>',
            '<li><span class="font-bold">المرحلة:</span> ' + c.lead_stage_label + '</li>',
        ];
        if (c.lead_source_label) {
            parts.push('<li><span class="font-bold">المصدر:</span> ' + c.lead_source_label + '</li>');
        }
        if (c.sales_rep) {
            parts.push('<li><span class="font-bold">السيلز:</span> ' + c.sales_rep + '</li>');
        }
        parts.push('</ul>');
        parts.push('<p class="mt-2 text-xs text-amber-700">لا يمكن حفظ عميل بنفس الرقم. راجع الملف الحالي أو صحّح الرقم.</p>');
        alertBox.innerHTML = parts.join('');
        alertBox.classList.remove('hidden');
        phoneInput.classList.add('border-amber-400', 'ring-2', 'ring-amber-200');
    }

    async function checkPhone() {
        const phone = phoneInput.value.trim();
        if (!phone || phone.length < 8) {
            hideAlert();
            return;
        }
        if (phone === lastChecked) return;
        lastChecked = phone;

        const params = new URLSearchParams({ phone });
        if (ignoreId) params.set('ignore_id', ignoreId);

        try {
            const res = await fetch(checkUrl + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;
            const data = await res.json();
            if (data.duplicate) {
                showDuplicate(data);
            } else {
                hideAlert();
            }
        } catch (e) {
            /* ignore network errors */
        }
    }

    function scheduleCheck() {
        clearTimeout(timer);
        timer = setTimeout(checkPhone, 450);
    }

    phoneInput.addEventListener('input', function () {
        lastChecked = '';
        scheduleCheck();
    });
    phoneInput.addEventListener('blur', checkPhone);
})();
</script>
@endpush
