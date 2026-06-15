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
    $cardHref = $href ?? $link ?? null;
    $cardLinkLabel = $linkLabel ?? $linkText ?? 'عرض التفاصيل';
    $cardLinkTarget = $linkTarget ?? null;
    $hasFooterArea = !empty($footer) || !empty($cardHref);
@endphp
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 {{ $pad }} hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 transform group flex flex-col h-full min-h-[108px] sm:min-h-[118px] {{ $class ?? '' }}">
    <div class="flex items-center justify-between gap-3 sm:gap-4 {{ $hasFooterArea ? 'mb-3 sm:mb-4' : '' }}">
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
    @if($hasFooterArea)
    <div class="pt-3 border-t border-gray-100 mt-auto font-tajawal">
        @if(!empty($footer))
        <div class="text-xs sm:text-sm {{ !empty($cardHref) ? 'mb-2' : '' }}">
            {!! $footer !!}
        </div>
        @endif
        @if(!empty($cardHref))
        <a href="{{ $cardHref }}"
           @if($cardLinkTarget) target="{{ $cardLinkTarget }}" @if($cardLinkTarget === '_blank') rel="noopener noreferrer" @endif @endif
           class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold hover:underline transition-colors"
           style="color: {{ $color }};">
            <span>{{ $cardLinkLabel }}</span>
            <svg class="w-3.5 h-3.5 shrink-0 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @endif
    </div>
    @endif
</div>
