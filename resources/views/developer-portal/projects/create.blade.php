@extends('layouts.developer')
@section('page-title', 'مشروع جديد')
@section('content')
<h1 class="text-2xl font-bold mb-6">إضافة مشروع</h1>
<form method="POST" action="{{ route('developer.projects.store') }}">@csrf
@include('developer-portal.projects.partials.form')
<button type="submit" class="mt-4 px-6 py-3 rounded-xl text-white font-bold" style="background:var(--brand)">حفظ المشروع</button>
</form>
@endsection
