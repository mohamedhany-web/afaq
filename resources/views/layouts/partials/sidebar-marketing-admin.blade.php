{{-- التسويق — إدارة عامة --}}
<a href="{{ route('marketing.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.dashboard') ? 'active' : '' }}">لوحة التسويق</a>
<a href="{{ route('marketing.campaigns.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.campaigns.*') ? 'active' : '' }}">الحملات</a>
<a href="{{ route('marketing.activities.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.activities.*') ? 'active' : '' }}">الجدول الدوري</a>
<a href="{{ route('marketing.leads.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.leads.*') ? 'active' : '' }}">العملاء المحتملون</a>
<a href="{{ route('marketing.reports.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.reports.*') ? 'active' : '' }}">التقارير الدورية</a>
<a href="{{ route('marketing.analytics.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.analytics.*') ? 'active' : '' }}">تحليلات الأداء</a>
<a href="{{ route('marketing.team.index') }}" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('marketing.team.*') ? 'active' : '' }}">فريق التسويق</a>
