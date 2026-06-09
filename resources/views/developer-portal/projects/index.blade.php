@extends('layouts.developer')
@section('page-title', 'مشاريعي')
@section('content')
<div class="mb-6 flex justify-between items-center gap-3">
    <h1 class="text-2xl font-bold">مشاريعي</h1>
    @if(auth('developer')->user()->canManageProjects())
    <a href="{{ route('developer.projects.create') }}" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:var(--brand)">+ مشروع جديد</a>
    @endif
</div>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($projects as $p)
    <a href="{{ route('developer.projects.show', $p) }}" class="bg-white rounded-2xl border p-5 hover:shadow-lg transition block">
        <div class="font-bold text-lg">{{ $p->name }}</div>
        <div class="text-sm text-gray-500 mt-1">{{ $p->city }} @if($p->location)— {{ $p->location }}@endif</div>
        <div class="mt-3 text-xs font-semibold text-gray-600">{{ $p->total_units }} وحدة · {{ $p->available_units }} متاح</div>
    </a>
    @empty<div class="col-span-full text-gray-400">لا مشاريع</div>@endforelse
</div>
<div class="mt-4">{{ $projects->links() }}</div>
@endsection
