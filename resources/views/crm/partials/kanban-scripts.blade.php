@props([
    'updateUrl' => '',
    'payloadKey' => 'stage',
    'itemKey' => 'dealId',
])

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const updateUrlTemplate = @json($updateUrl);
    const payloadKey = @json($payloadKey);
    const itemKey = @json($itemKey);

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
