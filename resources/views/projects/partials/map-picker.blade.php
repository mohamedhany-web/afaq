@php
    use App\Helpers\MapLocationHelper;
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $existingPins = [];
    if (isset($project)) {
        if (MapLocationHelper::hasReliableCoordinates($project)) {
            $existingPins[] = [
                'title' => $project->name,
                'pin_type' => 'project',
                'latitude' => $project->latitude,
                'longitude' => $project->longitude,
                'notes' => '',
            ];
        }
        foreach ($project->mapPins ?? [] as $pin) {
            if ($pin->pin_type === 'project' && MapLocationHelper::hasReliableCoordinates($project)) {
                continue;
            }
            $existingPins[] = [
                'title' => $pin->title,
                'pin_type' => $pin->pin_type,
                'latitude' => $pin->latitude,
                'longitude' => $pin->longitude,
                'notes' => $pin->notes,
            ];
        }
    }
    $initialPins = old('map_pins_payload')
        ? json_decode(old('map_pins_payload'), true)
        : $existingPins;
    $savedLat = old('latitude', isset($project) && MapLocationHelper::hasReliableCoordinates($project) ? $project->latitude : null);
    $savedLng = old('longitude', isset($project) && MapLocationHelper::hasReliableCoordinates($project) ? $project->longitude : null);
    $defaultLat = $savedLat ?? '';
    $defaultLng = $savedLng ?? '';
    $defaultZoom = old('map_zoom', $project->map_zoom ?? 14);
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full" id="project-map-section">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        الموقع على الخريطة
        <p class="text-xs font-normal text-gray-500 mt-1">ابحث عن المكان ثم انقر على الخريطة لتحديد موقع المشروع — بدون تحديد، لن يُحفظ موقع افتراضي</p>
    </div>
    <div class="p-5 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="text" id="map-search-input" placeholder="ابحث: التجمع الخامس، مدينة نصر، شارع…"
                   class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-tajawal">
            <button type="button" id="map-search-btn" class="px-5 py-3 rounded-xl text-white text-sm font-semibold font-tajawal shrink-0"
                    style="background: {{ $themeColor }};">بحث</button>
            <button type="button" id="map-add-unit-pin" class="px-5 py-3 rounded-xl border-2 text-sm font-semibold font-tajawal shrink-0"
                    style="border-color: {{ $themeColor }}40; color: {{ $themeColor }};">+ علامة وحدة</button>
            <button type="button" id="map-layer-toggle" class="px-5 py-3 rounded-xl border-2 text-sm font-semibold font-tajawal shrink-0 bg-gray-50 text-gray-700"
                    data-layer="street">🛰️ عرض جوي</button>
        </div>

        <div id="project-map" class="w-full rounded-xl border-2 border-gray-200 overflow-hidden" style="height: 380px;"></div>

        <input type="hidden" name="latitude" id="map_latitude" value="{{ $defaultLat }}">
        <input type="hidden" name="longitude" id="map_longitude" value="{{ $defaultLng }}">
        <input type="hidden" name="map_zoom" id="map_zoom" value="{{ $defaultZoom }}">
        <input type="hidden" name="map_pins_payload" id="map_pins_payload" value="{{ json_encode($initialPins, JSON_UNESCAPED_UNICODE) }}">

        <div id="map-pins-list" class="space-y-2"></div>
        <p class="text-xs text-gray-400 font-tajawal">💡 انقر على الخريطة لوضع علامة · زر «عرض جوي» للصور الجوية داخل الصفحة · اسحب السهم لضبط الموقع</p>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<style>
    .crm-map-pin {
        width: 28px; height: 36px; position: relative;
        display: flex; align-items: flex-end; justify-content: center;
    }
    .crm-map-pin-body {
        width: 22px; height: 22px; border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg); border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,.35);
    }
    .crm-map-pin.project .crm-map-pin-body { background: {{ $themeColor }}; }
    .crm-map-pin.unit .crm-map-pin-body { background: #f59e0b; }
    .crm-map-pin.landmark .crm-map-pin-body { background: #6366f1; }
    .crm-map-pin.entrance .crm-map-pin-body { background: #10b981; }
    .crm-map-pin-arrow {
        position: absolute; bottom: -2px; left: 50%; transform: translateX(-50%);
        width: 0; height: 0;
        border-left: 6px solid transparent; border-right: 6px solid transparent;
        border-top: 8px solid rgba(0,0,0,.25);
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const themeColor = @json($themeColor);
    let pins = @json($initialPins);
    const latInput = document.getElementById('map_latitude');
    const lngInput = document.getElementById('map_longitude');
    const zoomInput = document.getElementById('map_zoom');
    const payloadInput = document.getElementById('map_pins_payload');
    const pinsList = document.getElementById('map-pins-list');

    const savedLat = latInput.value !== '' ? parseFloat(latInput.value) : null;
    const savedLng = lngInput.value !== '' ? parseFloat(lngInput.value) : null;
    const startLat = savedLat ?? 30.0444;
    const startLng = savedLng ?? 31.2357;
    const startZoom = parseInt(zoomInput.value, 10) || 14;

    const map = L.map('project-map', { scrollWheelZoom: true }).setView([startLat, startLng], startZoom);
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OSM' });
    const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 20, attribution: '&copy; Esri' });
    streetLayer.addTo(map);
    let activeLayer = 'street';

    document.getElementById('map-layer-toggle')?.addEventListener('click', function () {
        if (activeLayer === 'street') {
            map.removeLayer(streetLayer);
            satLayer.addTo(map);
            activeLayer = 'satellite';
            this.textContent = '🗺️ خريطة';
            this.classList.add('text-white');
            this.style.background = themeColor;
        } else {
            map.removeLayer(satLayer);
            streetLayer.addTo(map);
            activeLayer = 'street';
            this.textContent = '🛰️ عرض جوي';
            this.classList.remove('text-white');
            this.style.background = '';
        }
    });

    const markers = [];

    function pinIcon(type) {
        return L.divIcon({
            className: '',
            html: `<div class="crm-map-pin ${type}"><div class="crm-map-pin-body"></div><div class="crm-map-pin-arrow"></div></div>`,
            iconSize: [28, 36],
            iconAnchor: [14, 36],
        });
    }

    function syncPayload() {
        payloadInput.value = JSON.stringify(pins);
        const main = pins.find(p => p.pin_type === 'project') || pins[0];
        if (main) {
            latInput.value = main.latitude;
            lngInput.value = main.longitude;
        } else {
            latInput.value = '';
            lngInput.value = '';
        }
        renderPinsList();
    }

    function renderPinsList() {
        pinsList.innerHTML = pins.map((p, i) => `
            <div class="flex flex-wrap items-center gap-2 p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm font-tajawal">
                <span class="font-bold text-gray-800">${p.title}</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-white border">${p.pin_type === 'project' ? 'المشروع' : (p.pin_type === 'unit' ? 'وحدة' : p.pin_type)}</span>
                <span class="text-xs text-gray-400" dir="ltr">${Number(p.latitude).toFixed(5)}, ${Number(p.longitude).toFixed(5)}</span>
                ${p.pin_type !== 'project' ? `<button type="button" data-remove-pin="${i}" class="text-xs text-red-600 mr-auto">حذف</button>` : ''}
            </div>
        `).join('');

        pinsList.querySelectorAll('[data-remove-pin]').forEach(btn => {
            btn.addEventListener('click', () => removePin(parseInt(btn.dataset.removePin, 10)));
        });
    }

    function addMarkerForPin(pin, index) {
        const marker = L.marker([pin.latitude, pin.longitude], {
            icon: pinIcon(pin.pin_type || 'unit'),
            draggable: true,
        }).addTo(map);

        marker.bindPopup(`<strong>${pin.title}</strong>`);
        marker.on('dragend', () => {
            const pos = marker.getLatLng();
            pins[index].latitude = pos.lat;
            pins[index].longitude = pos.lng;
            syncPayload();
        });
        marker.on('dblclick', () => {
            if (pin.pin_type !== 'project') removePin(index);
        });

        markers[index] = marker;
    }

    function rebuildMarkers() {
        markers.forEach(m => m && map.removeLayer(m));
        markers.length = 0;
        pins.forEach((p, i) => addMarkerForPin(p, i));
        syncPayload();
    }

    function removePin(index) {
        if (pins[index]?.pin_type === 'project') return;
        if (markers[index]) map.removeLayer(markers[index]);
        pins.splice(index, 1);
        rebuildMarkers();
    }

    function addPin(lat, lng, type, title) {
        pins.push({
            title: title || (type === 'unit' ? 'وحدة ' + (pins.filter(p => p.pin_type === 'unit').length + 1) : 'علامة'),
            pin_type: type,
            latitude: lat,
            longitude: lng,
            notes: '',
        });
        rebuildMarkers();
    }

    map.on('click', function (e) {
        const projectIdx = pins.findIndex(p => p.pin_type === 'project');
        if (projectIdx >= 0) {
            pins[projectIdx].latitude = e.latlng.lat;
            pins[projectIdx].longitude = e.latlng.lng;
            if (markers[projectIdx]) markers[projectIdx].setLatLng(e.latlng);
            syncPayload();
        } else {
            addPin(e.latlng.lat, e.latlng.lng, 'project', @json(isset($project) ? $project->name : 'موقع المشروع'));
        }
    });

    map.on('zoomend', () => { zoomInput.value = map.getZoom(); });

    document.getElementById('map-add-unit-pin')?.addEventListener('click', () => {
        const c = map.getCenter();
        addPin(c.lat, c.lng, 'unit', 'وحدة ' + (pins.filter(p => p.pin_type === 'unit').length + 1));
    });

    async function searchMap() {
        const q = document.getElementById('map-search-input').value.trim();
        if (!q) return;
        const btn = document.getElementById('map-search-btn');
        btn.disabled = true;
        btn.textContent = 'جاري البحث…';
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&accept-language=ar&q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json' },
            });
            const data = await res.json();
            if (!data.length) {
                alert('لم يتم العثور على نتائج — جرّب وصفاً أدق');
                return;
            }
            const hit = data[0];
            const lat = parseFloat(hit.lat);
            const lng = parseFloat(hit.lon);
            map.setView([lat, lng], 16);
            zoomInput.value = 16;
            const projectIdx = pins.findIndex(p => p.pin_type === 'project');
            if (projectIdx >= 0) {
                pins[projectIdx].latitude = lat;
                pins[projectIdx].longitude = lng;
                markers[projectIdx]?.setLatLng([lat, lng]);
            } else {
                addPin(lat, lng, 'project', hit.display_name?.split(',')[0] || 'موقع المشروع');
            }
            syncPayload();
        } catch (e) {
            alert('تعذر البحث — تحقق من الاتصال');
        } finally {
            btn.disabled = false;
            btn.textContent = 'بحث';
        }
    }

    document.getElementById('map-search-btn')?.addEventListener('click', searchMap);
    document.getElementById('map-search-input')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); searchMap(); }
    });

    if (pins.length) {
        rebuildMarkers();
        const bounds = L.latLngBounds(pins.map(p => [p.latitude, p.longitude]));
        if (pins.length > 1) map.fitBounds(bounds.pad(0.2));
    }

    setTimeout(() => map.invalidateSize(), 300);
});
</script>
