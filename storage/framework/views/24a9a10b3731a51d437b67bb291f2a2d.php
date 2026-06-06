<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'updateUrl' => '',
    'loadMoreUrl' => '',
    'payloadKey' => 'stage',
    'itemKey' => 'dealId',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'updateUrl' => '',
    'loadMoreUrl' => '',
    'payloadKey' => 'stage',
    'itemKey' => 'dealId',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const updateUrlTemplate = <?php echo json_encode($updateUrl, 15, 512) ?>;
    const loadMoreUrlTemplate = <?php echo json_encode($loadMoreUrl, 15, 512) ?>;
    const payloadKey = <?php echo json_encode($payloadKey, 15, 512) ?>;
    const itemKey = <?php echo json_encode($itemKey, 15, 512) ?>;

    const kanbanRoot = document.getElementById('client-deals-kanban') || document;

    function adjustColumnCount(stage, delta) {
        const badge = kanbanRoot.querySelector('.deals-kanban-count[data-deal-stage="' + stage + '"]')
            || kanbanRoot.querySelector('.kanban-count[data-deal-stage="' + stage + '"]');
        if (!badge) return;
        const next = Math.max(0, parseInt(badge.textContent.replace(/,/g, ''), 10) + delta);
        badge.textContent = String(next);
    }

    function ensureEmptyPlaceholder(zone) {
        const cards = zone.querySelectorAll('.kanban-card').length;
        let empty = zone.querySelector('.kanban-empty');
        if (cards === 0 && !empty) {
            empty = document.createElement('div');
            empty.className = 'kanban-empty flex items-center justify-center py-4 px-2 text-center rounded-md border border-dashed border-gray-200 bg-white/80';
            empty.innerHTML = '<p class="text-[10px] text-gray-400 font-tajawal">اسحب صفقة هنا</p>';
            zone.appendChild(empty);
        } else if (cards > 0 && empty) {
            empty.remove();
        }
    }

    if (typeof Sortable === 'undefined') {
        console.error('Sortable.js failed to load');
        return;
    }

    const zoneSelector = kanbanRoot.querySelector('.deals-kanban-zone')
        ? '.deals-kanban-zone'
        : '.kanban-drop-zone[data-deal-stage]';

    kanbanRoot.querySelectorAll(zoneSelector).forEach(function (zone) {
        const clientPageEl = document.querySelector('[data-client-page]');
        const groupName = clientPageEl
            ? 'client-deals-' + clientPageEl.dataset.clientPage
            : 'crm-pipeline';
        new Sortable(zone, {
            group: groupName,
            animation: 150,
            ghostClass: 'opacity-40',
            dragClass: 'shadow-md',
            draggable: '.kanban-card',
            filter: 'a, .kanban-load-more',
            preventOnFilter: true,
            delay: 0,
            delayOnTouchOnly: true,
            touchStartThreshold: 4,
            scroll: true,
            bubbleScroll: true,
            scrollSensitivity: 60,
            scrollSpeed: 12,
            forceAutoScrollFallback: true,
            emptyInsertThreshold: 8,
            onStart: function () {
                document.querySelectorAll('.kanban-empty').forEach(function (el) {
                    el.style.display = 'none';
                });
            },
            onEnd: async function (evt) {
                const card = evt.item;
                const id = card.dataset[itemKey];
                const newStage = evt.to.dataset.dealStage;
                const oldStage = evt.from.dataset.dealStage;

                kanbanRoot.querySelectorAll(zoneSelector).forEach(ensureEmptyPlaceholder);

                if (!id || !newStage || newStage === oldStage) return;

                ensureEmptyPlaceholder(evt.from);
                ensureEmptyPlaceholder(evt.to);

                let payload = { [payloadKey]: newStage };

                if (newStage === 'closed_lost') {
                    try {
                        const lost = await window.crmPromptLostReason();
                        payload.lost_reason = lost.lost_reason;
                        payload.lost_reason_notes = lost.lost_reason_notes;
                    } catch (e) {
                        evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                        ensureEmptyPlaceholder(evt.from);
                        ensureEmptyPlaceholder(evt.to);
                        return;
                    }
                }

                try {
                    const res = await fetch(updateUrlTemplate.replace('__ID__', id), {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!res.ok) throw new Error('failed');

                    adjustColumnCount(oldStage, -1);
                    adjustColumnCount(newStage, 1);
                } catch (e) {
                    evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                    ensureEmptyPlaceholder(evt.from);
                    ensureEmptyPlaceholder(evt.to);
                    alert('تعذر تحديث المرحلة. حاول مرة أخرى.');
                }
            },
        });
    });

    document.querySelectorAll('.kanban-load-more').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const stage = btn.dataset.stage;
            const page = btn.dataset.page;
            const zone = kanbanRoot.querySelector(zoneSelector + '[data-deal-stage="' + stage + '"]');
            if (!zone || btn.disabled) return;

            btn.disabled = true;
            const prevLabel = btn.textContent;
            btn.textContent = 'جاري التحميل...';

            const params = new URLSearchParams(window.location.search);
            params.set('page', page);

            try {
                const res = await fetch(loadMoreUrlTemplate.replace('__STAGE__', stage) + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (!res.ok) throw new Error('failed');

                const wrap = document.createElement('div');
                wrap.innerHTML = data.html;
                wrap.querySelectorAll('.kanban-card').forEach(function (card) {
                    zone.appendChild(card);
                });

                zone.dataset.loaded = String(zone.querySelectorAll('.kanban-card').length);
                ensureEmptyPlaceholder(zone);

                if (data.has_more) {
                    btn.dataset.page = String(parseInt(page, 10) + 1);
                    btn.textContent = 'عرض المزيد (' + data.remaining + '+)';
                    btn.disabled = false;
                } else {
                    btn.closest('[data-load-more-wrap]')?.remove();
                }
            } catch (e) {
                btn.textContent = 'إعادة المحاولة';
                btn.disabled = false;
            }
        });
    });
});
</script>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/partials/pipeline-kanban-scripts.blade.php ENDPATH**/ ?>