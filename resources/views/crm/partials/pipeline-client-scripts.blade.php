@props([
    'updateUrl' => '',
    'loadMoreUrl' => '',
])

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const updateUrlTemplate = @json($updateUrl);
    const loadMoreUrlTemplate = @json($loadMoreUrl);

    function adjustColumnCount(stage, delta) {
        const badge = document.querySelector('.kanban-count[data-stage="' + stage + '"]');
        const zone = document.querySelector('.kanban-drop-zone[data-stage="' + stage + '"]');
        if (!badge) return;
        const next = Math.max(0, parseInt(badge.textContent.replace(/,/g, ''), 10) + delta);
        badge.textContent = String(next);
        if (zone) zone.dataset.total = String(next);
    }

    function ensureEmptyPlaceholder(zone) {
        const cards = zone.querySelectorAll('.kanban-card').length;
        let empty = zone.querySelector('.kanban-empty');
        if (cards === 0 && !empty) {
            empty = document.createElement('div');
            empty.className = 'kanban-empty flex items-center justify-center py-6 px-2 text-center rounded-md border border-dashed border-gray-200 bg-white/80';
            empty.innerHTML = '<p class="text-[10px] text-gray-400 font-tajawal">اسحب عميلاً هنا</p>';
            zone.appendChild(empty);
        } else if (cards > 0 && empty) {
            empty.remove();
        }
    }

    if (typeof Sortable !== 'undefined') {
        document.querySelectorAll('.kanban-drop-zone').forEach(function (zone) {
            new Sortable(zone, {
                group: 'crm-client-pipeline',
                animation: 150,
                ghostClass: 'opacity-40',
                draggable: '.kanban-card',
                filter: 'a, button, input, select, textarea, summary, .client-interaction-form, .deal-stage-select',
                preventOnFilter: true,
                delay: 0,
                delayOnTouchOnly: true,
                touchStartThreshold: 4,
                scroll: true,
                bubbleScroll: true,
                scrollSensitivity: 60,
                scrollSpeed: 12,
                onEnd: async function (evt) {
                    const card = evt.item;
                    const id = card.dataset.clientId;
                    const newStage = evt.to.dataset.stage;
                    const oldStage = evt.from.dataset.stage;

                    document.querySelectorAll('.kanban-drop-zone').forEach(ensureEmptyPlaceholder);

                    if (!id || !newStage || newStage === oldStage) return;

                    ensureEmptyPlaceholder(evt.from);
                    ensureEmptyPlaceholder(evt.to);

                    try {
                        const res = await fetch(updateUrlTemplate.replace('__ID__', id), {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({ lead_stage: newStage }),
                        });
                        if (!res.ok) throw new Error('failed');
                        adjustColumnCount(oldStage, -1);
                        adjustColumnCount(newStage, 1);
                    } catch (e) {
                        evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                        ensureEmptyPlaceholder(evt.from);
                        ensureEmptyPlaceholder(evt.to);
                        alert('تعذر تحديث مرحلة العميل');
                    }
                },
            });
        });
    }

    document.querySelectorAll('.kanban-load-more').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const stage = btn.dataset.stage;
            const page = parseInt(btn.dataset.page, 10) || 2;
            btn.disabled = true;
            const url = loadMoreUrlTemplate.replace('__STAGE__', stage) + '?' + new URLSearchParams({
                page: page,
                ...Object.fromEntries(new URLSearchParams(window.location.search)),
            });
            try {
                const res = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await res.json();
                const zone = document.querySelector('.kanban-drop-zone[data-stage="' + stage + '"]');
                if (zone && data.html) {
                    zone.insertAdjacentHTML('beforeend', data.html);
                    zone.dataset.loaded = String(parseInt(zone.dataset.loaded, 10) + (data.html.match(/kanban-card/g) || []).length);
                    bindClientCardHandlers(zone);
                }
                if (data.has_more) {
                    btn.dataset.page = String(page + 1);
                    btn.textContent = 'المزيد (' + data.remaining.toLocaleString('ar-EG') + ')';
                    btn.disabled = false;
                } else {
                    btn.closest('[data-load-more-wrap]')?.remove();
                }
            } catch (e) {
                btn.disabled = false;
            }
        });
    });

    function bindClientCardHandlers(root) {
        root = root || document;

        root.querySelectorAll('.deal-stage-select').forEach(function (sel) {
            if (sel.dataset.bound) return;
            sel.dataset.bound = '1';
            sel.addEventListener('change', async function () {
                const url = sel.dataset.updateUrl;
                const prev = sel.dataset.prev || sel.value;
                sel.dataset.prev = sel.value;
                try {
                    const res = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ stage: sel.value }),
                    });
                    if (!res.ok) throw new Error();
                } catch (e) {
                    sel.value = prev;
                    alert('تعذر تحديث مرحلة الصفقة');
                }
            });
        });

        root.querySelectorAll('.client-interaction-form').forEach(function (form) {
            if (form.dataset.bound) return;
            form.dataset.bound = '1';
            const typeSel = form.querySelector('[name="interaction_type"]');
            const viewingDate = form.querySelector('.interaction-viewing-date');
            const saleSelect = form.querySelector('.interaction-sale-select');
            const msg = form.querySelector('.interaction-msg');

            function syncFields() {
                const t = typeSel?.value;
                viewingDate?.classList.toggle('hidden', t !== 'viewing');
                saleSelect?.classList.toggle('hidden', t !== 'viewing');
            }
            typeSel?.addEventListener('change', syncFields);
            syncFields();

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                e.stopPropagation();
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                msg.classList.add('hidden');
                try {
                    const res = await fetch(form.dataset.url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: new FormData(form),
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || 'failed');
                    form.querySelector('[name="notes"]').value = '';
                    msg.textContent = data.message || 'تم الحفظ';
                    msg.classList.remove('hidden');
                    msg.classList.add('text-green-600');
                } catch (err) {
                    msg.textContent = 'تعذر الحفظ';
                    msg.classList.remove('hidden');
                    msg.classList.add('text-red-600');
                } finally {
                    btn.disabled = false;
                }
            });
        });
    }

    bindClientCardHandlers(document);

    document.querySelectorAll('details.client-deals-panel, details.client-log-panel').forEach(function (el) {
        el.addEventListener('toggle', function (e) {
            e.stopPropagation();
        });
    });
});
</script>
