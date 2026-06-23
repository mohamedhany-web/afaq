@php
    $next = $client->listNextAction();
@endphp
@if($next)
<div class="text-xs font-tajawal max-w-[200px]">
    <p class="font-semibold {{ $next['overdue'] ? 'text-red-600' : 'text-gray-800' }}">{{ $next['label'] }}</p>
    <p class="{{ $next['overdue'] ? 'text-red-500' : 'text-gray-500' }} mt-0.5">
        {{ $next['at']->locale('ar')->translatedFormat('d M Y — H:i') }}
        @if($next['overdue'])<span class="font-bold"> · متأخر</span>@endif
    </p>
</div>
@else
<span class="text-xs text-gray-300">—</span>
@endif
