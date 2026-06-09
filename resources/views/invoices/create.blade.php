@extends('layouts.app')

@php
    $isFinancial = request()->routeIs('financial-invoices.*');
    $indexRoute = $isFinancial ? route('financial-invoices.index') : route('invoices.index');
    $storeRoute = $isFinancial ? route('financial-invoices.store') : route('invoices.store');
    $pageTitle = $isFinancial ? 'فاتورة مالية جديدة' : 'فاتورة مبيعات جديدة';
    $currencySymbol = \App\Helpers\SettingsHelper::getCurrencySymbol();
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal flex items-center justify-between';
    $inputClass = 'w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:border-transparent';
@endphp

@section('page-title', $pageTitle)

@section('content')
@include('accounting.partials.context')

@include('crm.partials.page-header', [
    'title' => $pageTitle,
    'subtitle' => 'أدخل بيانات الفاتورة وبنودها ثم احفظها',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => $indexRoute,
    'actionLabel' => 'العودة للفواتير',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />',
])

@include('accounting.partials.nav')

<form id="invoiceForm" class="font-tajawal space-y-6">
    @csrf

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
            <span>المعلومات الأساسية</span>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">رقم الفاتورة</label>
                <input type="text" name="invoice_number" value="{{ $invoiceNumber }}" readonly
                       class="{{ $inputClass }} bg-gray-50 text-gray-700 font-semibold tabular-nums">
            </div>
            <div>
                @include('partials.client-search-select', [
                    'required' => true,
                    'value' => old('client_id'),
                    'inputClass' => $inputClass,
                    'crmScope' => false,
                ])
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">المشروع</label>
                <select name="project_id" class="{{ $inputClass }}">
                    <option value="">اختياري — بدون مشروع</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">تاريخ الإصدار <span class="text-red-500">*</span></label>
                <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required class="{{ $inputClass }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">تاريخ الاستحقاق <span class="text-red-500">*</span></label>
                <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="{{ $inputClass }}">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
            <span>بنود الفاتورة</span>
            <button type="button" onclick="addInvoiceItem()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-white text-xs font-semibold shadow-md hover:shadow-lg transition-all"
                    style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                إضافة بند
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[640px]">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="p-3 text-right w-10">#</th>
                        <th class="p-3 text-right">الوصف</th>
                        <th class="p-3 text-center w-28">الكمية</th>
                        <th class="p-3 text-center w-36">السعر</th>
                        <th class="p-3 text-center w-32">الإجمالي</th>
                        <th class="p-3 w-12"></th>
                    </tr>
                </thead>
                <tbody id="invoiceItems" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
                <span>ملاحظات</span>
            </div>
            <div class="p-5">
                <textarea name="notes" rows="6" class="{{ $inputClass }} resize-none"
                          placeholder="ملاحظات إضافية على الفاتورة...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
                <span>الإجماليات</span>
            </div>
            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">المجموع الفرعي</span>
                    <span id="subtotalDisplay" class="font-bold text-gray-900 tabular-nums">0 {{ $currencySymbol }}</span>
                </div>
                <div class="flex items-center justify-between gap-4 py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">نسبة الضريبة %</span>
                    <input type="number" name="tax_rate" value="{{ old('tax_rate', 0) }}" min="0" max="100" step="0.01"
                           oninput="calculateTotals()" class="w-28 px-3 py-2 border border-gray-200 rounded-xl text-sm text-center tabular-nums">
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">قيمة الضريبة</span>
                    <span id="taxDisplay" class="font-bold text-gray-900 tabular-nums">0 {{ $currencySymbol }}</span>
                </div>
                <div class="flex items-center justify-between gap-4 py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">الخصم</span>
                    <input type="number" name="discount_amount" value="{{ old('discount_amount', 0) }}" min="0" step="0.01"
                           oninput="calculateTotals()" class="w-28 px-3 py-2 border border-gray-200 rounded-xl text-sm text-center tabular-nums">
                </div>
                <div class="flex items-center justify-between pt-3 rounded-xl px-4 py-3" style="background: {{ $themeColor }}10;">
                    <span class="font-bold text-gray-900">الإجمالي النهائي</span>
                    <span id="totalDisplay" class="text-xl font-bold tabular-nums" style="color: {{ $themeColor }};">0 {{ $currencySymbol }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 pb-2">
        <a href="{{ $indexRoute }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            إلغاء
        </a>
        <button type="submit" id="submitBtn"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            حفظ الفاتورة
        </button>
    </div>
</form>

<script>
let itemCounter = 0;
const currencySymbol = @json($currencySymbol);
const storeRoute = @json($storeRoute);
const indexRoute = @json($indexRoute);
const cellInput = 'w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:border-transparent';
const numInput = cellInput + ' text-center tabular-nums';

document.addEventListener('DOMContentLoaded', () => {
    addInvoiceItem();
});

function addInvoiceItem() {
    itemCounter++;
    const row = document.createElement('tr');
    row.className = 'invoice-item hover:bg-gray-50/60';
    row.dataset.item = itemCounter;
    row.innerHTML = `
        <td class="p-3 text-gray-500 font-medium item-num"></td>
        <td class="p-3">
            <input type="text" name="items[${itemCounter}][description]" required
                   class="${cellInput}" placeholder="وصف البند">
        </td>
        <td class="p-3">
            <input type="number" name="items[${itemCounter}][quantity]" value="1" min="0" step="0.01" required
                   class="item-quantity ${numInput}" oninput="calculateTotals()">
        </td>
        <td class="p-3">
            <input type="number" name="items[${itemCounter}][unit_price]" value="0" min="0" step="0.01" required
                   class="item-price ${numInput}" oninput="calculateTotals()">
        </td>
        <td class="p-3 text-center font-semibold text-gray-900 tabular-nums item-total">0</td>
        <td class="p-3 text-center">
            <button type="button" onclick="removeInvoiceItem(${itemCounter})"
                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </td>
    `;
    document.getElementById('invoiceItems').appendChild(row);
    renumberItems();
    calculateTotals();
}

function removeInvoiceItem(id) {
    if (document.querySelectorAll('.invoice-item').length <= 1) {
        alert('يجب أن تحتوي الفاتورة على بند واحد على الأقل.');
        return;
    }
    document.querySelector(`[data-item="${id}"]`)?.remove();
    renumberItems();
    calculateTotals();
}

function renumberItems() {
    document.querySelectorAll('.invoice-item').forEach((row, i) => {
        row.querySelector('.item-num').textContent = i + 1;
    });
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.invoice-item').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
        const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
        const line = qty * price;
        subtotal += line;
        const totalCell = row.querySelector('.item-total');
        if (totalCell) totalCell.textContent = line.toFixed(2);
    });

    const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]')?.value) || 0;
    const discount = parseFloat(document.querySelector('input[name="discount_amount"]')?.value) || 0;
    const tax = (subtotal * taxRate) / 100;
    const total = Math.max(0, subtotal + tax - discount);

    document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' ' + currencySymbol;
    document.getElementById('taxDisplay').textContent = tax.toFixed(2) + ' ' + currencySymbol;
    document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' ' + currencySymbol;
}

function notify(message, type) {
    const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-blue-600' };
    const el = document.createElement('div');
    el.className = `fixed top-4 left-4 z-[100] px-5 py-3 rounded-xl shadow-lg text-white text-sm font-tajawal ${colors[type] || colors.info}`;
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!document.querySelectorAll('.invoice-item').length) {
        notify('أضف بنداً واحداً على الأقل.', 'error');
        return;
    }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;

    fetch(storeRoute, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: new FormData(this),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            notify(data.message || 'تم إنشاء الفاتورة بنجاح', 'success');
            setTimeout(() => { window.location.href = indexRoute; }, 800);
        } else {
            btn.disabled = false;
            notify(data.message || data.error || 'حدث خطأ أثناء الحفظ', 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        notify('حدث خطأ في الاتصال', 'error');
    });
});
</script>
@endsection
