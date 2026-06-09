@extends('layouts.app')

@php
    $currencySymbol = \App\Helpers\SettingsHelper::getCurrencySymbol();
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal flex items-center justify-between';
    $inputClass = 'w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:border-transparent font-tajawal';
    $categories = [
        'office_supplies' => 'مستلزمات مكتبية',
        'utilities' => 'مرافق (كهرباء، ماء، إنترنت)',
        'rent' => 'إيجار',
        'salaries' => 'رواتب',
        'marketing' => 'تسويق',
        'travel' => 'سفر',
        'maintenance' => 'صيانة',
        'software' => 'برمجيات',
        'professional_fees' => 'رسوم مهنية',
        'insurance' => 'تأمين',
        'taxes' => 'ضرائب',
        'other' => 'أخرى',
    ];
@endphp

@section('page-title', 'مصروف جديد')

@section('content')
@include('accounting.partials.context')

@include('crm.partials.page-header', [
    'title' => 'مصروف جديد',
    'subtitle' => 'تسجيل مصروف تشغيلي جديد وإرساله للموافقة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => route('expenses.index'),
    'actionLabel' => 'العودة للمصروفات',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />',
])

@include('accounting.partials.nav')

<form action="{{ route('expenses.store') }}" method="POST" class="font-tajawal space-y-6">
    @csrf

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
            <span>معلومات المصروف</span>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="md:col-span-2 lg:col-span-3">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">الوصف <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="3" required
                          class="{{ $inputClass }} resize-none @error('description') border-red-500 @enderror"
                          placeholder="وصف المصروف...">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="expense_category" class="block text-sm font-medium text-gray-700 mb-1.5">الفئة <span class="text-red-500">*</span></label>
                <select name="expense_category" id="expense_category" required
                        class="{{ $inputClass }} @error('expense_category') border-red-500 @enderror">
                    <option value="">اختر فئة المصروف</option>
                    @foreach($categories as $value => $label)
                    <option value="{{ $value }}" @selected(old('expense_category') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('expense_category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1.5">المورد</label>
                <select name="vendor_id" id="vendor_id" class="{{ $inputClass }}">
                    <option value="">اختياري — بدون مورد</option>
                    @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" @selected(old('vendor_id') == $vendor->id)>{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">المبلغ <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                           class="{{ $inputClass }} pl-3 pr-14 tabular-nums @error('amount') border-red-500 @enderror"
                           placeholder="0.00">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">{{ $currencySymbol }}</span>
                </div>
                @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1.5">تاريخ المصروف <span class="text-red-500">*</span></label>
                <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                       class="{{ $inputClass }} @error('expense_date') border-red-500 @enderror">
                @error('expense_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1.5">طريقة الدفع <span class="text-red-500">*</span></label>
                <select name="payment_method" id="payment_method" required
                        class="{{ $inputClass }} @error('payment_method') border-red-500 @enderror">
                    <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>نقدي</option>
                    <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>تحويل بنكي</option>
                    <option value="check" @selected(old('payment_method') === 'check')>شيك</option>
                    <option value="credit_card" @selected(old('payment_method') === 'credit_card')>بطاقة ائتمان</option>
                </select>
                @error('payment_method')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="rounded-2xl border px-5 py-4 text-sm font-tajawal flex items-start gap-3"
         style="background: {{ $themeColor }}08; border-color: {{ $themeColor }}25; color: #374151;">
        <svg class="w-5 h-5 shrink-0 mt-0.5" style="color: {{ $themeColor }};" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>يُحفظ المصروف بحالة <strong>معلق</strong> حتى تتم الموافقة عليه من الإدارة المالية.</p>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 pb-2">
        <a href="{{ route('expenses.index') }}"
           class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            إلغاء
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            إضافة المصروف
        </button>
    </div>
</form>
@endsection
