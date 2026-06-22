@extends('layouts.app')
@section('page-title', 'مسار المبيعات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
@endphp

@include('crm.partials.page-header', [
    'title' => 'مسار المبيعات',
    'subtitle' => 'اختر عميلاً لفتح مساره — السحب والإفلات وتسجيل البيانات داخل صفحة العميل',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
    'actionUrl' => route('crm.clients.create'),
    'actionLabel' => 'عميل جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])

@include('crm.pipeline.partials.view-switcher', ['current' => 'list'])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'New Lead / جديد', 'value' => $stats['new_queue'] ?? 0, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />', 'href' => route('crm.pipeline.index', ['lead_stage' => 'new']), 'linkLabel' => 'عرض Kanban'])
    @include('crm.partials.stat-card', ['label' => 'جدد اليوم', 'value' => $stats['new_today'] ?? 0, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />', 'href' => route('crm.pipeline.index', ['lead_stage' => 'new', 'created_from' => today()->toDateString(), 'created_to' => today()->toDateString()]), 'linkLabel' => 'عرض جدد اليوم'])
    @include('crm.partials.stat-card', ['label' => 'محتملون', 'value' => $stats['prospect'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.pipeline.index', ['view' => 'list', 'status' => 'prospect']) . '#page-data', 'linkLabel' => 'عرض المحتملين'])
    @include('crm.partials.stat-card', ['label' => 'لديهم صفقات', 'value' => $stats['with_deals'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2" />', 'href' => route('crm.pipeline.index', ['view' => 'list', 'has_deals' => '1']) . '#page-data', 'linkLabel' => 'عرض الصفقات'])
</div>

@include('crm.partials.filter-bar')

<div id="page-data" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <h2 class="font-bold text-gray-900 font-tajawal">قائمة العملاء</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">{{ $clients->total() }} عميل</span>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($clients as $client)
        <div class="flex items-center gap-4 px-5 sm:px-6 py-4 hover:bg-gray-50/80 transition group">
            <a href="{{ $client->profileUrl() }}" class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 text-white font-bold text-sm font-tajawal hover:opacity-90"
                 style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);">
                {{ mb_substr($client->name, 0, 1) }}
            </a>
            <div class="flex-1 min-w-0">
                <a href="{{ $client->profileUrl() }}" class="font-bold text-gray-900 font-tajawal hover:underline truncate block">{{ $client->name }}</a>
                <p class="text-sm text-gray-500 font-tajawal truncate" dir="ltr">{{ $client->phone }}</p>
                <div class="mt-1 hidden sm:block">
                    @include('crm.clients.partials.created-by', ['client' => $client])
                </div>
            </div>
            <div class="hidden sm:flex flex-col items-end gap-1 shrink-0">
                @include('crm.clients.partials.status-badge', ['status' => $client->status])
                <span class="text-xs px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 font-semibold font-tajawal">
                    {{ $stageLabels[$client->lead_stage] ?? $client->lead_stage }}
                </span>
            </div>
            <div class="text-left shrink-0 flex flex-col items-end gap-2">
                @if($client->scoped_sales_count > 0)
                <span class="text-xs font-bold font-tajawal px-2.5 py-1 rounded-lg" style="background: {{ $themeColor }}12; color: {{ $themeColor }};">
                    {{ $client->scoped_sales_count }} صفقة
                </span>
                @else
                <span class="text-xs text-gray-400 font-tajawal">بدون صفقات</span>
                @endif
                <div class="flex flex-wrap gap-1 justify-end">
                    @can('viewFullDetails', $client)
                    <a href="{{ route('crm.clients.show', $client) }}" class="px-2 py-1 rounded-lg text-[10px] font-bold border hover:bg-gray-50" style="color:{{ $themeColor }};border-color:{{ $themeColor }}40">الملف الكامل</a>
                    @endcan
                    @can('update', $client)
                    <a href="{{ route('crm.clients.edit', $client) }}" class="px-2 py-1 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-700 hover:bg-gray-200">تعديل</a>
                    @endcan
                    <a href="{{ route('crm.pipeline.client', $client) }}" class="px-2 py-1 rounded-lg text-[10px] font-bold text-white" style="background:{{ $themeColor }}">المسار</a>
                </div>
            </div>
        </div>
        @empty
        <div class="py-16 text-center text-gray-400 font-tajawal">
            <p class="mb-4">لا يوجد عملاء مطابقون</p>
            <a href="{{ route('crm.clients.create') }}" class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-semibold"
               style="background: {{ $themeColor }};">إضافة عميل</a>
        </div>
        @endforelse
    </div>

    @if($clients->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $clients->links() }}</div>
    @endif
</div>
@endsection
