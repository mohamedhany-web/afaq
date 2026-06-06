@extends('layouts.app')
@section('page-title', 'حملة جديدة')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
@include('crm.partials.page-header', ['title' => 'إنشاء حملة تسويقية', 'subtitle' => 'قسم التسويق', 'actionUrl' => route('marketing.campaigns.index'), 'actionLabel' => 'العودة'])

<form action="{{ route('marketing.campaigns.store') }}" method="POST" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 space-y-4">
    @csrf
    @include('marketing.campaigns.partials.form')
    <button type="submit" class="px-8 py-3 rounded-xl text-white font-semibold text-sm font-tajawal" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">حفظ الحملة</button>
</form>
@endsection
