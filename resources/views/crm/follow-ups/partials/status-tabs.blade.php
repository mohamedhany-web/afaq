@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $indexRoute = $routes['index'] ?? route('crm.follow-ups.index');
    $queryExcept = ['bucket', 'filter', 'page'];
    $activeBucket = $bucket ?? request('bucket', request('filter'));
@endphp

<div class="flex flex-wrap gap-1.5 mb-4 font-tajawal">
    @foreach($buckets as $key => $label)
    @php
        $tabParams = array_filter(array_merge(request()->except($queryExcept), $key !== 'all' ? ['bucket' => $key] : []));
        $isActive = ($activeBucket === $key) || (!$activeBucket && $key === 'all');
    @endphp
    <a href="{{ $indexRoute . ($tabParams ? '?' . http_build_query($tabParams) : '') }}#page-data"
       class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-colors {{ $isActive ? 'text-white border-transparent' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}"
       @if($isActive) style="background:{{ $themeColor }}" @endif>
        {{ $label }}
        @if(isset($stats[$key]))
        <span class="opacity-80">({{ $stats[$key] }})</span>
        @endif
    </a>
    @endforeach
</div>
