@extends('layouts.app')
@section('page-title', 'طلباتي — العملاء')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'طلبات العملاء',
    'subtitle' => 'إضافة وتعديل وحذف العملاء — تُنفَّذ بعد موافقة الإدارة',
    'actionUrl' => auth()->user()?->can('create', \App\Models\Client::class) ? route('crm.clients.create') : null,
    'actionLabel' => 'طلب عميل جديد',
])

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif

<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900 font-tajawal">
    أي إضافة أو تعديل أو حذف لعميل يمرّ بموافقة <strong>الإدارة</strong> قبل التطبيق على النظام. الحذف يتطلب كتابة سبب واضح.
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 divide-y divide-gray-100">
    @forelse($requests as $req)
    <a href="{{ route('crm.clients.approvals.show', $req) }}" class="block px-5 py-4 hover:bg-gray-50 font-tajawal">
        <div class="flex justify-between gap-3">
            <div>
                <p class="font-semibold text-gray-900">{{ $req->summary }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $req->actionLabel() }} — {{ $req->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold h-fit
                @if($req->statusColor()==='amber') bg-amber-100 text-amber-800
                @elseif($req->statusColor()==='green') bg-green-100 text-green-800
                @else bg-red-100 text-red-800 @endif">{{ $req->statusLabel() }}</span>
        </div>
    </a>
    @empty
    <p class="p-8 text-center text-sm text-gray-500">لم ترسل أي طلبات بعد.</p>
    @endforelse
</div>
@if($requests->hasPages())
<div class="mt-4">{{ $requests->links() }}</div>
@endif
@endsection
