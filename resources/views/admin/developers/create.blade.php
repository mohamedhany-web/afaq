@extends('layouts.app')
@section('page-title', 'مطور جديد')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'إضافة مطور عقاري وتعاقد',
    'subtitle' => 'سجّل بيانات المطور والتعاقد وحساب بوابة الدخول في خطوة واحدة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
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

<form method="POST" action="{{ route('admin.developers.store') }}" class="w-full space-y-6">@csrf
@include('admin.developers.partials.form')
<div class="flex flex-col sm:flex-row gap-3">
    <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal shadow-md hover:shadow-lg transition-all"
            style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">حفظ وإنشاء البوابة</button>
    <a href="{{ route('admin.developers.index') }}" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 font-tajawal text-center">إلغاء</a>
</div>
</form>
@endsection
