@extends('layouts.app')
@section('page-title', 'موافقات العملاء')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'طلبات العملاء',
    'subtitle' => 'مراجعة طلبات الإضافة والتعديل والحذف من فريق المبيعات والتسويق',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
])

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-3 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'بانتظار الموافقة', 'value' => $stats['pending'], 'accent' => 'amber', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'معتمدة الشهر', 'value' => $stats['approved'], 'accent' => 'green', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'مرفوضة الشهر', 'value' => $stats['rejected'], 'accent' => 'red', 'compact' => true])
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b flex flex-wrap gap-2 font-tajawal">
        <a href="{{ route('crm.clients.approvals.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('status') ? 'text-white' : 'bg-gray-100 text-gray-600' }}"
           @if(!request('status')) style="background:{{ $themeColor }}" @endif>معلّقة</a>
        <a href="{{ route('crm.clients.approvals.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('status')==='approved' ? 'text-white' : 'bg-gray-100 text-gray-600' }}"
           @if(request('status')==='approved') style="background:{{ $themeColor }}" @endif>معتمدة</a>
        <a href="{{ route('crm.clients.approvals.index', ['status' => 'rejected']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('status')==='rejected' ? 'text-white' : 'bg-gray-100 text-gray-600' }}"
           @if(request('status')==='rejected') style="background:{{ $themeColor }}" @endif>مرفوضة</a>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($requests as $req)
        <a href="{{ route('crm.clients.approvals.show', $req) }}" class="block px-5 py-4 hover:bg-gray-50 font-tajawal">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="font-semibold text-gray-900">{{ $req->summary }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $req->actionLabel() }} — {{ $req->requester?->name }} — {{ $req->created_at->diffForHumans() }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                    @if($req->statusColor()==='amber') bg-amber-100 text-amber-800
                    @elseif($req->statusColor()==='green') bg-green-100 text-green-800
                    @elseif($req->statusColor()==='red') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-700 @endif">{{ $req->statusLabel() }}</span>
            </div>
        </a>
        @empty
        <p class="p-8 text-center text-sm text-gray-500 font-tajawal">لا توجد طلبات في هذا القسم.</p>
        @endforelse
    </div>
    @if($requests->hasPages())
    <div class="px-5 py-4 border-t">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
