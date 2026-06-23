{{-- Operations — manager workspace --}}
<a href="{{ route('operations.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.dashboard') ? 'active' : '' }}">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    {{ __('operations.sidebar.dashboard') }}
</a>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">{{ __('operations.sidebar.operations_hub') }}</h3>
    @canNav('view-clients')
    <a href="{{ route('operations.clients.index', ['bucket' => 'all']) }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.clients.*') || request()->routeIs('operations.leads.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        {{ __('operations.clients.hub_title') }}
    </a>
    @endcanNav
    @canNav('transfer-clients', 'bulk-update-clients')
    <a href="{{ route('operations.clients.transfer-board') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.clients.transfer-board') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        تحويل وسحب السيلز
    </a>
    @endcanNav
    @canNav('approve-client-changes')
    <a href="{{ route('crm.clients.approvals.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('crm.clients.approvals.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ __('operations.sidebar.client_approvals') }}
    </a>
    @endcanNav
    @canNav('view-analytics', 'view-reports')
    <a href="{{ route('operations.crm.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.crm.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
        {{ __('operations.sidebar.crm_tracking') }}
    </a>
    @endcanNav
    @canNav('view-sales')
    <a href="{{ route('operations.follow-ups.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.follow-ups.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        {{ __('operations.sidebar.follow_ups') }}
    </a>
    @endcanNav
    @canNav('view-all-projects', 'view-own-projects')
    <a href="{{ route('operations.inventory.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.inventory.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        {{ __('operations.sidebar.inventory') }}
    </a>
    @endcanNav
    @canNav('view-employees')
    <a href="{{ route('operations.team.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.team.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        {{ __('operations.sidebar.team_performance') }}
    </a>
    @endcanNav
</div>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">{{ __('operations.sidebar.operations') }}</h3>
    @canNav('view-all-projects', 'view-own-projects')
    <a href="{{ route('operations.projects.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.projects.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        {{ __('operations.sidebar.real_estate_projects') }}
    </a>
    @endcanNav
    @canNav('view-developers', 'manage-developers')
    <a href="{{ route('admin.developers.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.developers.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        {{ __('operations.sidebar.developers') }}
    </a>
    @endcanNav
    @canNav('generate-reports', 'view-reports')
    <a href="{{ route('operations.reports.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.reports.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        {{ __('operations.sidebar.my_periodic_reports') }}
    </a>
    @endcanNav
    @canNav('view-reports')
    <a href="{{ route('admin.system-reports.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('admin.system-reports.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
        {{ __('operations.sidebar.system_reports') }}
    </a>
    @endcanNav
</div>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">{{ __('operations.sidebar.team_resources') }}</h3>
    <a href="{{ route('operations.attendance-reviews.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.attendance-reviews.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        {{ __('operations.sidebar.absence_reviews') }}
    </a>
    <a href="{{ route('operations.checkout-reviews.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.checkout-reviews.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        {{ __('operations.sidebar.checkout_approvals') }}
    </a>
    <a href="{{ route('operations.leaves.index', ['status' => 'pending']) }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.leaves.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        {{ __('operations.sidebar.leave_approvals') }}
    </a>
    <a href="{{ route('operations.exit-permits.index', ['status' => 'pending']) }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('operations.exit-permits.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
        {{ __('operations.sidebar.exit_permit_approvals') }}
    </a>
    @canNav('view-users')
    <a href="{{ route('users.index', ['workspace' => 'operations']) }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('users.*') && request('workspace') === 'operations' ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        {{ __('operations.sidebar.operations_users') }}
    </a>
    @endcanNav
    @canNav('view-attendance')
    <a href="{{ route('attendances.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ __('operations.sidebar.attendance') }}
    </a>
    @endcanNav
    @include('layouts.partials.sidebar-leaves-link', ['label' => __('operations.sidebar.leaves')])
    @include('layouts.partials.sidebar-exit-permit-link', ['label' => __('operations.sidebar.request_exit_permit')])
</div>
@if(auth()->user()?->canAccessCrm())
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">{{ __('operations.sidebar.sales') }}</h3>
    @canNav('access-crm', 'view-sales')
    <a href="{{ route('crm.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('crm.dashboard') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        {{ __('operations.sidebar.sales_dashboard') }}
    </a>
    <a href="{{ route('crm.pipeline.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('crm.pipeline.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
        {{ __('operations.sidebar.sales_pipeline') }}
    </a>
    @endcanNav
</div>
@endif
