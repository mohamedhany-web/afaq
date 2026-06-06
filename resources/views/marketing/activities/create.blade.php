@extends('layouts.app')
@section('page-title', 'مهمة تسويقية')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
@endphp

@include('crm.partials.page-header', ['title' => 'مهمة تسويقية جديدة', 'actionUrl' => route('marketing.activities.index'), 'actionLabel' => 'الجدول'])

<form action="{{ route('marketing.activities.store') }}" method="POST" class="bg-white rounded-2xl shadow-lg border p-5 sm:p-6 space-y-4 font-tajawal">
    @csrf
    <div><label class="{{ $label }}">العنوان *</label><input name="title" required class="{{ $input }}" value="{{ old('title') }}"></div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="{{ $label }}">النوع</label><select name="type" class="{{ $input }}">@foreach($types as $k=>$l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
        <div><label class="{{ $label }}">الأولوية</label><select name="priority" class="{{ $input }}">@foreach($priorities as $k=>$l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
        <div><label class="{{ $label }}">الحالة</label><select name="status" class="{{ $input }}">@foreach($statuses as $k=>$l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
        <div><label class="{{ $label }}">الحملة</label><select name="campaign_id" class="{{ $input }}"><option value="">—</option>@foreach($campaigns as $c)<option value="{{ $c->id }}" @selected(old('campaign_id', $prefillCampaign) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="{{ $label }}">المسؤول</label><select name="assigned_to" class="{{ $input }}"><option value="">أنا</option>@foreach($assignableUsers as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
        <div><label class="{{ $label }}">موعد التنفيذ</label><input type="datetime-local" name="due_at" class="{{ $input }}" value="{{ old('due_at') }}"></div>
        <div><label class="{{ $label }}">التكرار الدوري</label><select name="recurrence" class="{{ $input }}">@foreach($recurrences as $k=>$l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
        <div><label class="{{ $label }}">كل (فترة)</label><input type="number" name="recurrence_interval" min="1" max="12" value="{{ old('recurrence_interval', 1) }}" class="{{ $input }}"></div>
    </div>
    <div><label class="{{ $label }}">الوصف</label><textarea name="description" rows="3" class="{{ $input }}">{{ old('description') }}</textarea></div>
    <button type="submit" class="px-8 py-3 rounded-xl text-white font-semibold" style="background:{{ $themeColor }}">حفظ</button>
</form>
@endsection
