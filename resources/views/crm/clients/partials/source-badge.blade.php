@php
    $source = \App\Models\Client::normalizeLeadSource($source ?? ($client->lead_source ?? null));
    $label = $source ? (\App\Models\Client::leadSourceLabels()[$source] ?? $source) : null;
    $colors = \App\Models\Client::leadSourceColors();
    $style = $colors[$source] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280'];
@endphp
@if($label)
<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal"
      style="background: {{ $style['bg'] }}; color: {{ $style['text'] }};">{{ $label }}</span>
@else
<span class="text-gray-400 text-xs font-tajawal">—</span>
@endif
