@extends('layouts.app')
@section('page-title', 'إضافة عميل')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $activeTab = request('tab', old('tab', 'manual'));
    $clientsRoutePrefix = $clientsRoutePrefix ?? 'operations.clients';
    $cr = fn (string $action, mixed $params = []) => route($clientsRoutePrefix . '.' . $action, $params);
    $client = $client ?? new \App\Models\Client();
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

@include('crm.partials.page-header', [
    'title' => 'إضافة عملاء / Leads',
    'subtitle' => 'إدخال يدوي أو استيراد من ملف Excel / CSV',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => $cr('index', ['view' => 'data']),
    'actionLabel' => 'قائمة العملاء',
])

<div class="mb-6 flex flex-wrap gap-2">
    <a href="{{ $cr('create', ['tab' => 'manual']) }}"
       class="px-5 py-2.5 rounded-xl text-sm font-bold font-tajawal border-2 transition {{ $activeTab === 'manual' ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 bg-white' }}"
       @if($activeTab === 'manual') style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);" @endif>
        إدخال يدوي
    </a>
    <a href="{{ $cr('create', ['tab' => 'import']) }}"
       class="px-5 py-2.5 rounded-xl text-sm font-bold font-tajawal border-2 transition {{ $activeTab === 'import' ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 bg-white' }}"
       @if($activeTab === 'import') style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);" @endif>
        استيراد من ملف
    </a>
</div>

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>
@endif
@php $importResult = session('import_result'); @endphp
@if($importResult && !empty($importResult['errors']))
<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm font-tajawal">
    <p class="font-bold text-amber-900 mb-2">تفاصيل الصفوف الفاشلة:</p>
    <ul class="space-y-1 text-amber-800 max-h-40 overflow-y-auto">
        @foreach(array_slice($importResult['errors'], 0, 10) as $err)
        <li>صف {{ $err['row'] ?? '—' }}: {{ $err['message'] ?? '' }}</li>
        @endforeach
    </ul>
</div>
@endif

@if($activeTab === 'import')
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900 flex flex-wrap items-center justify-between gap-3"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <span>استيراد Leads من Excel أو CSV</span>
        <a href="{{ $cr('import.template') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white font-tajawal"
           style="background: {{ $themeColor }};">
            تنزيل القالب
        </a>
    </div>
    <div class="p-5 sm:p-6 space-y-5">
        <form action="{{ $cr('import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="{{ $label }}">ملف Excel أو CSV *</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv,.txt" required class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">إذا وُجد نفس رقم الهاتف مسبقاً</label>
                <select name="duplicate_mode" class="{{ $input }} max-w-md">
                    <option value="skip" @selected(old('duplicate_mode', 'skip') === 'skip')>تخطي</option>
                    <option value="update" @selected(old('duplicate_mode') === 'update')>تحديث</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <a href="{{ $cr('index', ['view' => 'data']) }}" class="inline-flex justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
                <button type="submit" class="inline-flex justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                        style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">رفع واستيراد</button>
            </div>
        </form>
    </div>
</div>
@else
<form action="{{ $cr('store') }}" method="POST" class="w-full space-y-6">
    @csrf
    @include('crm.clients.partials.form', [
        'client' => $client,
        'marketingCampaigns' => $marketingCampaigns ?? collect(),
        'themeColor' => $themeColor,
        'input' => $input,
        'label' => $label,
        'clientsRoutePrefix' => $clientsRoutePrefix,
    ])
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
        <a href="{{ $cr('index', ['view' => 'data']) }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">حفظ العميل</button>
    </div>
</form>
@endif
@endsection
