@extends('layouts.developer')
@section('page-title', 'تعديل سابقة أعمال')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'تعديل: ' . $portfolio->title,
    'subtitle' => 'تحديث بيانات المشروع في سابقة الأعمال',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
    'secondaryUrl' => route('developer.portfolio.index'),
    'secondaryLabel' => 'قائمة السجل',
])

<form method="POST" action="{{ route('developer.portfolio.update', $portfolio) }}" class="space-y-6">
    @csrf @method('PUT')
    @include('developer-portal.portfolio.partials.form', ['portfolio' => $portfolio, 'themeColor' => $themeColor])
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <a href="{{ route('developer.portfolio.index') }}"
           class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
        <button type="submit"
                class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">حفظ التعديلات</button>
    </div>
</form>
@endsection
