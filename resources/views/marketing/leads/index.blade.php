@extends('layouts.app')
@section('page-title', 'عملاء محتملون')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'العملاء المحتملون — التسويق',
    'subtitle' => 'Leads من الحملات والقنوات',
    'actionUrl' => route('marketing.leads.create'),
    'actionLabel' => 'إضافة Lead',
    'secondaryUrl' => auth()->user()?->can('create', \App\Models\Client::class) ? route('crm.clients.create') : null,
    'secondaryLabel' => 'عميل في CRM',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-3 gap-3 mb-4">
    @include('crm.partials.stat-card', ['label' => 'الإجمالي', 'value' => $stats['total'], 'accent' => 'purple', 'href' => route('marketing.leads.index') . '#page-data', 'linkLabel' => 'عرض القائمة'])
    @include('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['today'], 'accent' => 'blue', 'href' => route('marketing.leads.index', ['period' => 'today']) . '#page-data', 'linkLabel' => 'عرض اليوم'])
    @include('crm.partials.stat-card', ['label' => 'الشهر', 'value' => $stats['month'], 'accent' => 'green', 'href' => route('marketing.leads.index', ['period' => 'month']) . '#page-data', 'linkLabel' => 'عرض الشهر'])
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..." class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
    <select name="campaign_id" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
        <option value="">كل الحملات</option>
        @foreach($campaigns as $c)<option value="{{ $c->id }}" @selected(request('campaign_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm" style="background:{{ $themeColor }}">تصفية</button>
</form>

<div id="page-data" class="bg-white rounded-2xl shadow-lg border overflow-x-auto">
    <table class="w-full text-sm font-tajawal">
        <thead class="bg-gray-50 text-gray-600"><tr>
            <th class="px-4 py-3 text-right">الاسم</th>
            <th class="px-4 py-3 text-right">الهاتف</th>
            <th class="px-4 py-3 text-right">الحملة</th>
            <th class="px-4 py-3 text-right">المصدر</th>
            <th class="px-4 py-3 text-right">التاريخ</th>
            <th class="px-4 py-3 text-right">إجراءات</th>
        </tr></thead>
        <tbody class="divide-y">
            @forelse($leads as $lead)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <a href="{{ $lead->profileUrl() }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $lead->name }}</a>
                </td>
                <td class="px-4 py-3" dir="ltr">{{ $lead->phone }}</td>
                <td class="px-4 py-3">{{ $lead->marketingCampaign?->name ?? '—' }}</td>
                <td class="px-4 py-3">{{ config('marketing.lead_sources.'.$lead->lead_source, $lead->lead_source ?? '—') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $lead->created_at->format('Y-m-d') }}</td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @can('viewFullDetails', $lead)
                        <a href="{{ route('crm.clients.show', $lead) }}" class="px-2 py-1 rounded-lg text-xs font-bold border hover:bg-gray-50" style="color:{{ $themeColor }};border-color:{{ $themeColor }}40">الملف الكامل</a>
                        @else
                        <a href="{{ $lead->profileUrl() }}" class="px-2 py-1 rounded-lg text-xs font-bold border hover:bg-gray-50" style="color:{{ $themeColor }};border-color:{{ $themeColor }}40">المسار</a>
                        @endcan
                        @can('update', $lead)
                        <a href="{{ route('crm.clients.edit', $lead) }}" class="px-2 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700 hover:bg-gray-200">تعديل</a>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">لا leads.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $leads->links() }}</div>
@endsection
