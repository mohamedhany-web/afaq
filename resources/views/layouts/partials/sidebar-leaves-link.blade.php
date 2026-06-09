@can('view-leaves')
<a href="{{ route('leaves.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    {{ $label ?? 'إجازاتي' }}
</a>
@endcan
