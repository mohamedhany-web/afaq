@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
@endphp
<div class="mb-6 sm:mb-8">
    <div class="rounded-2xl p-5 sm:p-6 lg:p-8 shadow-xl border overflow-hidden relative"
         style="background: linear-gradient(135deg, {{ $themeColor }}15 0%, {{ $themeColor }}05 50%, {{ $themeColor }}10 100%); border-color: {{ $themeColor }}30;">
        <div class="absolute top-0 left-0 w-full h-full opacity-5 overflow-hidden pointer-events-none">
            <div class="absolute top-10 right-10 w-64 h-64 rounded-full" style="background: {{ $themeColor }};"></div>
            <div class="absolute bottom-10 left-10 w-48 h-48 rounded-full" style="background: {{ $themeColor }};"></div>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if(!empty($icon))
                <div class="h-14 w-14 rounded-2xl flex items-center justify-center shadow-xl flex-shrink-0"
                     style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $icon !!}</svg>
                </div>
                @endif
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 font-tajawal">{{ $title }}</h1>
                    @if(!empty($subtitle))
                    <p class="text-sm text-gray-600 mt-1 font-tajawal">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            @if(!empty($actionUrl))
            <a href="{{ $actionUrl }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all"
               style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                @if(!empty($actionIcon))
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $actionIcon !!}</svg>
                @endif
                {{ $actionLabel ?? 'إضافة' }}
            </a>
            @endif
        </div>
    </div>
</div>
