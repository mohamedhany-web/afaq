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
    'actionUrl' => route('crm.projects.index'),
    'actionLabel' => 'المشاريع والوحدات',
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الوحدات', 'value' => $stats['total'], 'accent' => 'theme'])
    @include('crm.partials.stat-card', ['label' => 'متاحة', 'value' => $stats['available'], 'accent' => 'green'])
    @include('crm.partials.stat-card', ['label' => 'محجوزة', 'value' => $stats['reserved'], 'accent' => 'amber'])
    @include('crm.partials.stat-card', ['label' => 'مباعة', 'value' => $stats['sold'], 'accent' => 'blue'])
</div>

@if($inventoryKpis)
@include('operations.partials.kpi-group', ['group' => $inventoryKpis])
@endif

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
                </tr></thead>
                <tbody>
                @foreach($byProject as $project)
                <tr class="border-t">
                    <td class="p-3 font-semibold">{{ $project->name }}</td>
                    <td class="p-3 text-green-700 font-bold">{{ $project->available_count }}</td>
                    <td class="p-3 text-amber-700">{{ $project->reserved_count }}</td>
                    <td class="p-3 text-blue-700">{{ $project->sold_count }}</td>
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
                <p class="font-semibold">{{ $unit->code }} — {{ $unit->project?->name }}</p>
                <p class="text-xs text-gray-500">{{ $unit->useTypeLabel() }} — {{ $unit->area_m2 }} م²</p>
            </li>
            @empty
            <li class="p-6 text-center text-gray-500">جميع الوحدات المتاحة لها أسعار</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
