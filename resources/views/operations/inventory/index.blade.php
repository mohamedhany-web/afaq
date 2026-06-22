@extends('layouts.app')
@section('page-title', 'المخزون العقاري')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $statusLabels = config('project_units.statuses', []);
@endphp

@include('crm.partials.page-header', [
    'title' => 'إدارة المخزون العقاري',
    'subtitle' => 'الوحدات المتاحة والمحجوزة والمباعة — دقة الأسعار',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
    'actionUrl' => route('operations.projects.index'),
    'actionLabel' => 'إدارة المشاريع',
])

@include('crm.partials.filter-bar', [
    'mode' => 'projects',
    'action' => route('operations.inventory.index'),
    'clearUrl' => $clearUrl ?? route('operations.inventory.index'),
    'projectsRoutePrefix' => $projectsRoutePrefix ?? 'operations.projects',
    'inventoryExportRoute' => $inventoryExportRoute ?? route('operations.inventory.export', request()->query()),
    'preserve' => array_filter(['status' => request('status')]),
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الوحدات', 'value' => $stats['total'], 'accent' => 'theme', 'href' => route('operations.inventory.index') . '#page-data', 'linkLabel' => 'عرض الوحدات'])
    @include('crm.partials.stat-card', ['label' => 'متاحة', 'value' => $stats['available'], 'accent' => 'green', 'href' => route('operations.inventory.index', ['status' => 'available']) . '#page-data', 'linkLabel' => 'عرض المتاح'])
    @include('crm.partials.stat-card', ['label' => 'محجوزة', 'value' => $stats['reserved'], 'accent' => 'amber', 'href' => route('operations.inventory.index', ['status' => 'reserved']) . '#page-data', 'linkLabel' => 'عرض المحجوز'])
    @include('crm.partials.stat-card', ['label' => 'مباعة', 'value' => $stats['sold'], 'accent' => 'blue', 'href' => route('operations.inventory.index', ['status' => 'sold']) . '#page-data', 'linkLabel' => 'عرض المباع'])
</div>

@if($inventoryKpis)
@include('operations.partials.kpi-group', ['group' => $inventoryKpis, 'link' => route('operations.inventory.index') . '#page-data'])
@endif

@if($selectedProject ?? null)
@include('projects.partials.classification-filter', [
    'project' => $selectedProject,
    'themeColor' => $themeColor,
    'filterMode' => 'operations',
    'opsFilterUrl' => route('operations.inventory.index', array_filter([
        'project_id' => $selectedProject->id,
        'status' => request('status'),
        'search' => request('search'),
    ])),
    'defaultClass' => request('use_type'),
])
@endif

@include('operations.partials.unit-inventory-cards', compact('units', 'projects', 'statusFilter', 'themeColor', 'useTypeFilter', 'useTypeLabels') + ['projectsRoutePrefix' => $projectsRoutePrefix ?? 'operations.projects'])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6 font-tajawal">
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold">المخزون حسب المشروع</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="p-3 text-right">المشروع</th>
                    <th class="p-3 text-right">متاح</th>
                    <th class="p-3 text-right">محجوز</th>
                    <th class="p-3 text-right">مباع</th>
                    <th class="p-3 text-right"></th>
                </tr></thead>
                <tbody>
                @foreach($byProject as $project)
                <tr class="border-t">
                    <td class="p-3 font-semibold">{{ $project->name }}</td>
                    <td class="p-3 text-green-700 font-bold">{{ $project->available_count }}</td>
                    <td class="p-3 text-amber-700">{{ $project->reserved_count }}</td>
                    <td class="p-3 text-blue-700">{{ $project->sold_count }}</td>
                    <td class="p-3">
                        <a href="{{ route(($projectsRoutePrefix ?? 'operations.projects') . '.show', $project) }}#building-units-root" class="text-xs font-bold hover:underline" style="color:{{ $themeColor }}">الوحدات</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold text-red-700">وحدات بدون سعر</div>
        <ul class="divide-y">
            @forelse($missingPrice as $unit)
            <li class="p-4 text-sm">
                <a href="{{ route(($projectsRoutePrefix ?? 'operations.projects') . '.show', $unit->project_id) }}?unit={{ $unit->id }}#building-units-root" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $unit->code }} — {{ $unit->project?->name }}</a>
                <p class="text-xs text-gray-500">{{ $unit->useTypeLabel() }} — {{ $unit->area_m2 }} م²</p>
            </li>
            @empty
            <li class="p-6 text-center text-gray-500">جميع الوحدات المتاحة لها أسعار</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
