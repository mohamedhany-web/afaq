@extends('layouts.app')
@section('page-title', $client->name)

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $stageLabels = [
        'lead' => 'عميل محتمل',
        'prospect' => 'مهتم',
        'proposal' => 'عرض سعر',
        'negotiation' => 'تفاوض',
        'closed_won' => 'تم البيع',
        'closed_lost' => 'خسارة',
    ];
    $dealsCount = $client->sales->count();
    $dealsValue = $client->sales->sum('estimated_value');
    $canDelete = auth()->user()?->can('delete', $client);
@endphp

@include('crm.partials.page-header', [
    'title' => $client->name,
    'subtitle' => 'ملف العميل — ' . $client->typeLabel(),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'secondaryUrl' => auth()->user()?->can('update', $client) ? route('crm.clients.edit', $client) : null,
    'secondaryLabel' => 'تعديل البيانات',
    'secondaryIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
    'actionUrl' => route('crm.pipeline.create', ['client_id' => $client->id]),
    'actionLabel' => 'صفقة جديدة',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'الصفقات', 'value' => $dealsCount, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />', 'href' => route('crm.pipeline.client', $client), 'linkLabel' => 'عرض الصفقات'])
    @include('crm.partials.stat-card', ['label' => 'قيمة الصفقات', 'value' => $money($dealsValue), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />', 'href' => route('crm.pipeline.client', $client), 'linkLabel' => 'عرض الصفقات'])
    @include('crm.partials.stat-card', ['label' => 'الحالة', 'value' => match($client->status) { 'prospect' => 'محتمل', 'active' => 'نشط', 'inactive' => 'غير نشط', 'suspended' => 'موقوف', default => $client->status }, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => '#client-details', 'linkLabel' => 'عرض التفاصيل'])
    @include('crm.partials.stat-card', ['label' => 'تاريخ التسجيل', 'value' => $client->created_at->format('Y/m/d'), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />', 'href' => '#client-details', 'linkLabel' => 'عرض التفاصيل'])
</div>

@if($relatedProjects->isNotEmpty())
<div class="mb-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        المشاريع المرتبطة
    </div>
    <div class="p-5 sm:p-6 flex flex-wrap gap-2">
        @foreach($relatedProjects as $project)
        <a href="{{ route('crm.projects.show', $project) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 hover:border-gray-300 transition-all"
           style="background: {{ $themeColor }}08; color: {{ $themeColor }};">
            {{ $project->name }}
            @if($project->city)<span class="text-xs text-gray-500 mr-2">· {{ $project->city }}</span>@endif
        </a>
        @endforeach
    </div>
</div>
@endif

@include('crm.clients.partials.portal-hub', ['client' => $client, 'portalHub' => $portalHub ?? [], 'themeColor' => $themeColor])

@include('crm.clients.partials.journey-kanban', compact('client', 'stageLabels', 'themeColor'))
@include('crm.clients.partials.unified-timeline', compact('client', 'timeline', 'themeColor'))

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 w-full">
    {{-- بيانات العميل --}}
    <div id="client-details" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
             style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            بيانات التواصل
        </div>
        <div class="p-5 sm:p-6 space-y-4">
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">الهاتف</dt>
                <dd class="font-medium text-gray-900 font-tajawal" dir="ltr">{{ $client->phone }}</dd>
            </div>
            @if($client->id_number)
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">رقم البطاقة</dt>
                <dd class="font-medium text-gray-900 font-tajawal" dir="ltr">{{ $client->id_number }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">البريد الإلكتروني</dt>
                <dd class="text-gray-900 font-tajawal" dir="ltr">{{ $client->email ?? '—' }}</dd>
            </div>
            @if($client->company_name)
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">الشركة</dt>
                <dd class="text-gray-900 font-tajawal">{{ $client->company_name }}</dd>
            </div>
            @endif
            @if($client->address)
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">العنوان</dt>
                <dd class="text-gray-900 font-tajawal">{{ $client->address }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">تصنيف العميل</dt>
                <dd>@include('crm.clients.partials.type-badge', ['type' => $client->client_type])</dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">مصدر العميل</dt>
                <dd>@include('crm.clients.partials.source-badge', ['source' => $client->lead_source])</dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">الحالة</dt>
                <dd>@include('crm.clients.partials.status-badge', ['status' => $client->status])</dd>
            </div>
            @if($client->assignedEmployee)
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">مسؤول المبيعات</dt>
                <dd class="text-gray-900 font-tajawal">
                    @if($client->assignedEmployee->user)
                        <a href="{{ route('crm.team-members.show', $client->assignedEmployee->user) }}" class="font-semibold hover:underline" style="color: {{ $themeColor }};">
                            {{ trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name) }}
                        </a>
                    @else
                        {{ trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name) }}
                    @endif
                </dd>
            </div>
            @endif
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">من أضاف العميل</dt>
                <dd>@include('crm.clients.partials.created-by', ['client' => $client])</dd>
            </div>
            @if($client->notes)
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">ملاحظات</dt>
                <dd class="text-gray-700 text-sm font-tajawal whitespace-pre-line">{{ $client->notes }}</dd>
            </div>
            @endif
        </div>
        @if(!empty($pendingChange))
        <div class="mx-5 sm:mx-6 mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm font-tajawal">
            يوجد طلب <strong>{{ $pendingChange->actionLabel() }}</strong> بانتظار موافقة الإدارة.
            <a href="{{ route('crm.clients.approvals.show', $pendingChange) }}" class="font-bold mr-1" style="color:{{ $themeColor }}">عرض الطلب</a>
        </div>
        @endif
        <div class="px-5 sm:px-6 py-4 border-t border-gray-100 flex flex-wrap gap-2">
            @can('update', $client)
            @if(empty($pendingChange))
            <a href="{{ route('crm.clients.edit', $client) }}" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal text-white"
               style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">{{ ($requiresApproval ?? false) ? 'طلب تعديل' : 'تعديل البيانات' }}</a>
            @endif
            @endcan
            <a href="{{ route('crm.pipeline.client', $client) }}" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal border-2 font-medium"
               style="border-color: {{ $themeColor }}40; color: {{ $themeColor }};">فتح في المسار</a>
            <a href="{{ route('crm.pipeline.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal border-2 border-gray-200 text-gray-600 hover:bg-gray-50">قائمة المسار</a>
            <a href="{{ route('crm.clients.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal border-2 border-gray-200 text-gray-600 hover:bg-gray-50">كل العملاء</a>
            @can('delete', $client)
            @if(empty($pendingChange))
            <div class="w-full sm:w-auto sm:min-w-[220px]">
                @if($requiresApproval ?? false)
                    @include('crm.partials.delete-request-form', [
                        'action' => route('crm.clients.destroy', $client),
                        'label' => 'طلب حذف العميل',
                    ])
                @else
                <form action="{{ route('crm.clients.destroy', $client) }}" method="POST"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 rounded-xl text-sm font-semibold font-tajawal bg-red-50 text-red-600 hover:bg-red-100">حذف العميل</button>
                </form>
                @endif
            </div>
            @endif
            @endcan
        </div>
    </div>

    {{-- الصفقات --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
             style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            <h3 class="font-bold text-gray-900 font-tajawal">صفقات العميل</h3>
            <a href="{{ route('crm.pipeline.create', ['client_id' => $client->id]) }}" class="text-xs font-semibold font-tajawal px-3 py-1.5 rounded-lg"
               style="background: {{ $themeColor }}15; color: {{ $themeColor }};">+ صفقة جديدة</a>
        </div>
        <div class="p-5 sm:p-6">
            @forelse($client->sales as $sale)
                <a href="{{ route('crm.pipeline.show', $sale) }}" class="block p-4 mb-3 last:mb-0 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-gray-50/80 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900 font-tajawal truncate">{{ $sale->product_service }}</div>
                            @if($sale->project)
                                <div class="text-xs text-gray-500 mt-1 font-tajawal">
                                    <a href="{{ route('crm.projects.show', $sale->project) }}" class="hover:underline" style="color: {{ $themeColor }};">{{ $sale->project->name }}</a>
                                    @if($sale->salesRep)
                                        <span class="text-gray-300 mx-1">·</span>
                                        <a href="{{ route('crm.team-members.show', $sale->salesRep) }}" class="hover:underline">{{ $sale->salesRep->name }}</a>
                                    @endif
                                </div>
                            @elseif($sale->salesRep)
                                <div class="text-xs text-gray-500 mt-1 font-tajawal">
                                    <a href="{{ route('crm.team-members.show', $sale->salesRep) }}" class="hover:underline">{{ $sale->salesRep->name }}</a>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal bg-gray-100 text-gray-700">
                                {{ $stageLabels[$sale->stage] ?? $sale->stage }}
                            </span>
                            <span class="font-bold text-sm font-tajawal" style="color: {{ $themeColor }};">{{ $money($sale->estimated_value) }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-10">
                    <p class="text-gray-400 font-tajawal mb-4">لا توجد صفقات لهذا العميل بعد</p>
                    <a href="{{ route('crm.pipeline.create', ['client_id' => $client->id]) }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                       style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                        إنشاء أول صفقة
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@include('crm.partials.lost-reason-modal', ['lostReasons' => $lostReasons ?? config('crm_intelligence.lost_reasons')])
@endsection
