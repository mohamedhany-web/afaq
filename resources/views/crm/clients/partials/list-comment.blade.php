@php
    $comment = $client->listLatestComment();
@endphp
@if($comment)
<p class="text-xs text-gray-600 font-tajawal line-clamp-2 max-w-[220px]" title="{{ $comment }}">{{ Str::limit($comment, 90) }}</p>
@else
<span class="text-xs text-gray-300">—</span>
@endif
