@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => $v !== null ? \App\Helpers\SettingsHelper::formatMoney($v) : '—';
    $activeClassifications = $activeClassifications ?? $project->activeClassifications();
    $classColors = config('project_classifications.colors', []);
    $summaries = collect($activeClassifications)
        ->mapWithKeys(fn ($k) => [$k => $project->classificationSummary($k)])
        ->filter();
    $defaultClass = $defaultClass ?? request('classification', request('use_type', $activeClassifications[0] ?? null));
    $filterMode = $filterMode ?? 'units';
    $opsFilterUrl = $opsFilterUrl ?? null;
@endphp

@if($summaries->isNotEmpty())
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 font-tajawal" id="project-classification-panel"
     data-default-class="{{ $defaultClass }}">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <h2 class="font-bold text-gray-900">تصنيف المشروع والأسعار</h2>
        <p class="text-xs text-gray-500 mt-1">اختر التصنيف لعرض نطاق الأسعار والمساحات والوحدات الخاصة به فقط</p>
    </div>
    <div class="p-5 sm:p-6">
        <div class="flex flex-wrap gap-2 mb-4" id="classification-filter-buttons">
            @foreach($summaries as $key => $summary)
            @php $color = $classColors[$key] ?? $themeColor; @endphp
            <button type="button"
                    class="classification-filter-btn px-4 py-2 rounded-xl text-xs font-bold border-2 transition"
                    data-classification="{{ $key }}"
                    style="border-color: {{ $color }}40; color: {{ $color }};">
                {{ $summary['label'] }}
            </button>
            @endforeach
        </div>
        <div id="classification-summary-cards" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($summaries as $key => $summary)
            @php $color = $classColors[$key] ?? $themeColor; @endphp
            <div class="classification-summary-card hidden rounded-xl border-2 p-4"
                 data-classification="{{ $key }}"
                 style="border-color: {{ $color }}35; background: {{ $color }}08;">
                <h3 class="text-sm font-bold mb-3" style="color: {{ $color }}">{{ $summary['label'] }}</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">السعر من</dt>
                        <dd class="font-bold text-gray-900">{{ $money($summary['price_from']) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">السعر إلى</dt>
                        <dd class="font-bold text-gray-900">{{ $money($summary['price_to']) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">المساحة من</dt>
                        <dd class="font-bold text-gray-900">{{ $summary['area_from'] !== null ? number_format($summary['area_from']) . ' م²' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">المساحة إلى</dt>
                        <dd class="font-bold text-gray-900">{{ $summary['area_to'] !== null ? number_format($summary['area_to']) . ' م²' : '—' }}</dd>
                    </div>
                </dl>
            </div>
            @endforeach
        </div>
    </div>
</div>
@push('scripts')
<script>
(function () {
    const panel = document.getElementById('project-classification-panel');
    if (!panel) return;

    const themeColor = @json($themeColor);
    const classColors = @json($classColors);
    const filterMode = @json($filterMode);
    const opsFilterUrl = @json($opsFilterUrl);
    const defaultClass = panel.dataset.defaultClass || '';
    let active = defaultClass;

    function applyClassification(key) {
        if (filterMode === 'operations' && opsFilterUrl) {
            const url = new URL(opsFilterUrl, window.location.origin);
            url.searchParams.set('use_type', key);
            url.hash = 'page-data';
            window.location.href = url.toString();
            return;
        }
        active = key;
        panel.querySelectorAll('.classification-filter-btn').forEach(btn => {
            const on = btn.dataset.classification === key;
            const color = classColors[key] || themeColor;
            btn.classList.toggle('text-white', on);
            btn.style.background = on ? color : '';
            btn.style.borderColor = on ? color : (classColors[btn.dataset.classification] || themeColor) + '40';
        });
        panel.querySelectorAll('.classification-summary-card').forEach(card => {
            card.classList.toggle('hidden', card.dataset.classification !== key);
        });
        const detail = document.getElementById('project-detail-price-block');
        if (detail) {
            const card = panel.querySelector('.classification-summary-card[data-classification="' + key + '"]');
            if (card) {
                const label = card.querySelector('h3')?.textContent || '';
                const prices = card.querySelectorAll('dd');
                detail.innerHTML = '<dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">السعر — ' + label + '</dt>'
                    + '<dd class="font-bold font-tajawal" style="color:' + themeColor + '">'
                    + (prices[0]?.textContent || '—') + ' — ' + (prices[1]?.textContent || '—') + '</dd>';
            }
        }
        document.dispatchEvent(new CustomEvent('classification-changed', { detail: { key } }));
        const url = new URL(window.location.href);
        url.searchParams.set('classification', key);
        history.replaceState({}, '', url);
    }

    panel.querySelectorAll('.classification-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => applyClassification(btn.dataset.classification));
    });

    if (active) applyClassification(active);
    else {
        const first = panel.querySelector('.classification-filter-btn');
        if (first) applyClassification(first.dataset.classification);
    }
})();
</script>
@endpush
@endif
