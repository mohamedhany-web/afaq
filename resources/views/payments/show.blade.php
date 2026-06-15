@extends('layouts.app')

@php
    $isIncoming = $payment->payment_type === 'invoice'
        || ($payment->client_id && !in_array($payment->payment_type, ['salary', 'expense']));

    $typeName = match($payment->payment_type) {
        'invoice' => 'دفعة فاتورة',
        'salary' => 'دفعة راتب',
        'expense' => 'دفعة مصروف',
        'other' => 'دفعة أخرى',
        default => $payment->payment_type,
    };

    $methodName = match($payment->payment_method) {
        'cash' => 'نقدي',
        'bank_transfer' => 'تحويل بنكي',
        'check' => 'شيك',
        'credit_card' => 'بطاقة ائتمان',
        'online' => 'دفع إلكتروني',
        default => $payment->payment_method,
    };

    $statusName = match($payment->status) {
        'completed' => 'مكتملة',
        'pending' => 'معلقة',
        'cancelled' => 'ملغية',
        default => $payment->status,
    };

    $statusAccent = match($payment->status) {
        'completed' => 'green',
        'pending' => 'amber',
        'cancelled' => 'red',
        default => 'blue',
    };

    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';
@endphp

@section('page-title', 'دفعة ' . $payment->payment_number)

@section('content')
@include('accounting.partials.context')

@include('crm.partials.page-header', [
    'title' => 'دفعة ' . $payment->payment_number,
    'subtitle' => $typeName . ' — ' . ($payment->description ?: 'بدون وصف'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />',
    'actionUrl' => route('payments.index'),
    'actionLabel' => 'العودة للمدفوعات',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />',
])

@include('accounting.partials.nav')

@if($payment->status === 'pending')
<div class="flex flex-wrap gap-2 mb-6 font-tajawal">
    <button type="button" onclick="markAsCompleted()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        تأكيد الدفعة
    </button>
</div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', [
        'label' => 'المبلغ',
        'value' => ($isIncoming ? '+' : '-') . $money($payment->amount),
        'accent' => $isIncoming ? 'green' : 'red',
        'compact' => true,
        'href' => '#payment-details',
        'linkLabel' => 'عرض التفاصيل',
    ])
    @include('crm.partials.stat-card', ['label' => 'نوع الدفعة', 'value' => $typeName, 'accent' => $isIncoming ? 'green' : 'red', 'compact' => true, 'href' => '#payment-details', 'linkLabel' => 'عرض التفاصيل'])
    @include('crm.partials.stat-card', ['label' => 'الحالة', 'value' => $statusName, 'accent' => $statusAccent, 'compact' => true, 'href' => '#payment-details', 'linkLabel' => 'عرض التفاصيل'])
    @include('crm.partials.stat-card', ['label' => 'تاريخ الدفعة', 'value' => $payment->payment_date->format('Y/m/d'), 'accent' => 'theme', 'compact' => true, 'href' => '#payment-details', 'linkLabel' => 'عرض التفاصيل'])
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6" id="payment-details">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">تفاصيل الدفعة</div>
        <div class="p-5 sm:p-6">
            <dl class="space-y-3 text-sm font-tajawal">
                <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                    <dt class="text-gray-500 shrink-0">رقم الدفعة</dt>
                    <dd class="font-bold text-gray-900 tabular-nums">{{ $payment->payment_number }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                    <dt class="text-gray-500 shrink-0">الاتجاه</dt>
                    <dd>
                        <span class="text-xs font-bold px-2 py-1 rounded-lg {{ $isIncoming ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $isIncoming ? 'واردة' : 'صادرة' }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                    <dt class="text-gray-500 shrink-0">طريقة الدفع</dt>
                    <dd class="font-semibold text-gray-900">{{ $methodName }}</dd>
                </div>
                @if($payment->reference_number)
                <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                    <dt class="text-gray-500 shrink-0">رقم المرجع</dt>
                    <dd class="font-semibold text-gray-900 tabular-nums">{{ $payment->reference_number }}</dd>
                </div>
                @endif
                @if($payment->bankAccount)
                <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                    <dt class="text-gray-500 shrink-0">حساب البنك</dt>
                    <dd class="font-semibold text-gray-900 text-left">{{ $payment->bankAccount->code }} — {{ $payment->bankAccount->name }}</dd>
                </div>
                @endif
                @if($payment->creator)
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-gray-500 shrink-0">سجّلها</dt>
                    <dd class="font-semibold text-gray-900">{{ $payment->creator->name }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">معلومات الربط</div>
        <div class="p-5 sm:p-6">
            <dl class="space-y-3 text-sm font-tajawal">
                @if($payment->client)
                <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                    <dt class="text-gray-500 shrink-0">العميل</dt>
                    <dd class="font-semibold text-gray-900">{{ $payment->client->name }}</dd>
                </div>
                @endif
                @if($payment->employee)
                <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                    <dt class="text-gray-500 shrink-0">الموظف</dt>
                    <dd class="font-semibold text-gray-900">{{ $payment->employee->first_name }} {{ $payment->employee->last_name }}</dd>
                </div>
                @endif
                @if($payment->invoice)
                <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                    <dt class="text-gray-500 shrink-0">الفاتورة</dt>
                    <dd>
                        <a href="{{ route('financial-invoices.show', $payment->invoice) }}"
                           class="font-semibold hover:underline" style="color: {{ $themeColor }};">
                            {{ $payment->invoice->invoice_number }}
                        </a>
                    </dd>
                </div>
                <div class="flex justify-between gap-4 py-2 border-b border-gray-100">
                    <dt class="text-gray-500 shrink-0">إجمالي الفاتورة</dt>
                    <dd class="font-semibold text-gray-900 tabular-nums">{{ $money($payment->invoice->total_amount) }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2">
                    <dt class="text-gray-500 shrink-0">متبقي الفاتورة</dt>
                    <dd class="font-semibold text-gray-900 tabular-nums">{{ $money($payment->invoice->balance_due ?? 0) }}</dd>
                </div>
                @elseif(!$payment->client && !$payment->employee)
                <div class="text-center py-8 text-gray-500 text-sm rounded-xl border-2 border-dashed border-gray-200">
                    لا توجد جهة مرتبطة بهذه الدفعة.
                </div>
                @endif
            </dl>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">الوصف</div>
        <div class="p-5 sm:p-6">
            <p class="text-sm text-gray-700 font-tajawal leading-relaxed whitespace-pre-wrap">{{ $payment->description ?: '—' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">ملاحظات</div>
        <div class="p-5 sm:p-6">
            @if($payment->notes)
            <p class="text-sm text-gray-700 font-tajawal leading-relaxed whitespace-pre-wrap">{{ $payment->notes }}</p>
            @else
            <p class="text-sm text-gray-400 font-tajawal">لا توجد ملاحظات إضافية.</p>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', () => notify(@json(session('success')), 'success'));
</script>
@endif

<script>
const markCompletedUrl = @json(route('payments.mark-as-completed', $payment));

function notify(message, type) {
    const colors = { success: 'bg-green-600', error: 'bg-red-600' };
    const el = document.createElement('div');
    el.className = `fixed top-4 left-4 z-[100] px-5 py-3 rounded-xl shadow-lg text-white text-sm font-tajawal ${colors[type] || 'bg-blue-600'}`;
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

function markAsCompleted() {
    if (!confirm('تأكيد هذه الدفعة كمكتملة؟')) return;
    fetch(markCompletedUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            notify(data.message || 'تم التأكيد', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            notify(data.message || 'حدث خطأ', 'error');
        }
    })
    .catch(() => notify('حدث خطأ في الاتصال', 'error'));
}
</script>
@endsection
