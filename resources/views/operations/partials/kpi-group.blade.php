@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $group = $group ?? null;
@endphp
@if($group)
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex items-center justify-between gap-3" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">
        <div>
            <p class="font-bold text-gray-900">{{ $group['label'] }}</p>
            <p class="text-xs text-gray-500">النتيجة الإجمالية: {{ number_format($group['score'], 1) }}%</p>
        </div>
        @if(!empty($link))
        <a href="{{ $link }}" class="text-xs font-bold px-3 py-1.5 rounded-lg border hover:bg-gray-50" style="color:{{ $themeColor }}">التفاصيل</a>
        @endif
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($group['items'] as $item)
        @php
            $statusColors = ['excellent' => 'text-green-700 bg-green-50', 'good' => 'text-blue-700 bg-blue-50', 'warning' => 'text-amber-700 bg-amber-50', 'critical' => 'text-red-700 bg-red-50'];
            $badge = $statusColors[$item['status']] ?? 'text-gray-700 bg-gray-50';
        @endphp
        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
            <p class="text-xs text-gray-500 mb-1">{{ $item['label'] }}</p>
            <div class="flex items-end justify-between gap-2">
                <p class="text-lg font-extrabold text-gray-900">{{ number_format($item['value'], 1) }} <span class="text-xs font-normal text-gray-500">{{ $item['unit'] }}</span></p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $badge }}">{{ number_format($item['achievement'], 0) }}%</span>
            </div>
            <p class="text-[10px] text-gray-400 mt-1">المستهدف: {{ number_format($item['target'], 1) }} {{ $item['unit'] }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif
