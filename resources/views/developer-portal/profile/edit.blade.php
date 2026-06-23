@extends('layouts.developer')
@section('page-title', 'بيانات الشركة')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
@endphp

@include('crm.partials.page-header', [
    'title' => 'بيانات الشركة',
    'subtitle' => 'معلومات المطور المعروضة لفريق المبيعات والإدارة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0v-2a2 2 0 00-2-2H9a2 2 0 00-2 2v2m14 0H5"/>',
])

@if($developer->activeContract)
<div class="mb-6 p-4 sm:p-5 rounded-2xl bg-blue-50 border border-blue-200 text-sm text-blue-900 font-tajawal">
    <div class="font-bold mb-1">التعاقد مع أفاق</div>
    <p>المرجع: {{ $developer->activeContract->contract_ref ?? 'نشط' }} — العمولة: {{ $developer->activeContract->commission_percent ?? '—' }}%</p>
    <p class="text-xs text-blue-700 mt-1">بيانات التعاقد للقراءة فقط — تُدار من الإدارة</p>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
    <ul class="list-disc pr-5 text-sm text-red-700 font-tajawal space-y-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('developer.profile.update') }}" class="space-y-6">
    @csrf @method('PUT')
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            معلومات التواصل والملف التعريفي
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 font-tajawal">
            <div>
                <label class="{{ $label }}">اسم الشركة</label>
                <input value="{{ $developer->name }}" disabled class="{{ $input }} bg-gray-50 text-gray-500">
            </div>
            <div>
                <label class="{{ $label }}">الهاتف</label>
                <input name="phone" value="{{ old('phone', $developer->phone) }}" class="{{ $input }}" dir="ltr">
            </div>
            <div>
                <label class="{{ $label }}">البريد الإلكتروني</label>
                <input name="email" type="email" value="{{ old('email', $developer->email) }}" class="{{ $input }}" dir="ltr">
            </div>
            <div>
                <label class="{{ $label }}">الموقع الإلكتروني</label>
                <input name="website" value="{{ old('website', $developer->website) }}" class="{{ $input }}" dir="ltr" placeholder="https://">
            </div>
            <div>
                <label class="{{ $label }}">المدينة</label>
                <input name="city" value="{{ old('city', $developer->city) }}" class="{{ $input }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $label }}">العنوان</label>
                <input name="address" value="{{ old('address', $developer->address) }}" class="{{ $input }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $label }}">نبذة عن المطور</label>
                <textarea name="description" rows="5" class="{{ $input }}" placeholder="نبذة تعريفية تظهر لفريق المبيعات...">{{ old('description', $developer->description) }}</textarea>
            </div>
        </div>
    </div>
    <div class="flex justify-end">
        <button type="submit"
                class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            حفظ البيانات
        </button>
    </div>
</form>
@endsection
