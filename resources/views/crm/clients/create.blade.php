@extends('layouts.app')
@section('page-title', 'إضافة عميل')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $activeTab = request('tab', old('tab', 'manual'));
@endphp

@include('crm.partials.page-header', [
    'title' => ($requiresApproval ?? false) ? 'طلب إضافة عميل' : 'إضافة عملاء / Leads',
    'subtitle' => 'إدخال يدوي أو استيراد من ملف Excel / CSV',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => auth()->user()->clientsHubUrl(),
    'actionLabel' => 'قائمة العملاء',
])

@if($requiresApproval ?? false)
<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900 font-tajawal">
    سيتم إرسال بيانات العميل للإدارة للموافقة قبل إضافته إلى النظام.
</div>
@endif

<div class="mb-6 flex flex-wrap gap-2">
    <a href="{{ route('crm.clients.create', ['tab' => 'manual']) }}"
       class="px-5 py-2.5 rounded-xl text-sm font-bold font-tajawal border-2 transition {{ $activeTab === 'manual' ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 bg-white' }}"
       @if($activeTab === 'manual') style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);" @endif>
        إدخال يدوي
    </a>
    <a href="{{ route('crm.clients.create', ['tab' => 'import']) }}"
       class="px-5 py-2.5 rounded-xl text-sm font-bold font-tajawal border-2 transition {{ $activeTab === 'import' ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 bg-white' }}"
       @if($activeTab === 'import') style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);" @endif>
        استيراد من ملف
    </a>
</div>

@if($activeTab === 'import')
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900 flex flex-wrap items-center justify-between gap-3"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <span>استيراد Leads من Excel أو CSV</span>
        <a href="{{ route('crm.clients.import.template') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white font-tajawal"
           style="background: {{ $themeColor }};">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            تنزيل القالب
        </a>
    </div>
    <div class="p-5 sm:p-6 space-y-5">
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-900 font-tajawal space-y-1">
            <p class="font-bold">أهم الأعمدة في القالب:</p>
            <p><strong>الهاتف</strong> — مطلوب · <strong>الاسم</strong> — اختياري (يُقبل الملف بدونه)</p>
            <p>أعمدة إضافية: البريد، الشركة، العنوان، ملاحظات</p>
            <p class="text-xs text-blue-700 mt-2">يمكنك رفع ملف بنفس ترتيب القالب أو بعناوين مشابهة (عربي/إنجليزي).</p>
        </div>

        @if($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 font-tajawal">
            <ul class="list-disc pr-5 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('crm.clients.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="{{ $label }}">ملف Excel أو CSV *</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv,.txt" required
                       class="{{ $input }} file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:text-white"
                       style="file:background: {{ $themeColor }};">
            </div>
            <div>
                <label class="{{ $label }}">إذا وُجد نفس رقم الهاتف مسبقاً</label>
                <select name="duplicate_mode" class="{{ $input }} max-w-md">
                    <option value="skip" @selected(old('duplicate_mode', 'skip') === 'skip')>تخطي — لا تُضاف مرة أخرى</option>
                    <option value="update" @selected(old('duplicate_mode') === 'update')>تحديث — إكمال البيانات الناقصة</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                <a href="{{ auth()->user()->clientsHubUrl() }}" class="inline-flex justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
                <button type="submit" class="inline-flex justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                        style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                    رفع واستيراد العملاء
                </button>
            </div>
        </form>
    </div>
</div>
@else
<form action="{{ route('crm.clients.store') }}" method="POST" class="w-full space-y-6">
    @csrf
    @include('crm.clients.partials.form')
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
        <a href="{{ auth()->user()->clientsHubUrl() }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">
            إلغاء والعودة للعملاء
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            {{ ($requiresApproval ?? false) ? 'إرسال طلب الإضافة' : 'حفظ العميل' }}
        </button>
    </div>
</form>
@endif
@endsection
