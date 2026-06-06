@extends('layouts.app')
@section('page-title', 'إضافة Lead')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
@endphp

@include('crm.partials.page-header', ['title' => 'إضافة عميل محتمل', 'actionUrl' => route('marketing.leads.index'), 'actionLabel' => 'القائمة'])

<form action="{{ route('marketing.leads.store') }}" method="POST" class="bg-white rounded-2xl shadow-lg border p-5 sm:p-6 space-y-4 font-tajawal max-w-2xl">
    @csrf
    <div><label class="{{ $label }}">الاسم (اختياري)</label><input name="name" class="{{ $input }}" value="{{ old('name') }}"></div>
    <div><label class="{{ $label }}">الهاتف *</label><input name="phone" required class="{{ $input }}" value="{{ old('phone') }}"></div>
    <div><label class="{{ $label }}">البريد</label><input type="email" name="email" class="{{ $input }}" value="{{ old('email') }}"></div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="{{ $label }}">الحملة</label><select name="marketing_campaign_id" class="{{ $input }}"><option value="">—</option>@foreach($campaigns as $c)<option value="{{ $c->id }}" @selected(old('marketing_campaign_id', $prefillCampaign)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="{{ $label }}">المصدر</label><select name="lead_source" class="{{ $input }}">@foreach($leadSources as $k=>$l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
    </div>
    <div><label class="{{ $label }}">ملاحظات</label><textarea name="notes" rows="2" class="{{ $input }}">{{ old('notes') }}</textarea></div>
    <button type="submit" class="px-8 py-3 rounded-xl text-white font-semibold" style="background:{{ $themeColor }}">حفظ</button>
</form>
@endsection
