@php
    $source = \App\Models\Client::normalizeLeadSource($source ?? ($client->lead_source ?? null));
    $label = $source ? (\App\Models\Client::leadSourceLabels()[$source] ?? $source) : null;
    $colors = [
        'personal' => 'bg-slate-100 text-slate-700',
        'referral' => 'bg-blue-100 text-blue-800',
        'event' => 'bg-purple-100 text-purple-800',
        'marketing' => 'bg-amber-100 text-amber-800',
        'paid_ad' => 'bg-rose-100 text-rose-800',
    ];
    $class = $colors[$source] ?? 'bg-gray-100 text-gray-700';
@endphp
@if($label)
<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal {{ $class }}">{{ $label }}</span>
@else
<span class="text-gray-400 text-xs font-tajawal">—</span>
@endif
