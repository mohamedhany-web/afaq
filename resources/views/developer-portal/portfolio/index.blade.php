@extends('layouts.developer')
@section('page-title', 'سابقة الأعمال')
@section('content')
<div class="mb-6 flex justify-between"><h1 class="text-2xl font-bold">سابقة الأعمال</h1>
@if(auth('developer')->user()->canManagePortfolio())<a href="{{ route('developer.portfolio.create') }}" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:var(--brand)">+ إضافة</a>@endif</div>
<div class="space-y-3">
@forelse($items as $item)
<div class="bg-white rounded-2xl border p-5 flex justify-between gap-3">
    <div><div class="font-bold">{{ $item->title }}</div><div class="text-sm text-gray-500">{{ $item->city }} {{ $item->location }} @if($item->year)— {{ $item->year }}@endif</div><p class="text-sm text-gray-600 mt-2">{{ Str::limit($item->description, 120) }}</p></div>
    @if(auth('developer')->user()->canManagePortfolio())<a href="{{ route('developer.portfolio.edit', $item) }}" class="text-sm font-bold shrink-0" style="color:var(--brand)">تعديل</a>@endif
</div>
@empty<div class="text-gray-400">أضف مشاريع سابقة لعرض خبرة المطور</div>@endforelse
</div>
<div class="mt-4">{{ $items->links() }}</div>
@endsection
