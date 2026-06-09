@extends('layouts.app')
@section('page-title', 'تعديل مطور')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'تعديل: ' . $developer->name,
    'subtitle' => 'تحديث بيانات المطور والتعاقد وحساب بوابة الدخول',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
])

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5">
    <p class="font-bold text-red-800 font-tajawal mb-2">يرجى تصحيح الأخطاء التالية:</p>
    <ul class="list-disc pr-5 text-sm text-red-700 space-y-1 font-tajawal">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.developers.update', $developer) }}" class="w-full space-y-6">@csrf @method('PUT')
@include('admin.developers.partials.form', ['developer' => $developer])
<div class="flex flex-col sm:flex-row gap-3">
    <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal shadow-md hover:shadow-lg transition-all"
            style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">حفظ التعديلات</button>
    <a href="{{ route('admin.developers.show', $developer) }}" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 font-tajawal text-center">رجوع</a>
</div>
</form>
@endsection
