<?php if (! $__env->hasRenderedOnce('2b815480-6d86-4408-b135-573202e72008')): $__env->markAsRenderedOnce('2b815480-6d86-4408-b135-573202e72008'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('developerSearchSelect', (config) => ({
        selectedId: config.selectedId ? String(config.selectedId) : '',
        selectedName: config.selectedName || '',
        selectedLabel: config.selectedLabel || '',
        query: '',
        results: [],
        open: false,
        loading: false,
        searchUrl: config.searchUrl,
        required: !!config.required,
        allowCreate: config.allowCreate !== false,
        dropdownStyle: 'display: none;',

        get canUseNew() {
            if (!this.allowCreate) return false;
            const term = this.query.trim();
            if (term.length < 2) return false;
            return !this.results.some(r => r.name.toLowerCase() === term.toLowerCase());
        },

        init() {
            this._reposition = () => { if (this.open) this.positionDropdown(); };
            this._outsideClick = (e) => {
                if (!this.open) return;
                const panel = this.$refs.dropdownPanel;
                if (this.$el.contains(e.target) || (panel && panel.contains(e.target))) return;
                this.open = false;
            };
            window.addEventListener('scroll', this._reposition, true);
            window.addEventListener('resize', this._reposition);
            document.addEventListener('click', this._outsideClick, true);
        },

        positionDropdown() {
            const input = this.$refs.queryInput;
            if (!input) return;
            const rect = input.getBoundingClientRect();
            this.dropdownStyle = `position:fixed;z-index:9999;top:${rect.bottom + 4}px;left:${rect.left}px;width:${Math.max(rect.width, 220)}px;`;
        },

        async search() {
            const term = this.query.trim();
            if (term.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            this.loading = true;
            try {
                const { data } = await window.axios.get(this.searchUrl + '?q=' + encodeURIComponent(term));
                this.results = data.developers || [];
                this.open = this.results.length > 0 || this.canUseNew;
                if (this.open) this.$nextTick(() => this.positionDropdown());
            } catch (e) {
                this.results = [];
                this.open = this.canUseNew;
            } finally {
                this.loading = false;
            }
        },

        select(dev) {
            this.selectedId = String(dev.id);
            this.selectedName = dev.name;
            this.selectedLabel = dev.label || dev.name;
            this.query = '';
            this.results = [];
            this.open = false;
        },

        useNewName() {
            const name = this.query.trim();
            if (!name) return;
            this.selectedId = '';
            this.selectedName = name;
            this.selectedLabel = name;
            this.query = '';
            this.results = [];
            this.open = false;
        },

        clear() {
            this.selectedId = '';
            this.selectedName = '';
            this.selectedLabel = '';
            this.query = '';
            this.results = [];
            this.open = false;
        },
    }));
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\partials\developer-search-select-scripts.blade.php ENDPATH**/ ?>