@extends('layouts.app')
@section('page-title', 'مراجعة طلب عميل')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $payload = $request->payload ?? [];
    $clientData = $payload['client'] ?? [];
@endphp

@include('crm.partials.page-header', [
    'title' => $request->summary,
    'subtitle' => $request->actionLabel() . ' — ' . $request->statusLabel(),
    'actionUrl' => route('crm.clients.approvals.index'),
    'actionLabel' => 'كل الطلبات',
])

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-tajawal">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 space-y-4">
        <h3 class="font-bold text-gray-900">تفاصيل الطلب</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-xs text-gray-500">مقدم الطلب</dt><dd class="font-semibold">{{ $request->requester?->name }}</dd></div>
            <div><dt class="text-xs text-gray-500">التاريخ</dt><dd>{{ $request->created_at->format('Y-m-d H:i') }}</dd></div>
            @if($request->client)
            <div><dt class="text-xs text-gray-500">العميل الحالي</dt><dd><a href="{{ route('crm.clients.show', $request->client) }}" class="font-bold" style="color:{{ $themeColor }}">{{ $request->client->name }}</a></dd></div>
            @endif
        </dl>

        @if($request->action !== 'delete' && !empty($clientData))
        <div class="border-t pt-4 space-y-2 text-sm">
            <p><span class="text-gray-500">الاسم:</span> <strong>{{ $clientData['name'] ?? '—' }}</strong></p>
            <p><span class="text-gray-500">الهاتف:</span> {{ $clientData['phone'] ?? '—' }}</p>
            <p><span class="text-gray-500">البريد:</span> {{ $clientData['email'] ?? '—' }}</p>
            <p><span class="text-gray-500">الحالة:</span> {{ $clientData['status'] ?? '—' }}</p>
            @if(!empty($clientData['notes']))
            <p class="text-gray-700 whitespace-pre-wrap">{{ $clientData['notes'] }}</p>
            @endif
        </div>
        @elseif($request->action === 'delete')
        <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl p-4 space-y-2">
            <p>طلب حذف العميل: <strong>{{ $payload['client_name'] ?? $request->client?->name }}</strong></p>
            @if($request->request_reason || !empty($payload['delete_reason']))
            <p class="text-red-800"><span class="font-bold">سبب الحذف:</span> {{ $request->request_reason ?? $payload['delete_reason'] }}</p>
            @endif
        </div>
        @endif

        @if($request->review_notes)
        <div class="border-t pt-4 text-sm text-gray-600">
            <p class="text-xs text-gray-500 mb-1">ملاحظات المراجعة</p>
            {{ $request->review_notes }}
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6">
        @if($canApprove)
        <h3 class="font-bold text-gray-900 mb-4">قرار الإدارة</h3>
        <form action="{{ route('crm.clients.approvals.approve', $request) }}" method="POST" class="mb-4">
            @csrf
            <textarea name="review_notes" rows="2" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm mb-3" placeholder="ملاحظات (اختياري)"></textarea>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-green-600 text-white text-sm font-bold">موافقة وتنفيذ</button>
        </form>
        <form action="{{ route('crm.clients.approvals.reject', $request) }}" method="POST">
            @csrf
            <textarea name="review_notes" rows="2" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm mb-3" placeholder="سبب الرفض (اختياري)"></textarea>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm font-bold">رفض الطلب</button>
        </form>
        @else
        <p class="text-sm text-gray-600">الحالة: <strong>{{ $request->statusLabel() }}</strong></p>
        @if($request->reviewer)
        <p class="text-xs text-gray-500 mt-2">بواسطة {{ $request->reviewer->name }} — {{ $request->reviewed_at?->format('Y-m-d H:i') }}</p>
        @endif
        @endif
    </div>
</div>
@endsection
