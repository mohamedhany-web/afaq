@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
@endphp
<div class="mb-4 flex flex-wrap items-center gap-3 font-tajawal">
    @include('partials.ui-compact-toggle', [
        'themeColor' => $themeColor,
        'labelOn' => __('operations.ui.compact_on'),
        'labelOff' => __('operations.ui.compact_off'),
    ])
</div>
