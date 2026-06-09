@extends('layouts.app')

@php
    $currencySymbol = \App\Helpers\SettingsHelper::getCurrencySymbol();
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal flex items-center justify-between';
    $inputClass = 'w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:border-transparent font-tajawal';
@endphp

@section('page-title', 'إضافة دفعة جديدة')

@section('content')
@include('accounting.partials.context')

@include('crm.partials.page-header', [
    'title' => 'إضافة دفعة جديدة',
    'subtitle' => 'تسجيل دفعة مالية واردة أو صادرة وربطها بالفاتورة أو الموظف',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => route('payments.index'),
    'actionLabel' => 'العودة للمدفوعات',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />',
])

@include('accounting.partials.nav')

<form id="paymentForm" class="font-tajawal space-y-6">
    @csrf

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
            <span>المعلومات الأساسية</span>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">رقم الدفعة</label>
                <input type="text" name="payment_number" value="{{ $paymentNumber }}" readonly
                       class="{{ $inputClass }} bg-gray-50 text-gray-700 font-semibold tabular-nums">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">تاريخ الدفعة <span class="text-red-500">*</span></label>
                <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="{{ $inputClass }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">نوع الدفعة <span class="text-red-500">*</span></label>
                <select name="payment_type" required onchange="updatePaymentTypeUI(this.value)" class="{{ $inputClass }}">
                    <option value="">اختر نوع الدفعة</option>
                    <option value="invoice" @selected(old('payment_type') === 'invoice')>دفعة فاتورة (من العميل)</option>
                    <option value="salary" @selected(old('payment_type') === 'salary')>دفعة راتب (للموظف)</option>
                    <option value="expense" @selected(old('payment_type') === 'expense')>دفعة مصروف</option>
                    <option value="other" @selected(old('payment_type') === 'other')>دفعة أخرى</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">المبلغ <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" name="amount" step="0.01" min="0" required value="{{ old('amount') }}"
                           class="{{ $inputClass }} pl-3 pr-14 tabular-nums" placeholder="0.00">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">{{ $currencySymbol }}</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">طريقة الدفع <span class="text-red-500">*</span></label>
                <select name="payment_method" required id="payment_method" onchange="updateBankAccountField()" class="{{ $inputClass }}">
                    <option value="">اختر طريقة الدفع</option>
                    <option value="cash" @selected(old('payment_method') === 'cash')>نقدي</option>
                    <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>تحويل بنكي</option>
                    <option value="check" @selected(old('payment_method') === 'check')>شيك</option>
                    <option value="credit_card" @selected(old('payment_method') === 'credit_card')>بطاقة ائتمان</option>
                    <option value="online" @selected(old('payment_method') === 'online')>دفع إلكتروني</option>
                </select>
            </div>
            <div id="bank_account_field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">حساب البنك</label>
                <select name="bank_account_id" class="{{ $inputClass }}">
                    <option value="">اختر حساب البنك</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}" @selected(old('bank_account_id') == $account->id)>{{ $account->code }} — {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">رقم المرجع</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                       class="{{ $inputClass }}" placeholder="رقم المرجع أو الشيك">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
            <span>معلومات الربط</span>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div id="invoice_field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">الفاتورة المرتبطة</label>
                <select name="invoice_id" id="invoice_id" onchange="updateClientFromInvoice()" class="{{ $inputClass }}">
                    <option value="">لا يوجد (اختياري)</option>
                    @forelse($invoices as $invoice)
                    <option value="{{ $invoice->id }}"
                            data-client-id="{{ $invoice->client_id ?? '' }}"
                            data-client-label="{{ $invoice->client?->name ?? '' }}"
                            data-balance="{{ $invoice->balance_due ?? $invoice->total_amount }}"
                            @selected(old('invoice_id') == $invoice->id)>
                        {{ $invoice->invoice_number }} — {{ $invoice->client?->name ?? 'بدون عميل' }} — {{ $money($invoice->balance_due ?? $invoice->total_amount) }}
                        @if($invoice->payment_status === 'paid') (مدفوعة)
                        @elseif($invoice->payment_status === 'partial') (مدفوعة جزئياً)
                        @endif
                    </option>
                    @empty
                    <option value="" disabled>لا توجد فواتير متاحة</option>
                    @endforelse
                </select>
                @if($invoices->isEmpty())
                <p class="mt-2 text-xs text-amber-700 flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2">
                    <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>لا توجد فواتير متاحة. يمكنك إنشاء فاتورة من <a href="{{ route('financial-invoices.create') }}" class="font-bold underline" style="color:{{ $themeColor }}">الفواتير المالية</a>.</span>
                </p>
                @endif
            </div>

            <div id="client_field">
                <span id="client_required" class="text-red-500 text-sm hidden">*</span>
                @include('partials.client-search-select', [
                    'required' => false,
                    'value' => old('client_id'),
                    'inputClass' => $inputClass,
                    'crmScope' => false,
                ])
            </div>

            <div id="employee_field" class="hidden md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">الموظف</label>
                <select name="employee_id" class="{{ $inputClass }}">
                    <option value="">اختر الموظف (اختياري)</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
                <span>الوصف <span class="text-red-500 text-sm font-normal">*</span></span>
            </div>
            <div class="p-5">
                <textarea name="description" rows="6" required
                          class="{{ $inputClass }} resize-none"
                          placeholder="وصف الدفعة...">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
                <span>ملاحظات إضافية</span>
            </div>
            <div class="p-5">
                <textarea name="notes" rows="6"
                          class="{{ $inputClass }} resize-none"
                          placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 pb-2">
        <a href="{{ route('payments.index') }}"
           class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            إلغاء
        </a>
        <button type="submit" id="submitBtn"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all disabled:opacity-60"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            حفظ الدفعة
        </button>
    </div>
</form>

<script>
const storeRoute = @json(route('payments.store'));
const indexRoute = @json(route('payments.index'));

function setClientSearchValue(containerId, clientId, clientLabel) {
    const root = document.getElementById(containerId);
    if (!root) return;
    const el = root.querySelector('[x-data]');
    if (el && typeof Alpine !== 'undefined' && Alpine.$data) {
        const data = Alpine.$data(el);
        if (data) {
            data.selectedId = String(clientId);
            data.selectedLabel = clientLabel || '';
            data.query = '';
            data.results = [];
            data.open = false;
        }
    }
}

function updatePaymentTypeUI(type) {
    const clientField = document.getElementById('client_field');
    const employeeField = document.getElementById('employee_field');
    const invoiceField = document.getElementById('invoice_field');
    const clientRequired = document.getElementById('client_required');

    clientField.classList.remove('hidden');
    employeeField.classList.add('hidden');
    invoiceField.classList.add('hidden');
    clientRequired.classList.add('hidden');

    document.querySelector('select[name="employee_id"]').value = '';
    document.getElementById('invoice_id').value = '';
    if (typeof clearClientSearchSelect === 'function') {
        clearClientSearchSelect('client_field');
    }

    if (type === 'invoice') {
        clientField.classList.remove('hidden');
        invoiceField.classList.remove('hidden');
        clientRequired.classList.remove('hidden');
    } else if (type === 'salary') {
        clientField.classList.remove('hidden');
        employeeField.classList.remove('hidden');
    } else if (type === 'expense' || type === 'other') {
        clientField.classList.remove('hidden');
    }
}

function updateClientFromInvoice() {
    const invoiceSelect = document.getElementById('invoice_id');
    const selected = invoiceSelect.options[invoiceSelect.selectedIndex];
    if (!selected?.value) return;

    const clientId = selected.dataset.clientId;
    const clientLabel = selected.dataset.clientLabel;
    if (clientId) {
        setClientSearchValue('client_field', clientId, clientLabel);
    }

    const balance = parseFloat(selected.dataset.balance || 0);
    const amountInput = document.querySelector('input[name="amount"]');
    if (amountInput && !amountInput.value && balance > 0) {
        amountInput.value = balance.toFixed(2);
    }
}

function updateBankAccountField() {
    const paymentMethod = document.getElementById('payment_method').value;
    const bankAccountField = document.getElementById('bank_account_field');
    const show = paymentMethod === 'bank_transfer' || paymentMethod === 'check';
    bankAccountField.classList.toggle('hidden', !show);
    if (!show) {
        document.querySelector('select[name="bank_account_id"]').value = '';
    }
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;

    const formData = new FormData(this);
    ['invoice_id', 'client_id', 'employee_id', 'bank_account_id', 'reference_number', 'notes'].forEach(field => {
        if (!formData.get(field)) formData.set(field, '');
    });

    fetch(storeRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('json');
        if (isJson) {
            const data = await response.json();
            if (data.success) {
                notify(data.message || 'تم إضافة الدفعة بنجاح', 'success');
                setTimeout(() => { window.location.href = indexRoute; }, 900);
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join(' — ') : (data.message || 'حدث خطأ');
                notify(msg, 'error');
                btn.disabled = false;
            }
        } else if (response.status === 422) {
            notify('يرجى التحقق من جميع الحقول المطلوبة', 'error');
            btn.disabled = false;
        } else {
            notify('حدث خطأ أثناء إضافة الدفعة', 'error');
            btn.disabled = false;
        }
    })
    .catch(() => {
        notify('حدث خطأ في الاتصال', 'error');
        btn.disabled = false;
    });
});

function notify(message, type) {
    const colors = { success: 'bg-green-600', error: 'bg-red-600' };
    const el = document.createElement('div');
    el.className = `fixed top-4 left-4 z-[100] px-5 py-3 rounded-xl shadow-lg text-white text-sm font-tajawal max-w-md ${colors[type] || 'bg-blue-600'}`;
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

document.addEventListener('DOMContentLoaded', () => {
    const type = document.querySelector('select[name="payment_type"]').value;
    if (type) updatePaymentTypeUI(type);
    updateBankAccountField();
});
</script>
@endsection
