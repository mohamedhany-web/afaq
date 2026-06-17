@php
    $isEn = app()->getLocale() === 'en';
    $switchLocale = $isEn ? 'ar' : 'en';
    $switchLabel = $isEn ? __('operations.actions.switch_language_ar') : __('operations.actions.switch_language');
@endphp
<a href="{{ route('locale.switch', $switchLocale) }}"
   class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-bold text-gray-700 hover:bg-gray-50 transition font-tajawal shrink-0"
   title="{{ $switchLabel }}">
    <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
    </svg>
    <span class="hidden sm:inline">{{ $switchLabel }}</span>
</a>
