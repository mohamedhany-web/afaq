@php
    use App\Services\CrmScopeService;
    $creatorName = CrmScopeService::creatorDisplayName($client);
    $isAdmin = CrmScopeService::creatorIsAdmin($client);
@endphp
<span class="inline-flex items-center gap-1 text-xs font-tajawal {{ $class ?? 'text-gray-500' }}">
    <svg class="w-3.5 h-3.5 shrink-0 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
    </svg>
    <span>أضافه: <strong class="font-semibold {{ $isAdmin ? 'text-amber-700' : 'text-gray-700' }}">{{ $creatorName }}</strong></span>
    @if($isAdmin)
    <span class="px-1.5 py-px rounded text-[10px] font-bold bg-amber-100 text-amber-800">إدارة</span>
    @endif
</span>
