@extends('layouts.developer')
@section('page-title', 'سابقة الأعمال')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $account = auth('developer')->user();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

@include('crm.partials.page-header', [
    'title' => 'سابقة الأعمال',
    'subtitle' => 'عرض خبرات المطور ومشاريعه السابقة لفريق المبيعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2"/>',
    'actionUrl' => $account->canManagePortfolio() ? route('developer.portfolio.create') : null,
    'actionLabel' => 'إضافة مشروع سابق',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>',
])

<div class="space-y-4">
    @forelse($items as $item)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden font-tajawal hover:shadow-xl transition-shadow">
        <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h3 class="font-bold text-lg text-gray-900">{{ $item->title }}</h3>
                    @if($item->year)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ $item->year }}</span>
                    @endif
                    @if($item->is_published)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full text-green-700 bg-green-50">منشور</span>
                    @else
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full text-gray-500 bg-gray-100">مسودة</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500">
                    {{ $item->city }}
                    @if($item->location)— {{ $item->location }}@endif
                    @if($item->project_type)<span class="text-gray-300 mx-1">·</span>{{ $item->project_type }}@endif
                </p>
                @if($item->description)
                <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ Str::limit($item->description, 200) }}</p>
                @endif
            </div>
            @if($account->canManagePortfolio())
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('developer.portfolio.edit', $item) }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold border-2 hover:bg-gray-50"
                   style="border-color: {{ $themeColor }}40; color: {{ $themeColor }};">
                    تعديل
                </a>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-400 font-tajawal">
        <p class="font-semibold text-gray-600">لا توجد عناصر في سابقة الأعمال</p>
        <p class="text-sm mt-1">أضف مشاريع سابقة لعرض خبرة المطور</p>
        @if($account->canManagePortfolio())
        <a href="{{ route('developer.portfolio.create') }}"
           class="inline-flex mt-4 px-5 py-2.5 rounded-xl text-white text-sm font-semibold"
           style="background: {{ $themeColor }};">إضافة الآن</a>
        @endif
    </div>
    @endforelse
</div>

@if($items->hasPages())
<div class="mt-6 font-tajawal">{{ $items->links() }}</div>
@endif
@endsection
