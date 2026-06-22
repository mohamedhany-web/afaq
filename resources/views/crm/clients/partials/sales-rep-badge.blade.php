@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $repName = $client->assignedSalesRepName();
    $employee = $client->assignedEmployee;
    $userId = $employee?->user_id;
@endphp
@if($repName)
<a href="{{ route('crm.clients.index', array_merge(request()->except('page'), ['sales_rep' => $userId])) }}#page-data"
   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold font-tajawal hover:opacity-90 transition-opacity"
   style="background: {{ $themeColor }}15; color: {{ $themeColor }};"
   title="عرض كل عملاء {{ $repName }}">
    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    {{ $repName }}
</a>
@else
<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal bg-amber-50 text-amber-800 border border-amber-200">غير مُعيَّن</span>
@endif
