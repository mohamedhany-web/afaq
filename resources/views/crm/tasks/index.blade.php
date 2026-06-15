@extends('layouts.app')
@section('page-title', 'إدارة المهام')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $isAdmin = $viewMode === \App\Services\CrmRoleResolver::WORKSPACE_ADMIN;
    $isManager = $viewMode === \App\Services\CrmRoleResolver::WORKSPACE_MANAGER;
@endphp

@include('crm.partials.page-header', [
    'title' => $isAdmin ? 'مهام الشركة' : ($isManager ? 'مهام الفريق' : 'مهامي'),
    'subtitle' => 'تعيين وتتبع وإنجاز المهام المرتبطة بالمبيعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
    'actionUrl' => $canCreate ? route('crm.tasks.create') : null,
    'actionLabel' => 'مهمة جديدة',
])

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-800 text-sm font-tajawal border border-green-200">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'نشطة', 'value' => $stats['total_active'], 'compact' => true, 'accent' => 'theme', 'href' => route('crm.tasks.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'عرض النشطة'])
    @include('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['due_today'], 'compact' => true, 'accent' => 'blue', 'href' => route('crm.tasks.index', ['due' => 'today']) . '#page-data', 'linkLabel' => 'عرض اليوم'])
    @include('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'compact' => true, 'accent' => 'red', 'href' => route('crm.tasks.index', ['due' => 'overdue']) . '#page-data', 'linkLabel' => 'عرض المتأخرة'])
    @include('crm.partials.stat-card', ['label' => 'حرجة', 'value' => $stats['critical'], 'compact' => true, 'accent' => 'amber', 'href' => route('crm.tasks.index', ['priority' => 'critical']) . '#page-data', 'linkLabel' => 'عرض الحرجة'])
    @include('crm.partials.stat-card', ['label' => 'أُنجزت هذا الأسبوع', 'value' => $stats['completed_week'], 'compact' => true, 'accent' => 'green', 'href' => route('crm.tasks.index', ['status' => 'completed']) . '#page-data', 'linkLabel' => 'عرض المنجزة'])
</div>

@if($isManager || $isAdmin)
<div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden mb-6">
    <div class="px-5 py-3 border-b" style="{{ $headerStyle }}"><h3 class="font-bold font-tajawal text-sm">إنتاجية الفريق (7 أيام)</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm font-tajawal">
            <thead class="bg-gray-50 text-xs text-gray-500"><tr>
                <th class="text-right p-3">الموظف</th><th class="text-center p-3">مكتمل</th><th class="text-center p-3">متأخر</th><th class="text-center p-3">نشط</th><th class="text-center p-3">متوسط الأداء</th>
            </tr></thead>
            <tbody>
            @forelse($teamProductivity as $row)
                <tr class="border-t {{ $row['overloaded'] ? 'bg-amber-50' : '' }}">
                    <td class="p-3 font-semibold">{{ $row['user']->name }} @if($row['overloaded'])<span class="text-[10px] text-amber-700">حمّل زائد</span>@endif</td>
                    <td class="p-3 text-center">{{ $row['completed_week'] }}</td>
                    <td class="p-3 text-center text-red-600 font-bold">{{ $row['overdue'] }}</td>
                    <td class="p-3 text-center">{{ $row['open'] }}</td>
                    <td class="p-3 text-center">{{ $row['avg_score'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-gray-400">لا بيانات فريق</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="flex flex-wrap gap-1 mb-3 font-tajawal">
    @foreach(['active' => 'النشطة', 'today' => 'اليوم', 'overdue' => 'متأخرة', 'critical' => 'حرجة', 'high' => 'عالية+', 'completed' => 'مكتملة'] as $key => $lbl)
    <a href="{{ route('crm.tasks.index', array_merge(request()->except('filter', 'page'), ['filter' => $key])) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ ($filter ?? 'active') === $key ? 'text-white' : 'bg-gray-100 text-gray-600' }}"
       @if(($filter ?? 'active') === $key) style="background:{{ $themeColor }}" @endif>{{ $lbl }}</a>
    @endforeach
</div>
@include('crm.partials.filter-bar')

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($tasks as $task)
        @include('crm.tasks.partials.task-card', ['task' => $task])
    @empty
        <div class="col-span-full text-center py-16 text-gray-400 font-tajawal">
            <p class="mb-4">لا توجد مهام في هذا العرض</p>
            @if($canCreate)<a href="{{ route('crm.tasks.create') }}" class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">إنشاء مهمة</a>@endif
        </div>
    @endforelse
</div>
@if($tasks->hasPages())<div class="mt-6">{{ $tasks->links() }}</div>@endif
@endsection
