@extends('layouts.app')
@section('page-title', 'تعديل عقد وكيل')
@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
@include('crm.partials.page-header', ['title' => 'تعديل عقد: ' . $contract->user?->name, 'subtitle' => 'تحديث بيانات العقد والتارجت', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />'])
<form method="POST" action="{{ route('crm.freelance-agents.update', $contract) }}" class="space-y-6">@csrf @method('PUT')
@include('crm.freelance-agents.partials.form', ['contract' => $contract])
<div class="flex gap-3">
    <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal" style="background:linear-gradient(135deg,{{ $themeColor }} 0%,{{ $themeColor }}dd 100%);">حفظ</button>
    <a href="{{ route('crm.freelance-agents.show', $contract) }}" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-sm font-semibold font-tajawal">رجوع</a>
</div>
</form>
@endsection
