@extends('layouts.app')
@section('page-title', 'تعديل حملة')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
@include('crm.partials.page-header', ['title' => 'تعديل: ' . $campaign->name, 'actionUrl' => route('marketing.campaigns.show', $campaign), 'actionLabel' => 'عرض الحملة'])

<form action="{{ route('marketing.campaigns.update', $campaign) }}" method="POST" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 space-y-4">
    @csrf @method('PUT')
    @include('marketing.campaigns.partials.form')
    <button type="submit" class="px-8 py-3 rounded-xl text-white font-semibold text-sm font-tajawal" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">حفظ التعديلات</button>
</form>
@endsection
