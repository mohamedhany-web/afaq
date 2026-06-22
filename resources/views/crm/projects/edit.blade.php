@extends('layouts.app')
@section('page-title', 'تعديل — ' . $project->name)

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $projectsRoutePrefix = $projectsRoutePrefix ?? 'crm.projects';
    $pr = fn (string $action, mixed $params = []) => route($projectsRoutePrefix . '.' . $action, $params);
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

@include('crm.partials.page-header', [
    'title' => 'تعديل المشروع',
    'subtitle' => $project->name,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
    'actionUrl' => $pr('show', $project),
    'actionLabel' => 'عرض المشروع',
])

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
    <ul class="list-disc pr-5 text-sm text-red-700 font-tajawal space-y-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

@if($requiresApproval ?? false)
<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900 font-tajawal">
    التعديلات تُرسل للإدارة العليا ولن تُطبَّق على المشروع إلا بعد الموافقة.
</div>
@endif

<form action="{{ $pr('update', $project) }}" method="POST" class="w-full space-y-6">
    @csrf @method('PUT')
    @include('projects.partials.form', ['project' => $project, 'users' => $users, 'developers' => $developers ?? collect(), 'themeColor' => $themeColor])
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-6">
        <a href="{{ $pr('show', $project) }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">{{ ($requiresApproval ?? false) ? 'إرسال طلب التعديل' : 'حفظ التعديلات' }}</button>
    </div>
</form>
@endsection
