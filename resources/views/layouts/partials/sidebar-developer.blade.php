@php
    $devAccount = auth('developer')->user();
    $canProjects = $devAccount?->canManageProjects();
    $canPortfolio = $devAccount?->canManagePortfolio();
@endphp

<a href="{{ route('developer.dashboard') }}"
   class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('developer.dashboard') ? 'active' : '' }}">
    <svg class="ml-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    لوحة التحكم
</a>

<div class="mt-6">
    <h3 class="sidebar-section-title px-4">المشاريع والمخزون</h3>
    <a href="{{ route('developer.projects.index') }}"
       class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('developer.projects.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        مشاريعي
    </a>
    @if($canProjects)
    <a href="{{ route('developer.projects.create') }}"
       class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('developer.projects.create') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        إضافة مشروع
    </a>
    @endif
</div>

<div class="mt-6">
    <h3 class="sidebar-section-title px-4">الملف التعريفي</h3>
    <a href="{{ route('developer.portfolio.index') }}"
       class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('developer.portfolio.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        سابقة الأعمال
    </a>
    @if($canPortfolio)
    <a href="{{ route('developer.portfolio.create') }}"
       class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('developer.portfolio.create') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة لمشروع سابق
    </a>
    @endif
    <a href="{{ route('developer.profile.edit') }}"
       class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('developer.profile.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0v-2a2 2 0 00-2-2H9a2 2 0 00-2 2v2m14 0H5"/>
        </svg>
        بيانات الشركة
    </a>
</div>
