<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($modeLabel); ?> — <?php echo e($project->name); ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: #111; color: #fff; height: 100vh; display: flex; flex-direction: column; }
        header { padding: 10px 16px; background: #1f2937; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
        header h1 { margin: 0; font-size: 15px; font-weight: 700; }
        header span { font-size: 12px; opacity: .7; }
        #viewer-map, #viewer-iframe { flex: 1; width: 100%; min-height: 0; }
        #viewer-iframe { border: 0; }
        .crm-map-pin { width:28px;height:36px;display:flex;align-items:flex-end;justify-content:center; }
        .crm-map-pin-body { width:22px;height:22px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.4);background:<?php echo e($themeColor); ?>; }
    </style>
</head>
<body>
<header>
    <div>
        <h1><?php echo e($project->name); ?></h1>
        <span><?php echo e($modeLabel); ?> · <?php echo e($project->city ?? ''); ?></span>
    </div>
    <button onclick="window.close()" style="background:#374151;border:0;color:#fff;padding:8px 14px;border-radius:8px;cursor:pointer;font-family:inherit;font-size:13px;">إغلاق</button>
</header>

<?php if($mode === 'streetview' && $streetEmbed): ?>
    <iframe id="viewer-iframe" src="<?php echo e($streetEmbed); ?>" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
<?php else: ?>
    <div id="viewer-map"></div>
    <script>
    (function () {
        const pins = <?php echo json_encode($pins, 15, 512) ?>;
        const mode = <?php echo json_encode($mode, 15, 512) ?>;
        const zoom = <?php echo e($project->map_zoom ?? 16); ?>;
        const m = L.map('viewer-map', { scrollWheelZoom: true }).setView([pins[0].lat, pins[0].lng], mode === 'satellite' ? zoom + 1 : zoom);
        const tile = mode === 'satellite'
            ? 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
            : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        L.tileLayer(tile, { maxZoom: 20 }).addTo(m);
        const icon = L.divIcon({ className: '', html: '<div class="crm-map-pin"><div class="crm-map-pin-body"></div></div>', iconSize: [28,36], iconAnchor: [14,36] });
        pins.forEach(p => L.marker([p.lat, p.lng], { icon }).addTo(m).bindPopup(p.title));
        if (pins.length > 1) m.fitBounds(L.latLngBounds(pins.map(p => [p.lat, p.lng])).pad(0.08));
    })();
    </script>
<?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\public\project-map-viewer.blade.php ENDPATH**/ ?>