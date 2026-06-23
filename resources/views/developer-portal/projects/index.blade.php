@extends('layouts.developer')
@section('page-title', 'مشاريعي')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $account = auth('developer')->user();
@endphp

@include('crm.partials.page-header', [
    'title' => 'مشاريعي',
    'subtitle' => 'إدارة المشاريع والوحدات المعروضة لفريق المبيعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
    'actionUrl' => $account->canManageProjects() ? route('developer.projects.create') : null,
    'actionLabel' => 'مشروع جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>',
])

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
    @forelse($projects as $p)
    <a href="{{ route('developer.projects.show', $p) }}"
       class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 block font-tajawal group">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="h-11 w-11 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg"
                 style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                </svg>
            </div>
            @include('projects.partials.listing-badge', ['status' => $p->listing_status])
        </div>
        <h3 class="font-bold text-lg text-gray-900 group-hover:underline">{{ $p->name }}</h3>
        <p class="text-sm text-gray-500 mt-1">
            {{ $p->city ?? '—' }}
            @if($p->location)— {{ $p->location }}@endif
        </p>
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-3 text-xs font-semibold text-gray-600">
            <span>{{ $p->total_units }} وحدة</span>
            <span class="text-green-600">{{ $p->available_units }} متاح</span>
            <span>{{ $p->property_type_name }}</span>
        </div>
    </a>
    @empty
    <div class="col-span-full bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-400 font-tajawal">
        <p class="font-semibold text-gray-600 mb-2">لا توجد مشاريع مسجّلة</p>
        @if($account->canManageProjects())
        <a href="{{ route('developer.projects.create') }}"
           class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-semibold mt-2"
           style="background: {{ $themeColor }};">إضافة أول مشروع</a>
        @endif
    </div>
    @endforelse
</div>

@if($projects->hasPages())
<div class="mt-6 font-tajawal">{{ $projects->links() }}</div>
@endif
@endsection
