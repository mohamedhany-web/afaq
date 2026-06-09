@extends('layouts.developer')
@section('page-title', $project->name)
@section('content')
<div class="mb-6 flex flex-wrap justify-between gap-3">
    <div><h1 class="text-2xl font-bold">{{ $project->name }}</h1><p class="text-sm text-gray-500">{{ $project->city }} @if($project->location)— {{ $project->location }}@endif</p></div>
    @if($account->canManageProjects())
    <div class="flex gap-2">
        <a href="{{ route('developer.projects.edit', $project) }}" class="px-4 py-2 rounded-xl border text-sm font-bold">تعديل</a>
        <form method="POST" action="{{ route('developer.projects.destroy', $project) }}" onsubmit="return confirm('حذف المشروع؟')">@csrf @method('DELETE')
            <button class="px-4 py-2 rounded-xl bg-red-50 text-red-700 text-sm font-bold">حذف</button>
        </form>
    </div>
    @endif
</div>
<p class="text-sm text-gray-600 mb-4">{{ $project->description }}</p>
@include('crm.projects.partials.building-units', [
    'project' => $project,
    'themeColor' => $themeColor,
    'buildingSummary' => $buildingSummary,
    'unitsGenerateRoute' => route('developer.projects.units.generate', $project),
    'unitUpdateUrl' => preg_replace('/\/0(\?|$)/', '/__ID__$1', route('developer.projects.units.update', ['project' => $project, 'unit' => 0])),
    'showDealButton' => false,
    'canEdit' => $account->canManageProjects(),
])
@endsection
