{{-- التسويق — موظف تسويق --}}
@canNav('view-marketing', 'view-dashboard')
<a href="{{ route('dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('dashboard') || request()->routeIs('marketing.dashboard') ? 'active' : '' }}">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
    لوحتي
</a>
@endcanNav
@canNav('view-marketing')
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">مهامي</h3>
    <a href="{{ route('marketing.campaigns.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.campaigns.*') ? 'active' : '' }}">الحملات</a>
    <a href="{{ route('marketing.plans.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.plans.*') ? 'active' : '' }}">خطة الشهر</a>
    <a href="{{ route('marketing.activities.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.activities.*') ? 'active' : '' }}">مهامي</a>
    @canNav('view-clients')
    <a href="{{ route('marketing.leads.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.leads.*') ? 'active' : '' }}">عملائي المحتملون</a>
    @endcanNav
    @canNav('create-clients', 'create-marketing')
    <a href="{{ route('marketing.leads.create') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium">إضافة Lead</a>
    @endcanNav
    @canNav('generate-reports', 'view-reports')
    <a href="{{ route('marketing.reports.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.reports.*') ? 'active' : '' }}">تقريري اليومي</a>
    @endcanNav
    @include('layouts.partials.sidebar-leaves-link')
</div>
@endcanNav
