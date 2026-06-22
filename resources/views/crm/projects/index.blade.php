@extends('layouts.app')
@section('page-title', 'المشاريع العقارية')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
@include('projects.partials.route-prefix')

@include('crm.partials.page-header', [
    'title' => 'المشاريع العقارية',
    'subtitle' => 'كل المشاريع المتاحة لفريق المبيعات — إضافة وتعديل حسب الصلاحيات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
    'actionUrl' => auth()->user()?->can('create', \App\Models\Project::class) ? $pr('create') : null,
    'actionLabel' => ($requiresApproval ?? false) ? 'طلب مشروع جديد' : 'مشروع جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])

@if($requiresApproval ?? false)
<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900 font-tajawal">
    إضافة أو تعديل أو حذف المشاريع يمرّ بموافقة الإدارة. تتبع طلباتك من <a href="{{ route('crm.projects.approvals.index') }}" class="font-bold underline">طلباتي — المشاريع</a>.
</div>
@endif

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4">
    @include('crm.partials.stat-card', ['label' => 'المشاريع', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />', 'href' => $pr('index') . '#page-data', 'linkLabel' => 'عرض المشاريع'])
    @include('crm.partials.stat-card', ['label' => 'متاح للبيع', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />', 'href' => $pr('index', ['listing_status' => 'active']) . '#page-data', 'linkLabel' => 'عرض المتاح'])
    @include('crm.partials.stat-card', ['label' => 'قريباً', 'value' => $stats['upcoming'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => $pr('index', ['listing_status' => 'upcoming']) . '#page-data', 'linkLabel' => 'عرض قريباً'])
    @include('crm.partials.stat-card', ['label' => 'وحدات متاحة', 'value' => number_format($stats['available_units']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7" />', 'href' => $pr('index', ['listing_status' => 'active']) . '#page-data', 'linkLabel' => 'عرض الوحدات'])
</div>
@if(!empty($stats['ownership']))
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    @foreach($stats['ownership'] as $row)
    <a href="{{ $pr('index', ['inventory_source' => $row['key']]) }}#page-data"
       class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between font-tajawal hover:shadow-md transition-shadow">
        <div>
            <p class="text-xs text-gray-500">{{ $row['label'] }}</p>
            <p class="text-xl font-bold text-gray-900">{{ $row['count'] }} <span class="text-sm font-normal text-gray-400">مشروع</span></p>
        </div>
        <span class="text-xs text-gray-400">{{ number_format($row['units']) }} وحدة</span>
    </a>
    @endforeach
</div>
@endif

@include('crm.partials.filter-bar', ['projectsRoutePrefix' => $projectsRoutePrefix ?? 'crm.projects', 'projectsExportRoute' => $projectsExportRoute ?? null])

<div id="page-data" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
    @forelse($projects as $project)
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-200 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 flex flex-col h-full group">
        <div class="flex items-start justify-between gap-2 mb-3">
            <a href="{{ $pr('show', $project) }}" class="font-bold text-lg text-gray-900 font-tajawal group-hover:opacity-90 hover:underline line-clamp-2">{{ $project->name }}</a>
            <div class="flex flex-col items-end gap-1 shrink-0">
                @include('projects.partials.listing-badge', ['status' => $project->listing_status])
                @include('projects.partials.ownership-badge', ['type' => $project->ownership_type])
                <span class="text-[10px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-700 font-tajawal">{{ $project->inventorySourceLabel() }}</span>
            </div>
        </div>
        @if($project->requiresRegisteredDeveloper() && $project->displayDeveloperName() !== '—')
        <p class="text-xs text-emerald-700 font-tajawal mb-1">{{ $project->displayDeveloperName() }}</p>
        @endif
        <p class="text-sm text-gray-500 font-tajawal">{{ $project->city }} @if($project->location)— {{ $project->location }}@endif</p>
        <p class="text-xs text-gray-400 mt-1 font-tajawal">{{ $project->property_type_name }}</p>
        <p class="font-bold mt-3 text-lg font-tajawal" style="color: {{ $themeColor }};">
            {{ \App\Helpers\SettingsHelper::formatMoney($project->price_from) }}
            @if($project->price_to) — {{ \App\Helpers\SettingsHelper::formatMoney($project->price_to) }}@endif
        </p>
        <p class="text-xs text-gray-400 mt-2 font-tajawal">{{ $project->available_units }} متاح من {{ $project->total_units }} · {{ $project->map_pins_count ?? 0 }} علامة خريطة</p>
        @if($project->total_units > 0)
        <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full" style="width: {{ $project->occupancy_percent }}%; background: {{ $themeColor }};"></div>
        </div>
        @endif

        <div class="flex flex-wrap gap-2 mt-auto pt-4 border-t border-gray-100">
            <a href="{{ $pr('show', $project) }}"
               class="flex-1 min-w-[4.5rem] text-center py-2 rounded-lg text-xs font-bold text-white font-tajawal hover:opacity-90"
               style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">عرض</a>
            @can('update', $project)
            <a href="{{ $pr('edit', $project) }}"
               class="px-3 py-2 rounded-lg text-xs font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 font-tajawal">تعديل</a>
            @endcan
            <a href="{{ route('crm.pipeline.create', ['project_id' => $project->id]) }}"
               class="px-3 py-2 rounded-lg text-xs font-semibold font-tajawal hover:opacity-90"
               style="background: {{ $themeColor }}12; color: {{ $themeColor }};">+ صفقة</a>
            @can('delete', $project)
            <form action="{{ $pr('destroy', $project) }}" method="POST"
                  onsubmit="return confirm('حذف المشروع «{{ $project->name }}»؟ لا يمكن التراجع.')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-2 rounded-lg text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 font-tajawal">حذف</button>
            </form>
            @endcan
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-400 font-tajawal">
        <p class="mb-4">لا توجد مشاريع عقارية</p>
        @can('create', \App\Models\Project::class)
        <a href="{{ $pr('create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
           style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">إضافة أول مشروع</a>
        @endcan
    </div>
    @endforelse
</div>
@if($projects->hasPages())
<div class="mt-6">{{ $projects->links() }}</div>
@endif
@endsection
