@php
    $lines = $client->leadSourceDetailLines();
@endphp
@if($lines !== [])
<div class="sm:col-span-2 mt-1 space-y-1">
    @foreach($lines as $line)
    <p class="text-sm text-gray-700 font-tajawal">
        <span class="text-xs font-bold text-gray-500">{{ $line['label'] }}:</span>
        <span class="font-semibold">{{ $line['value'] }}</span>
    </p>
    @endforeach
</div>
@endif
