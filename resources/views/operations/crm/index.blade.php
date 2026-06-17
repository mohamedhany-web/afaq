@extends('layouts.app')
@section('page-title', 'متابعة CRM')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'متابعة CRM',
    'subtitle' => 'جودة البيانات — مراحل البيع — العملاء المتعثرون',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>',
    'actionUrl' => auth()->user()?->can('create', \App\Models\Client::class) ? route('crm.clients.create') : null,
    'actionLabel' => 'عميل جديد',
    'secondaryUrl' => auth()->user()->clientsHubUrl(),
    'secondaryLabel' => 'كل العملاء',
])

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي العملاء', 'value' => $stats['total_clients'], 'accent' => 'theme', 'href' => auth()->user()->clientsHubUrl(), 'linkLabel' => 'عرض العملاء'])
    @include('crm.partials.stat-card', ['label' => 'صفقات نشطة', 'value' => $stats['active_deals'], 'accent' => 'blue', 'href' => route('crm.pipeline.index', ['view' => 'deals']), 'linkLabel' => 'عرض الصفقات'])
    @include('crm.partials.stat-card', ['label' => 'إغلاقات الشهر', 'value' => $stats['won_month'], 'accent' => 'green', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won']), 'linkLabel' => 'عرض الإغلاقات'])
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
    @if($crmKpis) @include('operations.partials.kpi-group', ['group' => $crmKpis, 'link' => route('operations.crm.index') . '#page-data']) @endif
    @if($salesKpis) @include('operations.partials.kpi-group', ['group' => $salesKpis, 'link' => route('crm.pipeline.index')]) @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 font-tajawal" id="page-data">
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold">الـ Pipeline</div>
        <div class="p-4 space-y-2">
            @php $stageLabels = ['lead'=>'عميل جديد','prospect'=>'تم التواصل','proposal'=>'اجتماع','negotiation'=>'تفاوض','closed_won'=>'مغلق — ربح','closed_lost'=>'مغلق — خسارة']; @endphp
            @foreach($pipeline as $stage => $row)
            <a href="{{ route('crm.pipeline.index', ['view' => 'deals', 'stage' => $stage]) }}" class="flex justify-between items-center p-2 rounded-lg bg-gray-50 text-sm hover:bg-gray-100 transition-colors">
                <span>{{ $stageLabels[$stage] ?? $stage }}</span>
                <span class="font-bold">{{ $row->cnt }} <span class="text-gray-400 font-normal">({{ number_format($row->val) }})</span></span>
            </a>
            @endforeach
        </div>
    </div>
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold text-amber-800">عملاء متعثرون (+5 أيام)</div>
        <ul class="divide-y">
            @forelse($staleClients as $client)
            <li class="p-4 text-sm">
                <a href="{{ $client->profileUrl() }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $client->name }}</a>
                <p class="text-xs text-gray-500">{{ $client->assignedEmployee ? trim($client->assignedEmployee->first_name.' '.$client->assignedEmployee->last_name) : 'غير معيّن' }} — {{ $client->updated_at->diffForHumans() }}</p>
            </li>
            @empty
            <li class="p-6 text-center text-gray-500 text-sm">لا يوجد</li>
            @endforelse
        </ul>
    </div>
    <div class="lg:col-span-2 bg-white rounded-2xl border overflow-hidden" id="missed-reminders">
        <div class="px-5 py-4 border-b font-bold text-red-700">متابعات فائتة</div>
        <ul class="divide-y">
            @forelse($overdueFollowUps as $fu)
            <li class="p-4 text-sm flex justify-between gap-3">
                <div>
                    @if($fu->client)
                    <a href="{{ $fu->client->profileUrl() }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $fu->client->name }}</a>
                    @else
                    <p class="font-semibold">—</p>
                    @endif
                    <p class="text-xs text-gray-500">{{ $fu->user?->name }} — {{ $fu->scheduled_at?->format('Y-m-d H:i') }}</p>
                </div>
            </li>
            @empty
            <li class="p-6 text-center text-gray-500 text-sm">لا توجد متابعات فائتة</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
