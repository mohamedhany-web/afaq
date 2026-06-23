@extends('layouts.developer')
@section('page-title', 'لوحة التحكم')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

@include('crm.partials.page-header', [
    'title' => 'مرحباً، ' . $developer->name,
    'subtitle' => 'أدر مشاريعك ووحداتك — تظهر مباشرة لفريق المبيعات · ' . now()->locale('ar')->translatedFormat('l، d F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
    'actionUrl' => $account->canManageProjects() ? route('developer.projects.create') : null,
    'actionLabel' => 'مشروع جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>',
    'secondaryUrl' => route('developer.projects.index'),
    'secondaryLabel' => 'كل المشاريع',
    'secondaryIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>',
])

<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 mb-6 items-stretch">
    @include('crm.partials.stat-card', [
        'label' => 'المشاريع',
        'value' => number_format($stats['projects']),
        'accent' => 'theme',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>',
        'href' => route('developer.projects.index'),
        'linkLabel' => 'عرض المشاريع',
    ])
    @include('crm.partials.stat-card', [
        'label' => 'معروض للبيع',
        'value' => number_format($stats['active_listings']),
        'accent' => 'green',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>',
        'href' => route('developer.projects.index'),
        'linkLabel' => 'عرض المعروض',
    ])
    @include('crm.partials.stat-card', [
        'label' => 'إجمالي الوحدات',
        'value' => number_format($stats['total_units']),
        'accent' => 'blue',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7"/>',
        'href' => route('developer.projects.index'),
        'linkLabel' => 'عرض الوحدات',
    ])
    @include('crm.partials.stat-card', [
        'label' => 'وحدات متاحة',
        'value' => number_format($stats['available_units']),
        'accent' => 'amber',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        'href' => route('developer.projects.index'),
        'linkLabel' => 'عرض المتاح',
    ])
    @include('crm.partials.stat-card', [
        'label' => 'سابقة الأعمال',
        'value' => number_format($stats['portfolio']),
        'accent' => 'purple',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2"/>',
        'href' => route('developer.portfolio.index'),
        'linkLabel' => 'عرض السجل',
    ])
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900 flex flex-wrap items-center justify-between gap-3"
         style="{{ $headerStyle }}">
        <div>
            <h2 class="text-lg font-bold">أحدث المشاريع</h2>
            <p class="text-xs text-gray-500 font-normal mt-0.5">آخر التحديثات على مخزونك العقاري</p>
        </div>
        @if($account->canManageProjects())
        <a href="{{ route('developer.projects.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white text-xs font-semibold shadow-md"
           style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            مشروع جديد
        </a>
        @endif
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($recentProjects as $p)
        <a href="{{ route('developer.projects.show', $p) }}"
           class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4 hover:bg-gray-50 transition font-tajawal group">
            <div class="min-w-0">
                <div class="font-bold text-gray-900 group-hover:underline">{{ $p->name }}</div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ $p->city ?? '—' }}
                    @if($p->location)— {{ $p->location }}@endif
                    · {{ $p->total_units }} وحدة · {{ $p->available_units }} متاح
                </div>
            </div>
            <span class="text-xs font-bold shrink-0 px-2.5 py-1 rounded-full"
                  style="color: {{ $themeColor }}; background: {{ $themeColor }}15;">
                {{ $p->listing_status === 'active' ? 'معروض' : ($p->listing_status === 'upcoming' ? 'قريباً' : 'غير معروض') }}
            </span>
        </a>
        @empty
        <div class="p-10 text-center text-gray-400 font-tajawal">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
            </svg>
            <p class="font-semibold text-gray-600">لا توجد مشاريع بعد</p>
            <p class="text-sm mt-1">ابدأ بإضافة أول مشروع ليظهر لفريق المبيعات</p>
            @if($account->canManageProjects())
            <a href="{{ route('developer.projects.create') }}"
               class="inline-flex mt-4 px-5 py-2.5 rounded-xl text-white text-sm font-semibold"
               style="background: {{ $themeColor }};">إضافة مشروع</a>
            @endif
        </div>
        @endforelse
    </div>
</div>
@endsection
