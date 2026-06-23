<script>
(function () {
    const STORAGE_KEY = 'afaq_ui_compact';
    const body = document.body;

    function isCompact() {
        return body.classList.contains('ui-compact-mode');
    }

    function syncToggleButton() {
        const btn = document.getElementById('ui-compact-toggle');
        if (!btn) return;
        const on = isCompact();
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
        const label = btn.querySelector('.ui-compact-label');
        if (label) {
            label.textContent = on ? (btn.dataset.labelOff || 'عرض التفاصيل') : (btn.dataset.labelOn || 'عرض مبسّط');
        }
        btn.querySelector('.ui-compact-icon-on')?.classList.toggle('hidden', !on);
        btn.querySelector('.ui-compact-icon-off')?.classList.toggle('hidden', on);
        btn.classList.toggle('ring-2', on);
        btn.style.borderColor = on ? '<?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>' : '';
    }

    function setCompact(enabled) {
        body.classList.toggle('ui-compact-mode', enabled);
        try {
            localStorage.setItem(STORAGE_KEY, enabled ? '1' : '0');
        } catch (e) {}
        syncToggleButton();
    }

    try {
        if (localStorage.getItem(STORAGE_KEY) === '1') {
            body.classList.add('ui-compact-mode');
        }
    } catch (e) {}

    document.addEventListener('DOMContentLoaded', function () {
        syncToggleButton();
        document.getElementById('ui-compact-toggle')?.addEventListener('click', function () {
            setCompact(!isCompact());
        });
    });
})();
</script>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/partials/ui-compact-scripts.blade.php ENDPATH**/ ?>