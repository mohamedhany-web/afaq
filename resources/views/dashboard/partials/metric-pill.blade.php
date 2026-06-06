@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $accent = $accent ?? 'theme';
    $accentColors = [
        'theme' => $themeColor,
        'green' => '#16a34a',
        'purple' => '#9333ea',
        'red' => '#dc2626',
    ];
    $color = $accentColors[$accent] ?? $themeColor;
@endphp
<div class="text-center p-3 sm:p-4 rounded-2xl bg-white shadow-lg hover:shadow-xl transition-all duration-300 border flex-1 sm:flex-none min-w-[96px] sm:min-w-[108px]"
     style="border-color: {{ $themeColor }}25;">
    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1 tabular-nums font-tajawal" style="color: {{ $color }};">{{ $value }}</div>
    <div class="text-xs text-gray-500 font-tajawal leading-snug">{{ $label }}</div>
</div>
