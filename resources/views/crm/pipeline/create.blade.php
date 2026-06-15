@extends('layouts.app')
@section('page-title', 'صفقة جديدة')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
@endphp

@include('crm.partials.page-header', [
    'title' => 'صفقة جديدة',
    'subtitle' => 'إضافة صفقة إلى مسار المبيعات — القيم بالجنيه المصري',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])

<form action="{{ route('crm.pipeline.store') }}" method="POST" class="w-full space-y-6">
    @csrf

    {{-- العميل والمشروع --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
             style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            بيانات العميل والمشروع
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <div class="md:col-span-1 xl:col-span-1">
                @include('partials.client-search-select', [
                    'required' => true,
                    'value' => old('client_id', request('client_id')),
                    'inputClass' => $input,
                    'crmScope' => true,
                ])
            </div>
            <div class="md:col-span-1 xl:col-span-1">
                <label class="{{ $label }}">المشروع العقاري</label>
                <select name="project_id" class="{{ $input }}">
                    <option value="">— بدون مشروع —</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(old('project_id', request('project_id')) == $p->id)>{{ $p->name }} @if($p->city)({{ $p->city }})@endif</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 xl:col-span-1">
                <label class="{{ $label }}">مصدر العميل</label>
                <select name="lead_source" class="{{ $input }}">
                    <option value="">—</option>
                    @foreach(\App\Models\Client::leadSourceLabels() as $val => $txt)
                        <option value="{{ $val }}" @selected(old('lead_source') == $val)>{{ $txt }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- تفاصيل الصفقة --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
             style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            تفاصيل الصفقة
        </div>
        <div class="p-5 sm:p-6 space-y-4 sm:space-y-6">
            <div>
                <label class="{{ $label }}">وصف الصفقة *</label>
                <input name="product_service" value="{{ old('product_service', request('product_service')) }}" required class="{{ $input }}"
                       placeholder="مثال: شقة 3 غرف — الدور الخامس — برج النخيل">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div>
                    <label class="{{ $label }}">القيمة المتوقعة (ج.م) *</label>
                    <input name="estimated_value" type="number" step="0.01" min="0" value="{{ old('estimated_value', request('estimated_value')) }}" required class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">احتمالية الإغلاق % *</label>
                    <input name="probability_percentage" type="number" min="0" max="100" value="{{ old('probability_percentage', 50) }}" required class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">نوع الوحدة</label>
                    <input name="unit_type" value="{{ old('unit_type') }}" class="{{ $input }}" placeholder="شقة / فيلا / محل">
                </div>
                <div>
                    <label class="{{ $label }}">نوع الاهتمام</label>
                    <input name="interest_type" value="{{ old('interest_type') }}" class="{{ $input }}" placeholder="شراء / استثمار">
                </div>
            </div>
        </div>
    </div>

    {{-- المرحلة والمواعيد --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
             style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
            المرحلة والمواعيد
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div>
                <label class="{{ $label }}">مرحلة الصفقة</label>
                <select name="stage" class="{{ $input }}">
                    @foreach($stages as $s)
                        <option value="{{ $s }}" @selected(old('stage', 'lead') == $s)>{{ $stageLabels[$s] ?? $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">تاريخ الإغلاق المتوقع</label>
                <input name="expected_close_date" type="date" value="{{ old('expected_close_date') }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">موعد المعاينة</label>
                <input name="viewing_date" type="date" value="{{ old('viewing_date') }}" class="{{ $input }}">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="{{ $label }}">ملاحظات المعاينة</label>
                <input name="viewing_notes" value="{{ old('viewing_notes') }}" class="{{ $input }}" placeholder="تفاصيل موعد المعاينة...">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="{{ $label }}">ملاحظات عامة</label>
                <textarea name="notes" rows="4" class="{{ $input }} resize-none">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    {{-- أزرار --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
        <a href="{{ route('crm.pipeline.index') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">
            إلغاء والعودة للمسار
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            حفظ الصفقة
        </button>
    </div>
</form>
@endsection
