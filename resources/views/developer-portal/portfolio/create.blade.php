@extends('layouts.developer')
@section('page-title', 'إضافة سابقة أعمال')
@section('content')
<h1 class="text-2xl font-bold mb-6">إضافة لمشروع سابق</h1>
<form method="POST" action="{{ route('developer.portfolio.store') }}">@csrf
@include('developer-portal.portfolio.partials.form')
<button class="mt-4 px-6 py-3 rounded-xl text-white font-bold" style="background:var(--brand)">حفظ</button>
</form>
@endsection
