@extends('layouts.app')
@section('page-title', 'المشاريع العقارية')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'المشاريع العقارية',
    'subtitle' => 'كل المشاريع المتاحة لفريق المبيعات — إضافة وتعديل حسب الصلاحيات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
    'actionUrl' => auth()->user()->can('create-projects') ? route('crm.projects.create') : null,
    'actionLabel' => 'مشروع جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4">
    @include('crm.partials.stat-card', ['label' => 'المشاريع', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />'])
    @include('crm.partials.stat-card', ['label' => 'متاح للبيع', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />'])
    @include('crm.partials.stat-card', ['label' => 'قريباً', 'value' => $stats['upcoming'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'])
    @include('crm.partials.stat-card', ['label' => 'وحدات متاحة', 'value' => number_format($stats['available_units']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7" />'])
</div>
@if(!empty($stats['ownership']))
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    @foreach($stats['ownership'] as $row)
    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between font-tajawal">
        <div>
            <p class="text-xs text-gray-500">{{ $row['label'] }}</p>
            <p class="text-xl font-bold text-gray-900">{{ $row['count'] }} <span class="text-sm font-normal text-gray-400">مشروع</span></p>
        </div>
        <span class="text-xs text-gray-400">{{ number_format($row['units']) }} وحدة</span>
    </div>
    @endforeach
</div>
@endif

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:items-end">
        <div class="lg:col-span-2">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم المشروع، المدينة..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">حالة العرض</label>
            <select name="listing_status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\Project::LISTING_STATUSES as $val => $txt)
                    <option value="{{ $val }}" @selected(request('listing_status') === $val)>{{ $txt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">نوع الملكية</label>
            <select name="ownership_type" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\Project::OWNERSHIP_TYPES as $val => $txt)
                    <option value="{{ $val }}" @selected(request('ownership_type') === $val)>{{ $txt }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
            <button type="submit" class="flex-1 px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                    style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">بحث</button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
    @forelse($projects as $project)
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-200 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 flex flex-col h-full group">
        <div class="flex items-start justify-between gap-2 mb-3">
            <a href="{{ route('crm.projects.show', $project) }}" class="font-bold text-lg text-gray-900 font-tajawal group-hover:opacity-90 hover:underline line-clamp-2">{{ $project->name }}</a>
            <div class="flex flex-col items-end gap-1 shrink-0">
                @include('projects.partials.listing-badge', ['status' => $project->listing_status])
                @include('projects.partials.ownership-badge', ['type' => $project->ownership_type])
            </div>
        </div>
        @if($project->ownership_type === 'developer_third_party' && $project->displayDeveloperName() !== '—')
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
            <a href="{{ route('crm.projects.show', $project) }}"
               class="flex-1 min-w-[4.5rem] text-center py-2 rounded-lg text-xs font-bold text-white font-tajawal hover:opacity-90"
               style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">عرض</a>
            @can('update', $project)
            <a href="{{ route('crm.projects.edit', $project) }}"
               class="px-3 py-2 rounded-lg text-xs font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 font-tajawal">تعديل</a>
            @endcan
            <a href="{{ route('crm.pipeline.create', ['project_id' => $project->id]) }}"
               class="px-3 py-2 rounded-lg text-xs font-semibold font-tajawal hover:opacity-90"
               style="background: {{ $themeColor }}12; color: {{ $themeColor }};">+ صفقة</a>
            @can('delete', $project)
            <form action="{{ route('crm.projects.destroy', $project) }}" method="POST"
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
        @can('create-projects')
        <a href="{{ route('crm.projects.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
           style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">إضافة أول مشروع</a>
        @endcan
    </div>
    @endforelse
</div>
@if($projects->hasPages())
<div class="mt-6">{{ $projects->links() }}</div>
@endif
@endsection
