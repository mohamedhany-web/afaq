@extends('layouts.app')
@section('page-title', 'فرق المبيعات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

@include('crm.partials.page-header', [
    'title' => ($isScopedManager ?? false) ? 'فريق المبيعات' : 'فرق المبيعات',
    'subtitle' => ($isScopedManager ?? false) ? 'إدارة فريقك وأعضائه وبياناتهم فقط' : 'تنظيم مديري المبيعات وفرقهم',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
    'actionUrl' => $canCreate ? route('crm.teams.create') : null,
    'actionLabel' => 'فريق جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => ($isScopedManager ?? false) ? 'فرقي' : 'إجمالي الفرق', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />', 'href' => route('crm.teams.index') . '#page-data', 'linkLabel' => 'عرض الفرق'])
    @include('crm.partials.stat-card', ['label' => 'فرق نشطة', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.teams.index') . '#page-data', 'linkLabel' => 'عرض النشطة'])
    @include('crm.partials.stat-card', ['label' => 'إجمالي الأعضاء', 'value' => $stats['members'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />', 'href' => route('crm.teams.index') . '#page-data', 'linkLabel' => 'عرض الأعضاء'])
    @include('crm.partials.stat-card', ['label' => 'صفقات الفرق', 'value' => number_format($stats['deals']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />', 'href' => route('crm.pipeline.index', ['view' => 'deals']), 'linkLabel' => 'عرض الصفقات'])
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الفريق، المدير، أو الوصف..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-44">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <option value="active" @selected(request('status') === 'active')>نشط</option>
                <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal"
                    style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">تطبيق</button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('crm.teams.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            @endif
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
    @forelse($teams as $team)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100" style="{{ $headerStyle }}">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <a href="{{ route('crm.teams.show', $team) }}" class="font-bold text-gray-900 font-tajawal hover:underline text-lg truncate block">{{ $team->name }}</a>
                    <p class="text-xs text-gray-500 mt-1 font-tajawal truncate">مدير: {{ $team->manager?->name ?? '—' }}</p>
                </div>
                @if($team->is_active)
                <span class="shrink-0 text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-bold font-tajawal">نشط</span>
                @else
                <span class="shrink-0 text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-bold font-tajawal">موقوف</span>
                @endif
            </div>
        </div>
        <div class="p-5 flex-1 flex flex-col">
            @if($team->description)
            <p class="text-xs text-gray-500 font-tajawal line-clamp-2 mb-4">{{ $team->description }}</p>
            @endif
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="rounded-xl bg-gray-50 p-3 text-center">
                    <p class="text-xl font-bold text-gray-900 tabular-nums">{{ $team->members_count }}</p>
                    <p class="text-[10px] text-gray-500 font-tajawal">عضو</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3 text-center">
                    <p class="text-xl font-bold tabular-nums" style="color:{{ $themeColor }}">{{ $team->sales_count }}</p>
                    <p class="text-[10px] text-gray-500 font-tajawal">صفقة</p>
                </div>
            </div>
            @if($team->members->isNotEmpty())
            <div class="flex flex-wrap gap-1 mb-4">
                @foreach($team->members->take(4) as $member)
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-tajawal">{{ $member->name }}</span>
                @endforeach
                @if($team->members_count > 4)
                <span class="text-[10px] px-2 py-0.5 rounded-full font-tajawal" style="background:{{ $themeColor }}15;color:{{ $themeColor }}">+{{ $team->members_count - 4 }}</span>
                @endif
            </div>
            @endif
            <div class="flex gap-2 mt-auto pt-3 border-t border-gray-100">
                <a href="{{ route('crm.teams.show', $team) }}" class="flex-1 text-center py-2 rounded-lg text-xs font-bold text-white font-tajawal"
                   style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">عرض</a>
                @if($canManageAllTeams ?? false)
                <a href="{{ route('crm.teams.edit', $team) }}" class="px-3 py-2 rounded-lg text-xs font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 font-tajawal">تعديل</a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl border border-gray-200 p-12 text-center">
        <p class="text-gray-400 font-tajawal mb-4">{{ ($isScopedManager ?? false) ? 'لم تنشئ فريق مبيعات بعد' : 'لا توجد فرق مبيعات' }}</p>
        @if($canCreate)
        <a href="{{ route('crm.teams.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
           style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">{{ ($isScopedManager ?? false) ? 'إنشاء فريقي' : 'إنشاء أول فريق' }}</a>
        @endif
    </div>
    @endforelse
</div>

@if($teams->hasPages())
<div class="bg-white rounded-2xl border border-gray-200 p-4">{{ $teams->links() }}</div>
@endif
@endsection
