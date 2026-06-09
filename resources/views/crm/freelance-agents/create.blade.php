@extends('layouts.app')
@section('page-title', 'عقد وكيل جديد')
@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
@include('crm.partials.page-header', ['title' => 'تسجيل وكيل عقاري مستقل', 'subtitle' => 'Freelance Agent Agreement — يُفعّل هيكل العمولات تلقائياً', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />'])
<form method="POST" action="{{ route('crm.freelance-agents.store') }}" class="space-y-6">@csrf
@include('crm.freelance-agents.partials.form')
<div class="flex gap-3">
    <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal" style="background:linear-gradient(135deg,{{ $themeColor }} 0%,{{ $themeColor }}dd 100%);">حفظ العقد</button>
    <a href="{{ route('crm.freelance-agents.index') }}" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-sm font-semibold font-tajawal">إلغاء</a>
</div>
</form>
@endsection
