@extends('layouts.developer')
@section('page-title', 'تعديل مشروع')
@section('content')
<h1 class="text-2xl font-bold mb-6">تعديل: {{ $project->name }}</h1>
<form method="POST" action="{{ route('developer.projects.update', $project) }}">@csrf @method('PUT')
@include('developer-portal.projects.partials.form', ['project' => $project])
<div class="mt-4 flex gap-3">
    <button type="submit" class="px-6 py-3 rounded-xl text-white font-bold" style="background:var(--brand)">حفظ</button>
    <a href="{{ route('developer.projects.show', $project) }}" class="px-6 py-3 rounded-xl border text-sm font-bold">رجوع</a>
</div>
</form>
@endsection
