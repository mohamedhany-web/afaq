{{-- قسم المحاسبة — روابط النظام المالي --}}
@can('view-finance')
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">المحاسبة والمالية</h3>
    <a href="{{ route('accounting.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('accounting.index') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        لوحة المحاسبة
    </a>
    <a href="{{ route('accounting.accounts') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('accounting.accounts*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        دليل الحسابات
    </a>
    <a href="{{ route('accounting.journal-entries') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('accounting.journal-entries*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        القيود المحاسبية
    </a>
    <a href="{{ route('financial-invoices.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('financial-invoices.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        الفواتير المالية
    </a>
    <a href="{{ route('payments.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('payments.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        المدفوعات
    </a>
    <a href="{{ route('expenses.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        المصروفات
    </a>
    <a href="{{ route('accounting.reports.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('accounting.reports.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
        التقارير المالية
    </a>
    @can('view-invoices')
    <a href="{{ route('invoices.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('invoices.*') && !request()->routeIs('financial-invoices.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        فواتير المبيعات
    </a>
    @endcan
    @can('view-reports')
    <a href="{{ route('crm.compensation.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('crm.compensation.*') ? 'active' : '' }}">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        الرواتب والخصومات
    </a>
    @endcan
</div>
@endcan
