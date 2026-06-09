@extends('layouts.developer')
@section('page-title', 'لوحة التحكم')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">مرحباً، {{ $developer->name }}</h1><p class="text-sm text-gray-500">أدر مشاريعك ووحداتك — تظهر مباشرة لفريق المبيعات</p></div>
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
    @foreach([['المشاريع',$stats['projects']],['معروض',$stats['active_listings']],['الوحدات',$stats['total_units']],['متاح',$stats['available_units']],['سابقة أعمال',$stats['portfolio']]] as [$l,$v])
    <div class="bg-white rounded-2xl border p-4"><div class="text-xs text-gray-500">{{ $l }}</div><div class="text-2xl font-bold">{{ number_format($v) }}</div></div>
    @endforeach
</div>
<div class="bg-white rounded-2xl border">
    <div class="px-5 py-3 border-b font-bold flex justify-between"><span>أحدث المشاريع</span>
        @if($account->canManageProjects())<a href="{{ route('developer.projects.create') }}" class="text-sm font-bold" style="color:var(--brand)">+ مشروع</a>@endif
    </div>
    <div class="divide-y">
        @forelse($recentProjects as $p)
        <a href="{{ route('developer.projects.show', $p) }}" class="block px-5 py-3 hover:bg-gray-50 font-semibold text-sm">{{ $p->name }}</a>
        @empty<div class="p-6 text-gray-400 text-sm">ابدأ بإضافة أول مشروع</div>@endforelse
    </div>
</div>
@endsection
