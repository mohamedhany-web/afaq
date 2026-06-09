{{-- التسويق — مدير تسويق --}}
<a href="{{ route('dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('dashboard') || request()->routeIs('marketing.dashboard') ? 'active' : '' }}">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
    لوحة التسويق
</a>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">إدارة التسويق</h3>
    <a href="{{ route('marketing.campaigns.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.campaigns.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 4h10m-10 0a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V10a2 2 0 00-2-2"/></svg>
        الحملات
    </a>
    <a href="{{ route('marketing.plans.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.plans.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        خطة التسويق الشهرية
    </a>
    <a href="{{ route('marketing.activities.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.activities.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        مهام الفريق
    </a>
    <a href="{{ route('marketing.leads.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.leads.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        العملاء المحتملون
    </a>
    <a href="{{ route('marketing.reports.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.reports.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        التقارير الدورية
    </a>
    <a href="{{ route('marketing.analytics.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.analytics.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        تحليلات الأداء
    </a>
    <a href="{{ route('marketing.team.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.team.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        فريق التسويق
    </a>
    @include('layouts.partials.sidebar-leaves-link', ['label' => 'إجازات الفريق'])
</div>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">الحساب</h3>
    <a href="{{ route('notifications.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        الإشعارات
    </a>
</div>
