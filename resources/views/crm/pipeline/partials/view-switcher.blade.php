@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $query = request()->except('view');
@endphp
<div class="flex flex-wrap items-center gap-2 mb-4">
    <span class="text-xs text-gray-500 font-tajawal">العرض:</span>
    <a href="{{ route('crm.pipeline.index', $query) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal {{ ($current ?? 'clients') === 'clients' ? 'text-white' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}"
       @if(($current ?? 'clients') === 'clients') style="background: {{ $themeColor }};" @endif>عملاء + صفقات</a>
    <a href="{{ route('crm.pipeline.index', array_merge($query, ['view' => 'deals'])) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal {{ ($current ?? '') === 'deals' ? 'text-white' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}"
       @if(($current ?? '') === 'deals') style="background: {{ $themeColor }};" @endif>صفقات فقط</a>
    <a href="{{ route('crm.pipeline.index', array_merge($query, ['view' => 'list'])) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal {{ ($current ?? '') === 'list' ? 'text-white' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}"
       @if(($current ?? '') === 'list') style="background: {{ $themeColor }};" @endif>جدول</a>
    <a href="{{ route('crm.clients.create') }}" class="mr-auto px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal border border-dashed border-gray-300 text-gray-600 hover:bg-gray-50">+ عميل جديد</a>
</div>
