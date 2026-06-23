@extends('layouts.developer')
@section('page-title', 'إضافة سابقة أعمال')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'إضافة لمشروع سابق',
    'subtitle' => 'سجّل مشروعاً من سابقة أعمال المطور',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>',
    'secondaryUrl' => route('developer.portfolio.index'),
    'secondaryLabel' => 'قائمة السجل',
])

<form method="POST" action="{{ route('developer.portfolio.store') }}" class="space-y-6">
    @csrf
    @include('developer-portal.portfolio.partials.form', ['themeColor' => $themeColor])
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <a href="{{ route('developer.portfolio.index') }}"
           class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
        <button type="submit"
                class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">حفظ</button>
    </div>
</form>
@endsection
