@extends('layouts.app')
@section('page-title', 'عملاء محتملون')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'العملاء المحتملون — التسويق',
    'subtitle' => 'Leads من الحملات والقنوات',
    'actionUrl' => route('marketing.leads.create'),
    'actionLabel' => 'إضافة Lead',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-3 gap-3 mb-4">
    @include('crm.partials.stat-card', ['label' => 'الإجمالي', 'value' => $stats['total'], 'accent' => 'purple'])
    @include('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['today'], 'accent' => 'blue'])
    @include('crm.partials.stat-card', ['label' => 'الشهر', 'value' => $stats['month'], 'accent' => 'green'])
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..." class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
    <select name="campaign_id" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
        <option value="">كل الحملات</option>
        @foreach($campaigns as $c)<option value="{{ $c->id }}" @selected(request('campaign_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm" style="background:{{ $themeColor }}">تصفية</button>
</form>

<div class="bg-white rounded-2xl shadow-lg border overflow-x-auto">
    <table class="w-full text-sm font-tajawal">
        <thead class="bg-gray-50 text-gray-600"><tr><th class="px-4 py-3 text-right">الاسم</th><th class="px-4 py-3 text-right">الهاتف</th><th class="px-4 py-3 text-right">الحملة</th><th class="px-4 py-3 text-right">المصدر</th><th class="px-4 py-3 text-right">التاريخ</th></tr></thead>
        <tbody class="divide-y">
            @forelse($leads as $lead)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $lead->name }}</td>
                <td class="px-4 py-3">{{ $lead->phone }}</td>
                <td class="px-4 py-3">{{ $lead->marketingCampaign?->name ?? '—' }}</td>
                <td class="px-4 py-3">{{ config('marketing.lead_sources.'.$lead->lead_source, $lead->lead_source ?? '—') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $lead->created_at->format('Y-m-d') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">لا leads.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $leads->links() }}</div>
@endsection
