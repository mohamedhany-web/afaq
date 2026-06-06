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

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'العملاء', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'])
    @include('crm.partials.stat-card', ['label' => 'محتملون', 'value' => $stats['prospect'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'])
    @include('crm.partials.stat-card', ['label' => 'نشطون', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'])
    @include('crm.partials.stat-card', ['label' => 'لديهم صفقات', 'value' => $stats['with_deals'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2" />'])
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="الاسم، الهاتف، البريد، أو الشركة..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-48">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">حالة العميل</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">كل الحالات</option>
                @foreach($statusLabels as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full lg:w-48">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">مرحلة الرحلة</label>
            <select name="lead_stage" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">كل المراحل</option>
                @foreach($stageLabels as $key => $label)
                <option value="{{ $key }}" @selected(request('lead_stage') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal"
                    style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">بحث</button>
            @if(request()->hasAny(['search', 'status', 'lead_stage', 'has_deals', 'deal_stage']))
            <a href="{{ route('crm.pipeline.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <h2 class="font-bold text-gray-900 font-tajawal">قائمة العملاء</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">{{ $clients->total() }} عميل</span>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($clients as $client)
        <a href="{{ route('crm.pipeline.client', $client) }}"
           class="flex items-center gap-4 px-5 sm:px-6 py-4 hover:bg-gray-50/80 transition group">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 text-white font-bold text-sm font-tajawal"
                 style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);">
                {{ mb_substr($client->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-gray-900 font-tajawal group-hover:underline truncate">{{ $client->name }}</p>
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
            <div class="text-left shrink-0">
                @if($client->scoped_sales_count > 0)
                <span class="text-xs font-bold font-tajawal px-2.5 py-1 rounded-lg" style="background: {{ $themeColor }}12; color: {{ $themeColor }};">
                    {{ $client->scoped_sales_count }} صفقة
                </span>
                @else
                <span class="text-xs text-gray-400 font-tajawal">بدون صفقات</span>
                @endif
                <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-500 mt-1 mr-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </div>
        </a>
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
