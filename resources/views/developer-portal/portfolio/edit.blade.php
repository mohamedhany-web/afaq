@extends('layouts.developer')
@section('page-title', 'تعديل سابقة أعمال')
@section('content')
<h1 class="text-2xl font-bold mb-6">تعديل: {{ $portfolio->title }}</h1>
<form method="POST" action="{{ route('developer.portfolio.update', $portfolio) }}">@csrf @method('PUT')
@include('developer-portal.portfolio.partials.form', ['portfolio' => $portfolio])
<button class="mt-4 px-6 py-3 rounded-xl text-white font-bold" style="background:var(--brand)">حفظ</button>
</form>
@endsection
