@php
    use App\Services\CrmScopeService;
    $stage = $stage ?? ($client->lead_stage ?? null);
    $labels = CrmScopeService::leadStageLabels();
    $colors = CrmScopeService::clientLeadStageColors();
    $label = $labels[$stage] ?? $stage;
    $color = $colors[$stage] ?? ['bg' => '#6b7280', 'light' => '#f3f4f6'];
@endphp
@if($stage && $label)
<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal"
      style="background: {{ $color['light'] }}; color: {{ $color['bg'] }};">
    {{ $label }}
</span>
@else
<span class="text-gray-400 text-xs font-tajawal">—</span>
@endif
