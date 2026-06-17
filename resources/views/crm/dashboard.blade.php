@extends('layouts.app')
@section('page-title', 'لوحة التحكم')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $k = $kpis;
    $isManagerView = $isManager && $user->usesCrmWorkspace();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

@include('crm.partials.page-header', [
    'title' => $isManagerView ? 'لوحة مدير المبيعات' : ($isSuperAdmin ? 'لوحة التحكم' : 'لوحة CRM العقاري'),
    'subtitle' => $role . ' — ' . now()->locale('ar')->translatedFormat('l، d F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />',
    'actionUrl' => route('crm.clients.create'),
    'actionLabel' => 'عميل جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])

<div class="flex flex-wrap items-center gap-3 mb-6 font-tajawal">
    @include('partials.ui-compact-toggle', ['themeColor' => $themeColor])
</div>

@if(!empty($portalPulse))
<div class="mb-6 font-tajawal">
    <h2 class="text-sm font-bold text-gray-800 mb-2">بوابة العملاء — متابعة الإدارة</h2>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @include('crm.partials.stat-card', ['label' => 'عملاء لديهم بوابة', 'value' => $portalPulse['clients_with_portal'], 'accent' => 'theme', 'href' => route('client-accounts.index'), 'linkLabel' => 'الحسابات'])
        @include('crm.partials.stat-card', ['label' => 'اجتماعات معلّقة', 'value' => $portalPulse['pending_meetings'], 'accent' => 'purple', 'href' => route('client-meeting-requests.index'), 'linkLabel' => 'عرض'])
        @include('crm.partials.stat-card', ['label' => 'بلاغات مفتوحة', 'value' => $portalPulse['open_issues'], 'accent' => 'amber', 'href' => route('client-website-issues.index'), 'linkLabel' => 'عرض'])
        @include('crm.partials.stat-card', ['label' => 'تذاكر مفتوحة', 'value' => $portalPulse['open_tickets'], 'accent' => 'blue', 'href' => route('tickets.index'), 'linkLabel' => 'عرض'])
    </div>
</div>
@endif

{{-- KPIs — نفس أسلوب البطاقات السابق مع أحجام موحّدة --}}
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 mb-6 items-stretch">
    @include('crm.partials.stat-card', ['label' => 'إجمالي العملاء', 'value' => number_format($k['total_leads']), 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />', 'href' => auth()->user()->clientsHubUrl(), 'linkLabel' => 'عرض العملاء'])
    @include('crm.partials.stat-card', ['label' => 'جدد اليوم', 'value' => $k['new_leads_today'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />', 'href' => auth()->user()->clientsHubUrl(), 'linkLabel' => 'عرض العملاء'])
    @include('crm.partials.stat-card', ['label' => 'فرص نشطة', 'value' => $k['active_opportunities'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />', 'href' => route('crm.pipeline.index', ['view' => 'deals']), 'linkLabel' => 'عرض الصفقات'])
    @include('crm.partials.stat-card', ['label' => 'صفقات الشهر', 'value' => $k['closed_deals_month'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won']), 'linkLabel' => 'عرض الصفقات'])
    @include('crm.partials.stat-card', ['label' => 'إيرادات الشهر', 'value' => $money($k['total_revenue']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won']), 'linkLabel' => 'عرض الإيرادات'])
</div>

@if(!$isRepOnly)
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 mb-6 items-stretch ui-compact-hidden">
    @include('crm.partials.stat-card', ['label' => 'عملاء مؤهلون', 'value' => $k['qualified_leads'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => auth()->user()->clientsHubUrl(['status' => 'active']), 'linkLabel' => 'عرض العملاء'])
    @include('crm.partials.stat-card', ['label' => 'قيمة المسار', 'value' => $money($k['pipeline_value']), 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />', 'href' => route('crm.pipeline.index', ['view' => 'deals']), 'linkLabel' => 'عرض المسار'])
    @include('crm.partials.stat-card', ['label' => 'معدل التحويل', 'value' => $k['conversion_rate'] . '%', 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />', 'href' => route('crm.intelligence.index'), 'linkLabel' => 'عرض التحليلات'])
    @include('crm.partials.stat-card', ['label' => 'متوسط الصفقة', 'value' => $money($k['avg_deal_value']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2" />', 'href' => route('crm.pipeline.index', ['view' => 'deals']), 'linkLabel' => 'عرض الصفقات'])
    @include('crm.partials.stat-card', ['label' => 'تحقيق الهدف', 'value' => $k['target_achievement'] . '%', 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />', 'href' => route('crm.compensation.dashboard'), 'linkLabel' => 'عرض التعويضات'])
</div>
@endif

@if(!empty($crmPulse))
@include('operations.partials.crm-pulse', ['crmPulse' => $crmPulse])
@endif

@include('crm.partials.reports-hub')

{{-- رسوم بيانية --}}
<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 mb-6">
    <div class="xl:col-span-8 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="{{ $headerStyle }}">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">اتجاه الإيرادات الشهري</h3>
        </div>
        <div class="p-4 sm:p-6 h-56 sm:h-64"><canvas id="revenueTrendChart"></canvas></div>
    </div>
    <div class="xl:col-span-4 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="{{ $headerStyle }}">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">عملاء vs إغلاق</h3>
        </div>
        <div class="p-4 sm:p-6 h-56 sm:h-64"><canvas id="leadsClosedChart"></canvas></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="{{ $headerStyle }}">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">قمع التحويل</h3>
        </div>
        <div class="p-4 sm:p-6 space-y-3">
            @foreach($funnel as $step)
            <div>
                <div class="flex justify-between text-xs sm:text-sm mb-1 font-tajawal">
                    <span class="font-semibold text-gray-800">{{ $step['label'] }}</span>
                    <span class="text-gray-500 tabular-nums">{{ $step['count'] }}</span>
                </div>
                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500" style="width: {{ max($step['percent'], 4) }}%; background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="{{ $headerStyle }}">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">مصادر العملاء</h3>
        </div>
        <div class="p-4 sm:p-6">
            <div class="h-48 sm:h-52 mb-4"><canvas id="leadSourceChart"></canvas></div>
            <div class="grid grid-cols-2 gap-2">
                @foreach($leadSources as $src)
                <div class="flex justify-between text-xs px-3 py-2 rounded-lg bg-gray-50 border border-gray-100 font-tajawal">
                    <span>{{ $src['label'] }}</span>
                    <span class="font-bold" style="color: {{ $themeColor }};">{{ $src['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if($isManager && $teamRanking->isNotEmpty())
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex justify-between items-center" style="{{ $headerStyle }}">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">ترتيب الفريق</h3>
            @if($isManager)<a href="{{ route('crm.teams.index') }}" class="text-sm font-semibold font-tajawal" style="color: {{ $themeColor }};">الفرق</a>@endif
        </div>
        <div class="p-4 sm:p-6 space-y-3">
            @foreach($teamRanking as $i => $rep)
            <a href="{{ route('crm.team-members.show', $rep['id']) }}" class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:shadow-md transition bg-white">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold text-white shrink-0" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm text-gray-900 truncate font-tajawal">{{ $rep['name'] }}</p>
                    <p class="text-xs text-gray-500">{{ $rep['won_count'] }} صفقة · {{ $money($rep['revenue']) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @if($isSuperAdmin && $topTeams->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="{{ $headerStyle }}">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">أفضل فرق المبيعات</h3>
        </div>
        <div class="p-4 sm:p-6 space-y-3">
            @foreach($topTeams as $team)
            <a href="{{ route('crm.teams.show', $team['id']) }}" class="block p-3 rounded-xl border border-gray-200 hover:shadow-md transition">
                <p class="font-bold text-gray-900 font-tajawal">{{ $team['name'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $team['manager'] ?? '—' }} · {{ $money($team['revenue']) }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

{{-- العملاء المحتملون --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @foreach([
        ['title' => 'أحدث العملاء', 'items' => $leadLists['recent']],
        ['title' => 'أولوية عالية', 'items' => $leadLists['high_priority']],
        ['title' => 'غير مُعيَّنين', 'items' => $leadLists['unassigned']],
        ['title' => 'يحتاج متابعة', 'items' => $leadLists['follow_up']],
    ] as $list)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden flex flex-col h-full">
        <div class="px-4 sm:px-5 py-3 border-b border-gray-200 flex justify-between items-center" style="{{ $headerStyle }}">
            <h3 class="font-bold text-sm text-gray-900 font-tajawal">{{ $list['title'] }}</h3>
            <a href="{{ auth()->user()->clientsHubUrl() }}" class="text-[10px] font-semibold" style="color: {{ $themeColor }};">الكل</a>
        </div>
        <ul class="p-3 sm:p-4 space-y-2 flex-1">
            @forelse($list['items'] as $client)
            <li>
                <a href="{{ $client->profileUrl() }}" class="block p-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-sm font-tajawal">
                    <span class="font-semibold text-gray-900">{{ $client->name }}</span>
                    <span class="block text-[10px] text-gray-500 mt-0.5" dir="ltr">{{ $client->phone }}</span>
                </a>
            </li>
            @empty
            <li class="text-xs text-gray-400 py-6 text-center">لا يوجد</li>
            @endforelse
        </ul>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
    {{-- آخر الصفقات --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex justify-between items-center" style="{{ $headerStyle }}">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">آخر الصفقات</h3>
            <a href="{{ route('crm.pipeline.index') }}" class="text-sm font-medium font-tajawal" style="color: {{ $themeColor }};">مسار المبيعات</a>
        </div>
        <div class="overflow-x-auto p-4 sm:p-6">
            <table class="w-full text-sm">
                <thead><tr class="text-gray-500 border-b border-gray-200">
                    <th class="text-right py-2 font-tajawal">العميل</th>
                    <th class="text-right py-2 font-tajawal">المشروع</th>
                    <th class="text-right py-2 font-tajawal">المندوب</th>
                    <th class="text-right py-2 font-tajawal">القيمة</th>
                </tr></thead>
                <tbody>
                @forelse($recentSales as $sale)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('crm.pipeline.show', $sale) }}'">
                        <td class="py-2.5"><a href="{{ $sale->client?->profileUrl() }}" class="font-medium font-tajawal" style="color: {{ $themeColor }};" onclick="event.stopPropagation()">{{ $sale->client?->name }}</a></td>
                        <td class="text-gray-700 font-tajawal" onclick="event.stopPropagation()">
                            @include('crm.partials.entity-link', ['type' => 'project', 'entity' => $sale->project, 'linkClass' => 'hover:underline'])
                        </td>
                        <td class="text-gray-600 font-tajawal" onclick="event.stopPropagation()">
                            @include('crm.partials.entity-link', ['type' => 'rep', 'entity' => $sale->salesRep, 'linkClass' => 'hover:underline'])
                        </td>
                        <td class="font-semibold font-tajawal">{{ $money($sale->estimated_value) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-8 text-center text-gray-400 font-tajawal">لا توجد صفقات</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- مشاريع + مساعد ذكي --}}
    <div class="space-y-4 sm:gap-6">
        @if(!$user->isSalesAgentOnly())
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6">
            <h3 class="font-bold mb-3 text-gray-900 font-tajawal">مشاريع عقارية</h3>
            @forelse($projects as $project)
                <a href="{{ route('crm.projects.show', $project) }}" class="block py-2.5 border-b border-gray-100 last:border-0 hover:opacity-80">
                    <p class="font-medium text-gray-900 font-tajawal text-sm">{{ $project->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $project->city }} — {{ $project->available_units ?? '—' }} وحدة</p>
                </a>
            @empty
                <p class="text-sm text-gray-400 font-tajawal">لا توجد مشاريع</p>
            @endforelse
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="{{ $headerStyle }}">
                <h3 class="font-bold text-base text-gray-900 font-tajawal">توصيات ذكية</h3>
            </div>
            <div class="p-4 sm:p-5 space-y-2 max-h-64 overflow-y-auto">
                @forelse($aiInsights['high_intent'] as $sale)
                <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-100 text-xs font-tajawal">
                    @if($sale->client)
                    <a href="{{ $sale->client->profileUrl() }}" class="font-semibold text-gray-900 hover:underline block">{{ $sale->client->name }}</a>
                    @else
                    <p class="font-semibold text-gray-900">{{ $sale->product_service }}</p>
                    @endif
                    <p class="text-gray-500 mt-0.5">{{ $sale->probability_percentage ?? 0 }}% · {{ $sale->stage }}</p>
                </div>
                @empty
                <p class="text-xs text-gray-400">لا توجد توصيات</p>
                @endforelse
                @foreach($aiInsights['actions'] as $action)
                <p class="text-xs p-2 rounded-lg border border-gray-100 text-gray-600 font-tajawal">← {{ $action }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if($isManager && $managerCenter)
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="{{ $headerStyle }}">
        <h3 class="font-bold text-lg text-gray-900 font-tajawal">مركز مدير المبيعات — نشاط اليوم</h3>
    </div>
    <div class="p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div>
            <p class="text-sm text-gray-600 mb-2 font-tajawal">هدف الفريق</p>
            <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden mb-2">
                <div class="h-full rounded-full" style="width: {{ $managerCenter['team_progress_pct'] }}%; background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);"></div>
            </div>
            <p class="text-xs text-gray-500">{{ $money($managerCenter['team_progress']) }} / {{ $money($managerCenter['team_target']) }}</p>
        </div>
        @php $act = $managerCenter['daily_activity']; @endphp
        <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-5 gap-2">
            @foreach(['calls' => 'مكالمات', 'meetings' => 'اجتماعات', 'tours' => 'اجتماعات عقارية', 'follow_ups' => 'متابعات', 'negotiations' => 'تفاوض'] as $key => $label)
            <div class="text-center p-2.5 sm:p-3 rounded-xl bg-gray-50 border border-gray-100">
                <p class="text-lg sm:text-xl font-bold text-gray-900 tabular-nums">{{ $act[$key] }}</p>
                <p class="text-[10px] text-gray-500 font-tajawal">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if(!$isRepOnly && !empty($portfolio))
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-2" style="{{ $headerStyle }}">
        <h3 class="font-bold text-lg text-gray-900 font-tajawal">محفظة المشاريع العقارية</h3>
        <a href="{{ route('crm.projects.index') }}" class="text-sm font-semibold font-tajawal" style="color: {{ $themeColor }};">كل المشاريع</a>
    </div>
    <div class="p-4 sm:p-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
            @foreach($portfolio['by_ownership'] as $row)
            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 font-tajawal">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-xs font-bold text-gray-600">{{ $row['label'] }}</p>
                    @include('projects.partials.ownership-badge', ['type' => $row['key']])
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $row['count'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($row['available_units']) }} وحدة متاحة · {{ number_format($row['total_units']) }} إجمالي</p>
            </div>
            @endforeach
        </div>
        @if($portfolio['top_developers']->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <h4 class="text-sm font-bold text-gray-700 mb-2 font-tajawal">أبرز المطورين</h4>
                <div class="space-y-2">
                    @foreach($portfolio['top_developers'] as $dev)
                    <div class="flex justify-between p-2.5 rounded-lg bg-gray-50 text-sm font-tajawal">
                        <span class="font-semibold">{{ $dev['name'] }}</span>
                        <span class="text-gray-500">{{ $dev['projects_count'] }} مشروع</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <h4 class="text-sm font-bold text-gray-700 mb-2 font-tajawal">آخر مشاريع المطورين</h4>
                <div class="space-y-2">
                    @forelse($portfolio['recent_by_ownership']['developer'] ?? [] as $p)
                    <a href="{{ route('crm.projects.show', $p) }}" class="block p-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-sm font-tajawal">
                        <span class="font-semibold">{{ $p->name }}</span>
                        <span class="block text-xs text-gray-500">{{ $p->city }} · {{ $p->available_units }} وحدة</span>
                    </a>
                    @empty
                    <p class="text-xs text-gray-400">لا يوجد</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 border-b border-gray-200" style="{{ $headerStyle }}"><h3 class="font-bold text-base font-tajawal">مركز النشاط</h3></div>
        <ul class="p-4 space-y-2 max-h-72 overflow-y-auto">
            @foreach($activities as $item)
            <li class="p-2.5 rounded-lg bg-gray-50 text-xs font-tajawal">
                <p class="font-semibold text-gray-900 truncate">{{ $item['title'] }}</p>
                <p class="text-gray-500 mt-0.5">{{ $item['time']->diffForHumans() }}</p>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 border-b border-gray-200" style="{{ $headerStyle }}"><h3 class="font-bold text-base font-tajawal">تحليل جغرافي</h3></div>
        <div class="p-4 space-y-2">
            @foreach($geo['hotspots'] as $spot)
            <div class="flex justify-between p-2.5 rounded-lg bg-gray-50 text-sm font-tajawal">
                <span>{{ $spot['city'] }}</span>
                <span class="font-bold" style="color: {{ $themeColor }};">{{ $spot['score'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 border-b border-gray-200" style="{{ $headerStyle }}"><h3 class="font-bold text-base font-tajawal">المالية</h3></div>
        <div class="p-4 sm:p-5">
            <p class="text-xl sm:text-2xl font-bold font-tajawal" style="color: {{ $themeColor }};">{{ $money($financial['monthly_revenue']) }}</p>
            <p class="text-xs text-gray-500 mb-3">إيرادات الشهر</p>
            <dl class="space-y-1.5 text-xs font-tajawal text-gray-600">
                <div class="flex justify-between"><dt>عمولات</dt><dd class="font-bold">{{ $money($financial['commission_estimate']) }}</dd></div>
                <div class="flex justify-between"><dt>مستحقات</dt><dd class="font-bold">{{ $money($financial['outstanding']) }}</dd></div>
            </dl>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="{{ $headerStyle }}">
        <h3 class="font-bold text-lg text-gray-900 font-tajawal">التقويم والمهام</h3>
    </div>
    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach([
            ['title' => 'اجتماعات', 'items' => $calendar['meetings'], 'date' => 'viewing_date'],
            ['title' => 'متابعات', 'items' => $calendar['follow_ups'], 'date' => 'expected_close_date'],
            ['title' => 'تفاوض', 'items' => $calendar['deadlines'], 'date' => 'expected_close_date'],
        ] as $col)
        <div>
            <h4 class="text-sm font-bold text-gray-700 mb-2 font-tajawal">{{ $col['title'] }}</h4>
            @forelse($col['items'] as $item)
            <div class="p-2.5 mb-2 rounded-lg bg-gray-50 border border-gray-100 text-xs font-tajawal">
                <p class="font-semibold">{{ $item->client?->name ?? $item->product_service }}</p>
                <p class="text-gray-500">{{ $item->{$col['date']}?->format('Y/m/d') }}</p>
            </div>
            @empty
            <p class="text-xs text-gray-400">لا يوجد</p>
            @endforelse
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const theme = @json($themeColor);
    const chartFont = { family: 'Tajawal', size: 11 };
    const revenue = @json($chartPayload['revenue']);
    const leadsClosed = @json($chartPayload['leadsClosed']);
    const sources = @json($chartPayload['sources']);

    const baseOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { labels: { font: chartFont } } },
        scales: {
            x: { ticks: { font: chartFont }, grid: { display: false } },
            y: { ticks: { font: chartFont }, grid: { color: 'rgba(0,0,0,0.06)' } },
        },
    };

    const revEl = document.getElementById('revenueTrendChart');
    if (revEl && revenue.length) {
        new Chart(revEl, {
            type: 'line',
            data: {
                labels: revenue.map(r => r.label),
                datasets: [{
                    data: revenue.map(r => r.value),
                    borderColor: theme,
                    backgroundColor: theme + '22',
                    fill: true,
                    tension: 0.4,
                }],
            },
            options: { ...baseOpts, plugins: { legend: { display: false } } },
        });
    }

    const lcEl = document.getElementById('leadsClosedChart');
    if (lcEl && leadsClosed.length) {
        new Chart(lcEl, {
            type: 'bar',
            data: {
                labels: leadsClosed.map(r => r.label),
                datasets: [
                    { label: 'عملاء', data: leadsClosed.map(r => r.leads), backgroundColor: theme },
                    { label: 'إغلاق', data: leadsClosed.map(r => r.closed), backgroundColor: theme + '99' },
                ],
            },
            options: baseOpts,
        });
    }

    const srcEl = document.getElementById('leadSourceChart');
    if (srcEl && sources.length) {
        new Chart(srcEl, {
            type: 'doughnut',
            data: {
                labels: sources.map(s => s.label),
                datasets: [{ data: sources.map(s => s.count), backgroundColor: [theme, theme + 'cc', theme + '99', theme + '66', '#94a3b8', '#cbd5e1'] }],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: chartFont } } } },
        });
    }
});
</script>
@endpush
