<?php if (! $__env->hasRenderedOnce('b839761e-bc54-4ddd-8738-de8cef963a31')): $__env->markAsRenderedOnce('b839761e-bc54-4ddd-8738-de8cef963a31'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('clientSearchSelect', (config) => ({
        name: config.name || 'client_id',
        selectedId: config.selectedId ? String(config.selectedId) : '',
        selectedLabel: config.selectedLabel || '',
        query: '',
        results: [],
        open: false,
        loading: false,
        searched: false,
        searchUrl: config.searchUrl,
        crmScope: !!config.crmScope,
        required: !!config.required,
        dropdownStyle: 'display: none;',

        init() {
            this._reposition = () => {
                if (this.open && this.results.length) {
                    this.positionDropdown();
                }
            };
            this._outsideClick = (e) => {
                if (!this.open) {
                    return;
                }
                const panel = this.$refs.dropdownPanel;
                if (this.$el.contains(e.target) || (panel && panel.contains(e.target))) {
                    return;
                }
                this.open = false;
            };
            window.addEventListener('scroll', this._reposition, true);
            window.addEventListener('resize', this._reposition);
            document.addEventListener('click', this._outsideClick, true);
        },

        destroy() {
            window.removeEventListener('scroll', this._reposition, true);
            window.removeEventListener('resize', this._reposition);
            document.removeEventListener('click', this._outsideClick, true);
        },

        positionDropdown() {
            const input = this.$refs.queryInput;
            if (!input) {
                return;
            }
            const rect = input.getBoundingClientRect();
            this.dropdownStyle = [
                'position: fixed',
                'z-index: 9999',
                `top: ${rect.bottom + 4}px`,
                `left: ${rect.left}px`,
                `width: ${Math.max(rect.width, 192)}px`,
            ].join('; ');
        },

        async search() {
            const term = this.query.trim();
            if (term.length < 2) {
                this.results = [];
                this.open = false;
                this.searched = false;
                return;
            }

            this.loading = true;
            this.searched = true;

            try {
                const params = new URLSearchParams({ q: term });
                if (this.crmScope) {
                    params.set('crm_scope', '1');
                }
                const { data } = await window.axios.get(this.searchUrl + '?' + params.toString());
                this.results = data.clients || [];
                this.open = this.results.length > 0;
                if (this.open) {
                    this.$nextTick(() => this.positionDropdown());
                }
            } catch (e) {
                console.error(e);
                this.results = [];
                this.open = false;
            } finally {
                this.loading = false;
            }
        },

        select(client) {
            this.selectedId = String(client.id);
            this.selectedLabel = client.label || client.name;
            this.query = '';
            this.results = [];
            this.open = false;
            this.searched = false;
        },

        clear() {
            this.selectedId = '';
            this.selectedLabel = '';
            this.query = '';
            this.results = [];
            this.open = false;
            this.searched = false;
        },
    }));
});

window.clearClientSearchSelect = function (container) {
    const root = typeof container === 'string' ? document.getElementById(container) : container;
    if (!root) return;
    const el = root.querySelector('[x-data]');
    if (el && typeof Alpine !== 'undefined' && Alpine.$data) {
        const data = Alpine.$data(el);
        if (data && typeof data.clear === 'function') {
            data.clear();
            return;
        }
    }
    const hidden = root.querySelector('input[type="hidden"][name="client_id"]');
    if (hidden) hidden.value = '';
};
</script>
<style>
    [x-cloak] { display: none !important; }
</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/partials/client-search-select-scripts.blade.php ENDPATH**/ ?>