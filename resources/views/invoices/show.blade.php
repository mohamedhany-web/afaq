@extends('layouts.app')

@php
    $isFinancial = request()->routeIs('financial-invoices.*');
    $indexRoute = $isFinancial ? route('financial-invoices.index') : route('invoices.index');
    $markPaidRoute = $isFinancial ? route('financial-invoices.mark-as-paid', $invoice) : route('invoices.mark-as-paid', $invoice);
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';

    $statusName = match($invoice->status) {
        'draft' => 'مسودة',
        'sent' => 'مرسلة',
        'viewed' => 'تم المشاهدة',
        'paid' => 'مدفوعة',
        'overdue' => 'متأخرة',
        'cancelled' => 'ملغاة',
        default => $invoice->status,
    };
    $statusColor = match($invoice->status) {
        'paid' => 'bg-green-100 text-green-800',
        'sent', 'viewed' => 'bg-blue-100 text-blue-800',
        'overdue', 'cancelled' => 'bg-red-100 text-red-800',
        default => 'bg-amber-100 text-amber-800',
    };

    $invoiceDate = $invoice->invoice_date
        ? ($invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date : \Carbon\Carbon::parse($invoice->invoice_date))
        : ($invoice->issue_date ?? null);
    $dueDate = $invoice->due_date
        ? ($invoice->due_date instanceof \Carbon\Carbon ? $invoice->due_date : \Carbon\Carbon::parse($invoice->due_date))
        : null;

    $itemsArray = [];
    if ($invoice->items && is_array($invoice->items) && count($invoice->items) > 0) {
        $itemsArray = $invoice->items;
    } elseif (method_exists($invoice, 'getRelation') && $invoice->relationLoaded('items') && $invoice->items && $invoice->items->isNotEmpty()) {
        $itemsArray = $invoice->items->map(fn ($i) => [
            'description' => $i->description ?? $i->item_name ?? '',
            'quantity' => $i->quantity,
            'unit_price' => $i->unit_price,
            'amount' => $i->amount ?? ($i->quantity * $i->unit_price),
        ])->toArray();
    }

    $logoPath = \App\Helpers\SettingsHelper::getLogoPath();
    $logoExists = $logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath);
    $companyName = \App\Helpers\SettingsHelper::getCompanyName() ?: config('app.name');
@endphp

@section('page-title', 'فاتورة ' . $invoice->invoice_number)

@section('header-actions')
<button type="button" onclick="printInvoice()"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors font-tajawal shrink-0"
        title="طباعة الفاتورة">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
    <span class="hidden sm:inline">طباعة الفاتورة</span>
</button>
@endsection

@section('content')
<div class="invoice-page-chrome no-print">
@include('accounting.partials.context')

@include('crm.partials.page-header', [
    'title' => 'فاتورة ' . $invoice->invoice_number,
    'subtitle' => ($isFinancial ? 'فاتورة مالية' : 'فاتورة مبيعات') . ' — ' . ($invoice->client?->name ?? 'بدون عميل'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
    'actionUrl' => $indexRoute,
    'actionLabel' => 'العودة للفواتير',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />',
])

@include('accounting.partials.nav')

<div class="flex flex-wrap items-center gap-2 mb-6 font-tajawal">
    @if($invoice->status !== 'paid')
    <button type="button" onclick="markAsPaid()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        تحديد كمدفوعة
    </button>
    @endif
    <button type="button" onclick="printInvoice()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all"
            style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        طباعة الفاتورة
    </button>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الفاتورة', 'value' => $money($invoice->total_amount), 'accent' => 'theme', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'المدفوع', 'value' => $money($invoice->paid_amount ?? 0), 'accent' => 'green', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'المتبقي', 'value' => $money($invoice->balance_due ?? max(0, $invoice->total_amount - ($invoice->paid_amount ?? 0))), 'accent' => 'amber', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'الحالة', 'value' => $statusName, 'accent' => $invoice->status === 'paid' ? 'green' : ($invoice->status === 'overdue' ? 'red' : 'blue'), 'compact' => true])
</div>
</div>

<div id="invoice-document" class="invoice-document bg-white rounded-2xl shadow-lg border border-gray-200 font-tajawal max-w-5xl mx-auto" style="--invoice-theme: {{ $themeColor }};">

    {{-- رأس الفاتورة --}}
    <div class="invoice-print-header invoice-avoid-break px-6 sm:px-8 py-6 text-white" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($logoExists)
                <div class="bg-white p-2 rounded-xl shrink-0">
                    <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="h-12 w-auto object-contain">
                </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold">{{ $companyName }}</h2>
                    <p class="text-sm opacity-90 mt-1">{{ $isFinancial ? 'فاتورة مالية' : 'فاتورة مبيعات' }} · {{ $invoice->invoice_number }}</p>
                </div>
            </div>
            <div class="bg-white/95 text-gray-900 rounded-2xl px-5 py-3 text-center sm:text-left shadow-md">
                <div class="text-xs text-gray-500 mb-0.5">المبلغ المستحق</div>
                <div class="text-2xl font-bold tabular-nums" style="color: {{ $themeColor }};">{{ $money($invoice->total_amount) }}</div>
                <span class="inline-block mt-1 text-xs font-bold px-2 py-0.5 rounded-lg {{ $statusColor }}">{{ $statusName }}</span>
            </div>
        </div>
    </div>

    {{-- الأطراف والتواريخ --}}
    <div class="invoice-print-parties invoice-avoid-break p-5 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-gray-100" style="background: {{ $themeColor }}06;">
        <div class="invoice-party-card bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-xs font-bold text-gray-500 mb-3 uppercase">من</h3>
            <p class="font-bold text-gray-900">{{ $companyName }}</p>
            @if(\App\Helpers\SettingsHelper::getCompanyAddress())
            <p class="text-xs text-gray-600 mt-1">{{ \App\Helpers\SettingsHelper::getCompanyAddress() }}</p>
            @endif
            @if(\App\Helpers\SettingsHelper::getCompanyPhone())
            <p class="text-xs text-gray-600 mt-1">{{ \App\Helpers\SettingsHelper::getCompanyPhone() }}</p>
            @endif
            @if(\App\Helpers\SettingsHelper::getCompanyEmail())
            <p class="text-xs text-gray-600">{{ \App\Helpers\SettingsHelper::getCompanyEmail() }}</p>
            @endif
        </div>
        <div class="invoice-party-card bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-xs font-bold text-gray-500 mb-3 uppercase">إلى</h3>
            <p class="font-bold text-gray-900">{{ $invoice->client?->name ?? '—' }}</p>
            @if($invoice->client?->address)<p class="text-xs text-gray-600 mt-1">{{ $invoice->client->address }}</p>@endif
            @if($invoice->client?->email)<p class="text-xs text-gray-600 mt-1">{{ $invoice->client->email }}</p>@endif
            @if($invoice->client?->phone)<p class="text-xs text-gray-600">{{ $invoice->client->phone }}</p>@endif
        </div>
        <div class="invoice-party-card bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-xs font-bold text-gray-500 mb-3 uppercase">التفاصيل</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">الإصدار</dt><dd class="font-semibold">{{ $invoiceDate?->format('Y/m/d') ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">الاستحقاق</dt><dd class="font-semibold">{{ $dueDate?->format('Y/m/d') ?? '—' }}</dd></div>
                @if($invoice->project)
                <div class="flex justify-between"><dt class="text-gray-500">المشروع</dt><dd class="font-semibold">{{ $invoice->project->name }}</dd></div>
                @endif
                @if($invoice->createdBy)
                <div class="flex justify-between"><dt class="text-gray-500">أنشأها</dt><dd class="font-semibold">{{ $invoice->createdBy->name }}</dd></div>
                @endif
            </dl>
        </div>
    </div>

    {{-- البنود --}}
    <div class="invoice-print-items p-5 sm:p-6">
        <h3 class="font-bold text-gray-900 mb-4 invoice-avoid-break-after">بنود الفاتورة</h3>
        @if(count($itemsArray) > 0)
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="invoice-items-table w-full text-sm">
                <thead class="text-xs uppercase text-gray-500" style="background: {{ $themeColor }}08;">
                    <tr>
                        <th class="p-3 text-right">الوصف</th>
                        <th class="p-3 text-center w-20">الكمية</th>
                        <th class="p-3 text-center w-32">السعر</th>
                        <th class="p-3 text-center w-32">الإجمالي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($itemsArray as $item)
                    <tr class="invoice-item-row hover:bg-gray-50/50">
                        <td class="p-3 font-medium text-gray-900">{{ $item['description'] ?? '—' }}</td>
                        <td class="p-3 text-center tabular-nums">{{ $item['quantity'] ?? 0 }}</td>
                        <td class="p-3 text-center tabular-nums">{{ $money($item['unit_price'] ?? 0) }}</td>
                        <td class="p-3 text-center font-bold tabular-nums">{{ $money($item['amount'] ?? (($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0))) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 rounded-xl border-2 border-dashed border-gray-200 text-gray-500 text-sm">
            لا توجد بنود مسجّلة لهذه الفاتورة.
        </div>
        @endif
    </div>

    {{-- الإجماليات --}}
    <div class="invoice-print-totals px-5 sm:px-6 pb-6 invoice-avoid-break">
        <div class="flex justify-end">
            <div class="invoice-summary-box w-full sm:w-80 rounded-xl border border-gray-200 overflow-hidden">
                <div class="flex justify-between p-3 border-b text-sm">
                    <span class="text-gray-600">المجموع الفرعي</span>
                    <span class="font-bold tabular-nums">{{ $money($invoice->subtotal ?? $invoice->amount ?? $invoice->total_amount) }}</span>
                </div>
                @if(($invoice->tax_amount ?? 0) > 0)
                <div class="flex justify-between p-3 border-b text-sm">
                    <span class="text-gray-600">الضريبة ({{ $invoice->tax_rate ?? 0 }}%)</span>
                    <span class="font-bold tabular-nums">{{ $money($invoice->tax_amount) }}</span>
                </div>
                @endif
                @if(($invoice->discount_amount ?? 0) > 0)
                <div class="flex justify-between p-3 border-b text-sm">
                    <span class="text-gray-600">الخصم</span>
                    <span class="font-bold text-red-600 tabular-nums">- {{ $money($invoice->discount_amount) }}</span>
                </div>
                @endif
                <div class="flex justify-between p-4 text-white font-bold" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                    <span>الإجمالي</span>
                    <span class="text-lg tabular-nums">{{ $money($invoice->total_amount) }}</span>
                </div>
            </div>
        </div>
    </div>

    @php
        $financialNotes = \App\Helpers\SettingsHelper::getInvoiceFinancialNotes();
        $paymentMethods = \App\Helpers\SettingsHelper::getPaymentMethods();
        $defaultPeriod = \App\Helpers\SettingsHelper::getDefaultPaymentPeriod();
        $days = ($invoiceDate && $dueDate) ? $invoiceDate->diffInDays($dueDate) : $defaultPeriod;
    @endphp

    @if($invoice->notes || $financialNotes)
    <div class="invoice-print-notes invoice-avoid-break px-5 sm:px-6 pb-5 border-t border-gray-100 pt-5">
        <h3 class="font-bold text-gray-900 mb-2 text-sm">ملاحظات</h3>
        <div class="rounded-xl border border-gray-200 p-4 text-sm text-gray-700 bg-gray-50">
            @if($invoice->notes)<p>{{ $invoice->notes }}</p>@endif
            @if($financialNotes)<p class="{{ $invoice->notes ? 'mt-2' : '' }}">{{ $financialNotes }}</p>@endif
        </div>
    </div>
    @endif

    <div class="invoice-print-terms invoice-avoid-break px-5 sm:px-6 pb-6 grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-100 pt-5">
        <div class="invoice-terms-card rounded-xl border border-gray-200 p-4">
            <h4 class="text-xs font-bold text-gray-500 mb-2 uppercase">شروط الدفع</h4>
            <p class="text-sm text-gray-700"><span class="font-semibold">الطريقة:</span> {{ $paymentMethods ?: 'تحويل بنكي' }}</p>
            <p class="text-sm text-gray-700 mt-1"><span class="font-semibold">المدة:</span> {{ $days }} يوم</p>
        </div>
        <div class="invoice-terms-card rounded-xl border border-gray-200 p-4">
            <h4 class="text-xs font-bold text-gray-500 mb-2 uppercase">بيانات البنك</h4>
            @if(\App\Helpers\SettingsHelper::getBankName())<p class="text-sm text-gray-700">{{ \App\Helpers\SettingsHelper::getBankName() }}</p>@endif
            @if(\App\Helpers\SettingsHelper::getBankAccountNumber())<p class="text-sm text-gray-700 mt-1">حساب: {{ \App\Helpers\SettingsHelper::getBankAccountNumber() }}</p>@endif
            @if(\App\Helpers\SettingsHelper::getBankIban())<p class="text-sm text-gray-700 mt-1">IBAN: {{ \App\Helpers\SettingsHelper::getBankIban() }}</p>@endif
        </div>
    </div>

    <div class="invoice-print-footer invoice-avoid-break px-6 py-4 text-center text-xs text-gray-500 border-t border-gray-100" style="background: {{ $themeColor }}05;">
        شكراً لتعاملكم معنا · {{ $companyName }} · {{ date('Y') }}
    </div>
</div>

@if(session('success'))
<script class="no-print">
document.addEventListener('DOMContentLoaded', () => notify(@json(session('success')), 'success'));
</script>
@endif

<script class="no-print">
const markPaidUrl = @json($markPaidRoute);

function notify(message, type) {
    const colors = { success: 'bg-green-600', error: 'bg-red-600' };
    const el = document.createElement('div');
    el.className = `fixed top-4 left-4 z-[100] px-5 py-3 rounded-xl shadow-lg text-white text-sm font-tajawal ${colors[type] || 'bg-blue-600'}`;
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

function printInvoice() {
    window.print();
}

window.addEventListener('beforeprint', () => {
    document.body.classList.add('printing-invoice');
});
window.addEventListener('afterprint', () => {
    document.body.classList.remove('printing-invoice');
});

function markAsPaid() {
    if (!confirm('تحديد هذه الفاتورة كمدفوعة؟')) return;
    fetch(markPaidUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.headers.get('content-type')?.includes('json') ? r.json() : { success: true })
    .then(data => {
        if (data.success) {
            notify(data.message || 'تم تحديث الحالة', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            notify(data.message || 'حدث خطأ', 'error');
        }
    })
    .catch(() => notify('حدث خطأ في الاتصال', 'error'));
}
</script>
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm 8mm;
    }

    html, body {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        height: auto !important;
        overflow: visible !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    body.printing-invoice #sidebar,
    body.printing-invoice .sidebar-overlay,
    body.printing-invoice .app-top-header,
    body.printing-invoice .no-print,
    body.printing-invoice .invoice-page-chrome,
    body.printing-invoice main > div > .bg-green-50,
    body.printing-invoice main > div > .bg-red-50,
    body.printing-invoice main > div > .bg-amber-50,
    body.printing-invoice main > div > .bg-blue-50,
    body.printing-invoice #work-day-must-start-banner {
        display: none !important;
    }

    body.printing-invoice .flex.h-screen,
    body.printing-invoice .main-content-mobile,
    body.printing-invoice main,
    body.printing-invoice main > div {
        display: block !important;
        overflow: visible !important;
        height: auto !important;
        min-height: 0 !important;
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    body.printing-invoice #invoice-document {
        display: block !important;
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
        max-width: none !important;
        width: 100% !important;
        margin: 0 !important;
        border-radius: 0 !important;
        overflow: visible !important;
        page-break-before: auto;
    }

    body.printing-invoice .invoice-avoid-break {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    body.printing-invoice .invoice-avoid-break-after {
        break-after: avoid;
        page-break-after: avoid;
    }

    body.printing-invoice .invoice-print-parties {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    body.printing-invoice .invoice-party-card,
    body.printing-invoice .invoice-terms-card,
    body.printing-invoice .invoice-summary-box {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    body.printing-invoice .invoice-items-table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    body.printing-invoice .invoice-items-table thead {
        display: table-header-group;
    }

    body.printing-invoice .invoice-items-table tbody {
        display: table-row-group;
    }

    body.printing-invoice .invoice-item-row {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    body.printing-invoice .invoice-item-row td,
    body.printing-invoice .invoice-items-table th {
        border-bottom: 1px solid #f3f4f6;
    }

    body.printing-invoice .invoice-print-totals {
        break-before: auto;
        page-break-before: auto;
    }

    body.printing-invoice .invoice-print-footer {
        margin-top: 4mm;
    }
}
</style>
@endsection
