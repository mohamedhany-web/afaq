<?php
    use App\Helpers\GoogleMapsHelper;
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $allPins = collect();
    if ($project->hasMapLocation()) {
        $allPins->push((object)[
            'title' => $project->name,
            'pin_type' => 'project',
            'latitude' => $project->latitude,
            'longitude' => $project->longitude,
            'notes' => null,
        ]);
    }
    foreach ($project->mapPins ?? [] as $pin) {
        if ($pin->pin_type === 'project' && $project->hasMapLocation()) continue;
        $allPins->push($pin);
    }
    $mapId = 'project-map-view-' . $project->id;
    $satMapId = 'project-sat-view-' . $project->id;
    $hasGoogle = GoogleMapsHelper::hasEmbedSupport();
    $viewerBase = route('public.project.locate.viewer', $project);
    $pinJson = $allPins->map(fn ($p) => [
        'title' => $p->title,
        'pin_type' => $p->pin_type ?? 'unit',
        'lat' => (float) $p->latitude,
        'lng' => (float) $p->longitude,
    ])->values();
?>

<?php if($allPins->isNotEmpty()): ?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 w-full" id="crm-map-block-<?php echo e($project->id); ?>">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900 flex flex-wrap items-center justify-between gap-2"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <span>موقع المشروع <span class="text-xs font-normal text-gray-500">(<?php echo e($allPins->count()); ?> علامة)</span></span>
        <div class="flex gap-2">
            <button type="button" class="crm-open-viewer px-3 py-1.5 rounded-lg text-xs font-semibold text-white font-tajawal" style="background:<?php echo e($themeColor); ?>;"
                    data-mode="satellite">نافذة عرض جوي</button>
            <?php if($hasGoogle): ?>
            <button type="button" class="crm-open-viewer px-3 py-1.5 rounded-lg text-xs font-semibold border-2 font-tajawal" style="border-color:<?php echo e($themeColor); ?>40;color:<?php echo e($themeColor); ?>;"
                    data-mode="streetview">نافذة Street View</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="p-4 sm:p-5">
        <div class="flex flex-wrap gap-2 mb-4 border-b border-gray-100 pb-4">
            <button type="button" class="crm-map-tab px-4 py-2 rounded-xl text-xs font-bold font-tajawal border-2 border-gray-200 bg-white text-gray-800" data-tab="map">خريطة</button>
            <button type="button" class="crm-map-tab px-4 py-2 rounded-xl text-xs font-bold font-tajawal border-2 border-transparent text-gray-500" data-tab="satellite">عرض جوي</button>
            <?php if($hasGoogle): ?>
            <button type="button" class="crm-map-tab px-4 py-2 rounded-xl text-xs font-bold font-tajawal border-2 border-transparent text-gray-500" data-tab="streetview">Street View</button>
            <?php endif; ?>
        </div>

        <div class="crm-map-pane" data-pane="map">
            <div class="crm-map-wrap">
                <button type="button" class="crm-map-fullscreen-btn" data-fullscreen="map">⛶ ملء الشاشة</button>
                <div id="<?php echo e($mapId); ?>" class="w-full rounded-xl border border-gray-200" style="height:380px;"></div>
            </div>
        </div>

        <div class="crm-map-pane hidden" data-pane="satellite">
            <div class="crm-map-wrap">
                <button type="button" class="crm-map-fullscreen-btn" data-fullscreen="satellite">⛶ ملء الشاشة</button>
                <div id="<?php echo e($satMapId); ?>" class="w-full rounded-xl border border-gray-200" style="height:380px;"></div>
            </div>
            <p class="text-xs text-gray-400 mt-2 font-tajawal">عرض جوي مدمج داخل النظام — صور أقمار صناعية عالية الدقة</p>
        </div>

        <?php if($hasGoogle): ?>
        <?php $primary = $allPins->first(); ?>
        <div class="crm-map-pane hidden" data-pane="streetview">
            <div class="crm-map-wrap">
                <button type="button" class="crm-map-fullscreen-btn" data-fullscreen="streetview">⛶ ملء الشاشة</button>
                <iframe id="crm-street-iframe-<?php echo e($project->id); ?>" class="w-full rounded-xl border border-gray-200" style="height:380px;border:0;"
                        loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                        src="<?php echo e(GoogleMapsHelper::streetViewEmbedUrl($primary->latitude, $primary->longitude)); ?>"></iframe>
            </div>
            <p class="text-xs text-gray-400 mt-2 font-tajawal">جولة حيوية 360° — مدمجة داخل صفحتنا</p>
        </div>
        <?php endif; ?>

        <div class="mt-4 flex flex-wrap gap-2">
            <?php $__currentLoopData = $allPins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="text-xs px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 font-tajawal">
                    <?php echo e($pin->title); ?>

                    <span class="text-gray-400">· <?php echo e($pin instanceof \App\Models\ProjectMapPin ? $pin->typeLabel() : 'موقع المشروع'); ?></span>
                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>


<div class="crm-map-modal" id="crm-map-modal-<?php echo e($project->id); ?>" aria-hidden="true">
    <div class="crm-map-modal-box">
        <div class="crm-map-modal-head">
            <strong id="crm-map-modal-title-<?php echo e($project->id); ?>">عرض الخريطة</strong>
            <button type="button" class="crm-map-modal-close px-3 py-1 rounded-lg border text-sm font-tajawal">إغلاق</button>
        </div>
        <div class="crm-map-modal-body" id="crm-map-modal-body-<?php echo e($project->id); ?>"></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<?php echo $__env->make('projects.partials.map-leaflet-styles', ['themeColor' => $themeColor], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pins = <?php echo json_encode($pinJson, 15, 512) ?>;
    if (!pins.length) return;

    const themeColor = <?php echo json_encode($themeColor, 15, 512) ?>;
    const zoom = <?php echo e($project->map_zoom ?? 16); ?>;
    const viewerBase = <?php echo json_encode($viewerBase, 15, 512) ?>;
    const hasGoogle = <?php echo json_encode($hasGoogle, 15, 512) ?>;
    const streetSrc = <?php echo json_encode($hasGoogle && $allPins->first() ? GoogleMapsHelper::streetViewEmbedUrl($allPins->first()->latitude, $allPins->first()->longitude) : null, 512) ?>;
    const projectId = <?php echo e($project->id); ?>;
    const mapId = <?php echo json_encode($mapId, 15, 512) ?>;
    const satMapId = <?php echo json_encode($satMapId, 15, 512) ?>;

    function pinIcon(type) {
        return L.divIcon({
            className: '',
            html: `<div class="crm-map-pin ${type}"><div class="crm-map-pin-body"></div></div>`,
            iconSize: [28, 36], iconAnchor: [14, 36],
        });
    }

    function buildLeafletMap(elId, tileUrl, attribution, extraZoom) {
        const el = document.getElementById(elId);
        if (!el || el.dataset.inited) return null;
        const m = L.map(elId, { scrollWheelZoom: true }).setView([pins[0].lat, pins[0].lng], extraZoom ? zoom + 1 : zoom);
        L.tileLayer(tileUrl, { maxZoom: 20, attribution }).addTo(m);
        pins.forEach(p => L.marker([p.lat, p.lng], { icon: pinIcon(p.pin_type) }).addTo(m).bindPopup(`<strong>${p.title}</strong>`));
        if (pins.length > 1) m.fitBounds(L.latLngBounds(pins.map(p => [p.lat, p.lng])).pad(0.12));
        el.dataset.inited = '1';
        setTimeout(() => m.invalidateSize(), 200);
        return m;
    }

    const streetMap = buildLeafletMap(mapId, 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', '&copy; OSM');
    let satMap = null;

    const modal = document.getElementById('crm-map-modal-' + projectId);
    const modalBody = document.getElementById('crm-map-modal-body-' + projectId);
    const modalTitle = document.getElementById('crm-map-modal-title-' + projectId);

    function openModal(mode) {
        modalBody.innerHTML = '';
        const wrap = document.createElement('div');
        wrap.style.height = '70vh';
        wrap.style.minHeight = '400px';
        modalBody.appendChild(wrap);

        if (mode === 'streetview' && streetSrc) {
            modalTitle.textContent = 'Street View — ' + <?php echo json_encode($project->name, 15, 512) ?>;
            const iframe = document.createElement('iframe');
            iframe.src = streetSrc;
            iframe.style.cssText = 'width:100%;height:70vh;min-height:400px;border:0;border-radius:0';
            iframe.allowFullscreen = true;
            modalBody.innerHTML = '';
            modalBody.appendChild(iframe);
        } else {
            modalTitle.textContent = mode === 'satellite' ? 'عرض جوي — ' + <?php echo json_encode($project->name, 15, 512) ?> : 'خريطة — ' + <?php echo json_encode($project->name, 15, 512) ?>;
            const id = 'modal-map-' + mode + '-' + projectId;
            wrap.id = id;
            const tile = mode === 'satellite'
                ? 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const attr = mode === 'satellite' ? '&copy; Esri' : '&copy; OSM';
            setTimeout(() => {
                const m = L.map(id).setView([pins[0].lat, pins[0].lng], mode === 'satellite' ? zoom + 1 : zoom);
                L.tileLayer(tile, { maxZoom: 20, attribution: attr }).addTo(m);
                pins.forEach(p => L.marker([p.lat, p.lng], { icon: pinIcon(p.pin_type) }).addTo(m).bindPopup(`<strong>${p.title}</strong>`));
                if (pins.length > 1) m.fitBounds(L.latLngBounds(pins.map(p => [p.lat, p.lng])).pad(0.1));
                setTimeout(() => m.invalidateSize(), 150);
            }, 50);
        }
        modal.classList.add('open');
    }

    function openViewerWindow(mode) {
        const w = Math.min(screen.width * 0.92, 1280);
        const h = Math.min(screen.height * 0.88, 860);
        const left = (screen.width - w) / 2;
        const top = (screen.height - h) / 2;
        window.open(viewerBase + '?mode=' + mode, 'crm_map_viewer_' + projectId,
            `width=${w},height=${h},left=${left},top=${top},scrollbars=yes,resizable=yes`);
    }

    document.querySelectorAll('#crm-map-block-' + projectId + ' .crm-map-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;
            document.querySelectorAll('#crm-map-block-' + projectId + ' .crm-map-tab').forEach(b => {
                b.classList.remove('border-gray-200', 'bg-white', 'text-gray-800');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            btn.classList.add('border-gray-200', 'bg-white', 'text-gray-800');
            btn.classList.remove('border-transparent', 'text-gray-500');
            document.querySelectorAll('#crm-map-block-' + projectId + ' .crm-map-pane').forEach(p => p.classList.add('hidden'));
            document.querySelector('#crm-map-block-' + projectId + ' [data-pane="' + tab + '"]')?.classList.remove('hidden');
            if (tab === 'satellite' && !satMap) {
                satMap = buildLeafletMap(satMapId,
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    '&copy; Esri, Maxar, Earthstar', true);
            }
            if (tab === 'map') setTimeout(() => streetMap?.invalidateSize(), 200);
            if (tab === 'satellite') setTimeout(() => satMap?.invalidateSize(), 200);
        });
    });

    document.querySelectorAll('#crm-map-block-' + projectId + ' .crm-map-fullscreen-btn').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.dataset.fullscreen));
    });

    document.querySelectorAll('#crm-map-block-' + projectId + ' .crm-open-viewer').forEach(btn => {
        btn.addEventListener('click', () => openViewerWindow(btn.dataset.mode));
    });

    modal?.querySelector('.crm-map-modal-close')?.addEventListener('click', () => modal.classList.remove('open'));
    modal?.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });
});
</script>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\projects\partials\map-display.blade.php ENDPATH**/ ?>