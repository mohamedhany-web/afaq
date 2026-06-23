@extends('layouts.developer')
@section('page-title', $project->name)

@section('content')
@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
@endphp

@include('crm.partials.page-header', [
    'title' => $project->name,
    'subtitle' => trim(($project->city ?? '') . ($project->location ? ' — ' . $project->location : '')),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>',
    'secondaryUrl' => route('developer.projects.index'),
    'secondaryLabel' => 'قائمة المشاريع',
    'secondaryIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>',
    'actionUrl' => $account->canManageProjects() ? route('developer.projects.edit', $project) : null,
    'actionLabel' => 'تعديل المشروع',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'الوحدات', 'value' => $project->total_units ?? 0, 'accent' => 'theme', 'href' => '#building-units-root', 'linkLabel' => 'عرض الوحدات'])
    @include('crm.partials.stat-card', ['label' => 'متاح', 'value' => $project->available_units ?? 0, 'accent' => 'green', 'href' => '?status=available#building-units-root', 'linkLabel' => 'عرض المتاح'])
    @include('crm.partials.stat-card', ['label' => 'مباع', 'value' => $project->sold_units ?? 0, 'accent' => 'amber'])
    @include('crm.partials.stat-card', ['label' => 'حالة العرض', 'value' => $project->listing_status === 'active' ? 'معروض' : ($project->listing_status === 'upcoming' ? 'قريباً' : 'متوقف'), 'accent' => 'blue'])
</div>

@if($project->description)
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 mb-6 font-tajawal">
    <h2 class="text-sm font-bold text-gray-500 mb-2">وصف المشروع</h2>
    <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $project->description }}</p>
</div>
@endif

@include('projects.partials.classification-filter', compact('project', 'themeColor'))

@include('crm.projects.partials.building-units', [
    'project' => $project,
    'themeColor' => $themeColor,
    'buildingSummary' => $buildingSummary,
    'projectsRoutePrefix' => 'developer.projects',
    'unitsGenerateRoute' => route('developer.projects.units.generate', $project),
    'unitUpdateUrl' => preg_replace('/\/0(\?|$)/', '/__ID__$1', route('developer.projects.units.update', ['project' => $project, 'unit' => 0])),
    'unitShowUrl' => '',
    'showDealButton' => false,
    'canEdit' => $account->canManageProjects(),
])

@if($account->canManageProjects())
<div class="mt-6 flex justify-end">
    <form method="POST" action="{{ route('developer.projects.destroy', $project) }}"
          onsubmit="return confirm('حذف هذا المشروع؟ لا يمكن التراجع.')">
        @csrf @method('DELETE')
        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-red-50 text-red-600 hover:bg-red-100 font-tajawal border border-red-100">
            حذف المشروع
        </button>
    </form>
</div>
@endif
@endsection
