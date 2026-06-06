@php
    use App\Helpers\GoogleMapsHelper;
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $panelId = $panelId ?? 'google-maps-panel-' . uniqid();
    $pins = collect($pins ?? []);
    if ($pins->isEmpty() && isset($project) && $project->hasMapLocation()) {
        $pins = collect([(object)[
            'title' => $project->name,
            'latitude' => $project->latitude,
            'longitude' => $project->longitude,
        ]]);
    }
    $hasEmbed = GoogleMapsHelper::hasEmbedSupport();
    $primary = $pins->first();
    $embedded = !empty($embedded);
@endphp

@if($pins->isNotEmpty() && $primary)
<div class="{{ $embedded ? 'w-full' : 'bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full' }}" id="{{ $panelId }}">
    @if(!$embedded)
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-gray-900 font-tajawal">عرض Google Maps</h3>
                <p class="text-xs text-gray-500 mt-1 font-tajawal">قمر صناعي · Street View حيوي · روابط خارجية</p>
            </div>
    @else
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    @endif
            @if($pins->count() > 1)
            <select class="google-pin-select border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal max-w-xs"
                    data-panel="{{ $panelId }}">
                @foreach($pins as $i => $pin)
                    <option value="{{ $i }}"
                            data-lat="{{ $pin->latitude }}"
                            data-lng="{{ $pin->longitude }}"
                            data-title="{{ $pin->title }}">
                        {{ $pin->title }}
                    </option>
                @endforeach
            </select>
            @endif
        </div>
    </div>

    <div class="border-b border-gray-100 px-4 sm:px-5 pt-3 flex flex-wrap gap-2 google-map-tabs" data-panel="{{ $panelId }}">
        @if($hasEmbed)
        <button type="button" data-tab="satellite" class="gmap-tab px-4 py-2 rounded-t-xl text-xs font-bold font-tajawal border-2 border-b-0 border-gray-200 bg-white text-gray-700">قمر صناعي</button>
        <button type="button" data-tab="streetview" class="gmap-tab px-4 py-2 rounded-t-xl text-xs font-bold font-tajawal border-2 border-transparent text-gray-500">Street View</button>
        <button type="button" data-tab="place" class="gmap-tab px-4 py-2 rounded-t-xl text-xs font-bold font-tajawal border-2 border-transparent text-gray-500">خريطة Google</button>
        @endif
        <button type="button" data-tab="links" class="gmap-tab px-4 py-2 rounded-t-xl text-xs font-bold font-tajawal border-2 {{ $hasEmbed ? 'border-transparent text-gray-500' : 'border-b-0 border-gray-200 bg-white text-gray-700' }}">روابط خارجية</button>
    </div>

    <div class="p-4 sm:p-5">
        @if($hasEmbed)
        <div class="gmap-pane hidden" data-pane="satellite" data-panel="{{ $panelId }}">
            <iframe class="gmap-iframe w-full rounded-xl border border-gray-200" style="height:380px;border:0;" loading="lazy" allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="{{ GoogleMapsHelper::satelliteEmbedUrl($primary->latitude, $primary->longitude) }}"></iframe>
        </div>
        <div class="gmap-pane hidden" data-pane="streetview" data-panel="{{ $panelId }}">
            <iframe class="gmap-iframe w-full rounded-xl border border-gray-200" style="height:380px;border:0;" loading="lazy" allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="{{ GoogleMapsHelper::streetViewEmbedUrl($primary->latitude, $primary->longitude) }}"></iframe>
            <p class="text-xs text-gray-400 mt-2 font-tajawal">إذا لم يتوفر Street View في هذا الموقع، استخدم زر «فتح Street View» بالأسفل.</p>
        </div>
        <div class="gmap-pane hidden" data-pane="place" data-panel="{{ $panelId }}">
            <iframe class="gmap-iframe w-full rounded-xl border border-gray-200" style="height:380px;border:0;" loading="lazy" allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="{{ GoogleMapsHelper::placeEmbedUrl($primary->latitude, $primary->longitude, $primary->title) }}"></iframe>
        </div>
        @else
        <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-100 text-sm font-tajawal text-amber-900">
            لعرض القمر الصناعي وStreet View <strong>داخل الصفحة</strong>، أضف <code class="text-xs bg-white px-1 rounded">GOOGLE_MAPS_API_KEY</code> في ملف <code class="text-xs bg-white px-1 rounded">.env</code>
            وتفعّل <strong>Maps Embed API</strong> من Google Cloud.
            <br><span class="text-xs text-amber-700 mt-1 inline-block">الروابط الخارجية تعمل الآن بدون مفتاح.</span>
        </div>
        @endif

        <div class="gmap-pane {{ $hasEmbed ? 'hidden' : '' }}" data-pane="links" data-panel="{{ $panelId }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ GoogleMapsHelper::mapsUrl($primary->latitude, $primary->longitude, $primary->title) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="gmap-ext-link flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition font-tajawal"
                   data-link="maps">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">🗺️</span>
                    <span>
                        <span class="block font-bold text-sm text-gray-900">فتح في Google Maps</span>
                        <span class="text-xs text-gray-500">تبويب خارجي — خريطة تفاعلية</span>
                    </span>
                </a>
                <a href="{{ GoogleMapsHelper::streetViewUrl($primary->latitude, $primary->longitude) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="gmap-ext-link flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition font-tajawal"
                   data-link="streetview">
                    <span class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-lg">🚶</span>
                    <span>
                        <span class="block font-bold text-sm text-gray-900">Street View حيوي</span>
                        <span class="text-xs text-gray-500">جولة 360° خارج النظام</span>
                    </span>
                </a>
                <a href="{{ GoogleMapsHelper::satelliteUrl($primary->latitude, $primary->longitude) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="gmap-ext-link flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition font-tajawal"
                   data-link="satellite">
                    <span class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg">🛰️</span>
                    <span>
                        <span class="block font-bold text-sm text-gray-900">قمر صناعي</span>
                        <span class="text-xs text-gray-500">عرض جوي في Google Maps</span>
                    </span>
                </a>
                <a href="{{ GoogleMapsHelper::directionsUrl($primary->latitude, $primary->longitude) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="gmap-ext-link flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition font-tajawal"
                   data-link="directions">
                    <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">🧭</span>
                    <span>
                        <span class="block font-bold text-sm text-gray-900">الاتجاهات</span>
                        <span class="text-xs text-gray-500">توجيه GPS من موقعك</span>
                    </span>
                </a>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button" class="gmap-copy-link px-4 py-2 rounded-xl text-xs font-semibold font-tajawal text-white"
                        style="background: {{ $themeColor }};"
                        data-url="{{ GoogleMapsHelper::mapsUrl($primary->latitude, $primary->longitude, $primary->title) }}">
                    نسخ رابط المشاركة
                </button>
                <button type="button" class="gmap-copy-link px-4 py-2 rounded-xl text-xs font-semibold font-tajawal border-2 border-gray-200 text-gray-700"
                        data-url="{{ GoogleMapsHelper::streetViewUrl($primary->latitude, $primary->longitude) }}">
                    نسخ رابط Street View
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const panelId = @json($panelId);
    const hasEmbed = @json($hasEmbed);
    const apiKey = @json(GoogleMapsHelper::apiKey());
    const root = document.getElementById(panelId);
    if (!root) return;

    const pins = @json($pins->map(fn ($p) => [
        'title' => $p->title,
        'lat' => (float) $p->latitude,
        'lng' => (float) $p->longitude,
    ])->values());

    function urlsFor(pin) {
        const c = pin.lat + ',' + pin.lng;
        const q = encodeURIComponent(pin.title + '@' + c);
        return {
            maps: 'https://www.google.com/maps/search/?api=1&query=' + q,
            street: 'https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=' + c,
            satellite: 'https://www.google.com/maps/@' + c + ',18z/data=!3m1!1e3',
            directions: 'https://www.google.com/maps/dir/?api=1&destination=' + c,
            embedSatellite: apiKey ? 'https://www.google.com/maps/embed/v1/view?key=' + encodeURIComponent(apiKey) + '&center=' + encodeURIComponent(c) + '&zoom=18&maptype=satellite' : null,
            embedStreet: apiKey ? 'https://www.google.com/maps/embed/v1/streetview?key=' + encodeURIComponent(apiKey) + '&location=' + encodeURIComponent(c) + '&heading=210&pitch=10&fov=90' : null,
            embedPlace: apiKey ? 'https://www.google.com/maps/embed/v1/place?key=' + encodeURIComponent(apiKey) + '&q=' + q + '&zoom=18' : null,
        };
    }

    function activePin() {
        const sel = root.querySelector('.google-pin-select');
        const idx = sel ? parseInt(sel.value, 10) : 0;
        return pins[idx] || pins[0];
    }

    function refreshLinks() {
        const u = urlsFor(activePin());
        root.querySelectorAll('.gmap-ext-link').forEach(a => {
            const t = a.dataset.link;
            if (t === 'maps') a.href = u.maps;
            if (t === 'streetview') a.href = u.street;
            if (t === 'satellite') a.href = u.satellite;
            if (t === 'directions') a.href = u.directions;
        });
        const copyBtns = root.querySelectorAll('.gmap-copy-link');
        if (copyBtns[0]) copyBtns[0].dataset.url = u.maps;
        if (copyBtns[1]) copyBtns[1].dataset.url = u.street;
        if (hasEmbed) {
            root.querySelectorAll('.gmap-iframe').forEach((iframe, i) => {
                const src = [u.embedSatellite, u.embedStreet, u.embedPlace][i];
                if (src) iframe.src = src;
            });
        }
    }

    root.querySelectorAll('.gmap-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;
            root.querySelectorAll('.gmap-tab').forEach(b => {
                b.classList.remove('border-gray-200', 'border-b-0', 'bg-white', 'text-gray-700');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            btn.classList.add('border-gray-200', 'border-b-0', 'bg-white', 'text-gray-700');
            btn.classList.remove('border-transparent', 'text-gray-500');
            root.querySelectorAll('.gmap-pane').forEach(p => p.classList.add('hidden'));
            root.querySelector('[data-pane="' + tab + '"]')?.classList.remove('hidden');
        });
    });

    root.querySelector('.google-pin-select')?.addEventListener('change', refreshLinks);

    root.querySelectorAll('.gmap-copy-link').forEach(btn => {
        btn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(btn.dataset.url || '');
                const prev = btn.textContent;
                btn.textContent = 'تم النسخ ✓';
                setTimeout(() => { btn.textContent = prev; }, 2000);
            } catch (e) {
                prompt('انسخ الرابط:', btn.dataset.url);
            }
        });
    });

    if (hasEmbed) {
        root.querySelector('.gmap-tab[data-tab="satellite"]')?.click();
    }
});
</script>
@endif
