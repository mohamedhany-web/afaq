@php
    $colors = [
        'green' => 'bg-green-100 text-green-800',
        'orange' => 'bg-orange-100 text-orange-800',
        'red' => 'bg-red-100 text-red-800',
        'amber' => 'bg-amber-100 text-amber-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'gray' => 'bg-gray-100 text-gray-600',
    ];
    $class = $colors[$color ?? 'gray'] ?? $colors['gray'];
@endphp
<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $class }}">{{ $label }}</span>
