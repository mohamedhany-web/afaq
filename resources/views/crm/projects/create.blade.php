@extends('layouts.app')
@section('page-title', 'إضافة مشروع عقاري')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $projectsRoutePrefix = $projectsRoutePrefix ?? 'crm.projects';
    $pr = fn (string $action, mixed $params = []) => route($projectsRoutePrefix . '.' . $action, $params);
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

@include('crm.partials.page-header', [
    'title' => 'إضافة مشروع عقاري',
    'subtitle' => 'تسجيل مشروع جديد مع تحديد الموقع على الخريطة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => $pr('index'),
    'actionLabel' => 'قائمة المشاريع',
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
    سيتم إرسال بيانات المشروع للإدارة العليا للموافقة قبل نشره في النظام.
</div>
@endif

<form action="{{ $pr('store') }}" method="POST" class="w-full space-y-6">
    @csrf
    @include('projects.partials.form', ['project' => $project, 'users' => $users, 'developers' => $developers ?? collect(), 'themeColor' => $themeColor])
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-6">
        <a href="{{ $pr('index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">{{ ($requiresApproval ?? false) ? 'إرسال للموافقة' : 'حفظ المشروع' }}</button>
    </div>
</form>
@endsection
