<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'updateUrl' => '',
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
    const payloadKey = <?php echo json_encode($payloadKey, 15, 512) ?>;
    const itemKey = <?php echo json_encode($itemKey, 15, 512) ?>;

    document.querySelectorAll('.journey-kanban-zone').forEach(function (zone) {
        new Sortable(zone, {
            group: 'crm-client-journey',
            animation: 180,
            ghostClass: 'opacity-40',
            draggable: '.kanban-card',
            onEnd: async function (evt) {
                const card = evt.item;
                const id = card.dataset[itemKey];
                const newStage = evt.to.dataset.journeyStage;
                const oldStage = evt.from.dataset.journeyStage;

                if (!id || !newStage || newStage === oldStage) return;

                let payload = { [payloadKey]: newStage };

                if (newStage === 'closed_lost') {
                    try {
                        const lost = await window.crmPromptLostReason();
                        payload.lost_reason = lost.lost_reason;
                        payload.lost_reason_notes = lost.lost_reason_notes;
                    } catch (e) {
                        evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
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
                        },
                        body: JSON.stringify(payload),
                    });
                    if (!res.ok) {
                        const err = await res.json().catch(() => ({}));
                        throw new Error(err.message || 'failed');
                    }
                    window.location.reload();
                } catch (e) {
                    evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                    alert('تعذر تحديث مرحلة العميل. حاول مرة أخرى.');
                }
            },
        });
    });
});
</script>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\partials\kanban-scripts.blade.php ENDPATH**/ ?>