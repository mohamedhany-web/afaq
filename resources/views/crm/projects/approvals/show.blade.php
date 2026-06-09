@extends('layouts.app')
@section('page-title', 'مراجعة طلب مشروع')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $payload = $request->payload ?? [];
    $projectData = $payload['project'] ?? [];
@endphp

@include('crm.partials.page-header', [
    'title' => $request->summary,
    'subtitle' => $request->actionLabel() . ' — ' . $request->statusLabel(),
    'actionUrl' => route('crm.projects.approvals.index'),
    'actionLabel' => 'كل الطلبات',
])

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-tajawal">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 space-y-4">
        <h3 class="font-bold text-gray-900">تفاصيل الطلب</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-xs text-gray-500">مقدم الطلب</dt><dd class="font-semibold">{{ $request->requester?->name }}</dd></div>
            <div><dt class="text-xs text-gray-500">التاريخ</dt><dd>{{ $request->created_at->format('Y-m-d H:i') }}</dd></div>
            @if($request->project)
            <div><dt class="text-xs text-gray-500">المشروع الحالي</dt><dd><a href="{{ route('crm.projects.show', $request->project) }}" class="font-bold" style="color:{{ $themeColor }}">{{ $request->project->name }}</a></dd></div>
            @endif
        </dl>

        @if($request->action !== 'delete' && !empty($projectData))
        <div class="border-t pt-4 space-y-2 text-sm">
            <p><span class="text-gray-500">الاسم:</span> <strong>{{ $projectData['name'] ?? '—' }}</strong></p>
            <p><span class="text-gray-500">المدينة:</span> {{ $projectData['city'] ?? '—' }}</p>
            <p><span class="text-gray-500">نوع العقار:</span> {{ \App\Models\Project::PROPERTY_TYPES[$projectData['property_type'] ?? ''] ?? '—' }}</p>
            <p><span class="text-gray-500">حالة العرض:</span> {{ \App\Models\Project::LISTING_STATUSES[$projectData['listing_status'] ?? ''] ?? '—' }}</p>
            <p><span class="text-gray-500">الوحدات:</span> {{ $projectData['total_units'] ?? 0 }} (متاح: {{ $projectData['available_units'] ?? 0 }})</p>
            @if(!empty($projectData['description']))
            <p class="text-gray-700 whitespace-pre-wrap">{{ $projectData['description'] }}</p>
            @endif
        </div>
        @elseif($request->action === 'delete')
        <p class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl p-4">طلب حذف المشروع: <strong>{{ $payload['project_name'] ?? $request->project?->name }}</strong></p>
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
        <h3 class="font-bold text-gray-900 mb-4">قرار الإدارة العليا</h3>
        <form action="{{ route('crm.projects.approvals.approve', $request) }}" method="POST" class="mb-4">
            @csrf
            <textarea name="review_notes" rows="2" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm mb-3" placeholder="ملاحظات (اختياري)"></textarea>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-green-600 text-white text-sm font-bold">موافقة وتنفيذ</button>
        </form>
        <form action="{{ route('crm.projects.approvals.reject', $request) }}" method="POST">
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
