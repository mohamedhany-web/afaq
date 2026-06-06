@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $accent = $accent ?? 'theme';
    $accentColors = [
        'theme' => $themeColor,
        'green' => '#16a34a',
        'blue' => '#2563eb',
        'amber' => '#d97706',
        'purple' => '#9333ea',
        'red' => '#dc2626',
    ];
    $color = $accentColors[$accent] ?? $themeColor;
    $compact = !empty($compact);
    $pad = $compact ? 'p-4 sm:p-5' : 'p-5 sm:p-6';
    $valueClass = $compact ? 'text-xl sm:text-2xl' : 'text-2xl sm:text-3xl lg:text-4xl';
    $labelClass = $compact ? 'text-xs' : 'text-xs sm:text-sm';
    $iconBox = $compact ? 'p-2.5 sm:p-3' : 'p-3 sm:p-4';
    $iconSvg = $compact ? 'w-5 h-5 sm:w-6 sm:h-6' : 'w-6 h-6 sm:w-7 sm:h-7';
@endphp
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 {{ $pad }} hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 transform group flex flex-col h-full min-h-[108px] sm:min-h-[118px] {{ $class ?? '' }}">
    <div class="flex items-center justify-between gap-3 sm:gap-4 {{ !empty($footer) ? 'mb-3 sm:mb-4' : '' }}">
        <div class="text-right flex-1 min-w-0">
            <div class="{{ $labelClass }} text-gray-500 mb-1.5 font-tajawal leading-snug">{{ $label }}</div>
            <div class="{{ $valueClass }} font-bold text-gray-900 font-tajawal tabular-nums leading-tight break-words">{{ $value }}</div>
        </div>
        @if(!empty($icon))
        <div class="{{ $iconBox }} rounded-2xl shadow-lg flex-shrink-0 group-hover:scale-110 transition-transform duration-300"
             style="background: linear-gradient(135deg, {{ $color }} 0%, {{ $color }}dd 100%);">
            <svg class="{{ $iconSvg }} text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $icon !!}</svg>
        </div>
        @endif
    </div>
    @if(!empty($footer))
    <div class="pt-3 border-t border-gray-100 mt-auto text-xs sm:text-sm font-tajawal">
        {!! $footer !!}
    </div>
    @endif
</div>
