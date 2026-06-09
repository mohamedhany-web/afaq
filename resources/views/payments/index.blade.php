@extends('layouts.app')
@section('page-title', 'المدفوعات')

@section('content')
@include('accounting.partials.context')
@include('crm.partials.page-header', [
    'title' => 'المدفوعات',
    'subtitle' => 'تتبع المدفوعات الواردة والصادرة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />',
    'actionUrl' => route('payments.create'),
    'actionLabel' => 'دفعة جديدة',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])
@include('accounting.partials.nav')

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي المدفوعات', 'value' => $totalPayments, 'accent' => 'theme', 'compact' => true, 'footer' => '<span class="text-gray-500">'.$monthlyPayments.' هذا الشهر</span>'])
    @include('crm.partials.stat-card', ['label' => 'واردة', 'value' => $incomingPayments, 'accent' => 'green', 'compact' => true, 'footer' => '<span class="text-green-600">'.$money($totalIncoming).'</span>'])
    @include('crm.partials.stat-card', ['label' => 'صادرة', 'value' => $outgoingPayments, 'accent' => 'red', 'compact' => true, 'footer' => '<span class="text-red-600">'.$money($totalOutgoing).'</span>'])
    @include('crm.partials.stat-card', ['label' => 'معلقة', 'value' => $pendingPayments, 'accent' => 'amber', 'compact' => true, 'footer' => '<span class="text-amber-600">'.$money($pendingAmount).'</span>'])
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">قائمة المدفوعات</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[1000px] font-tajawal">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="p-4 text-right font-bold">رقم الدفعة</th>
                    <th class="p-4 text-center font-bold">التاريخ</th>
                    <th class="p-4 text-right font-bold">الوصف</th>
                    <th class="p-4 text-center font-bold">المبلغ</th>
                    <th class="p-4 text-right font-bold">طريقة الدفع</th>
                    <th class="p-4 text-right font-bold">النوع</th>
                    <th class="p-4 text-center font-bold">الحالة</th>
                    <th class="p-4 text-center font-bold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                @php
                    $isIncoming = $payment->payment_type === 'invoice' || ($payment->client_id && $payment->payment_type !== 'salary' && $payment->payment_type !== 'expense');
                    $methodName = match($payment->payment_method) {
                        'cash' => 'نقدي', 'bank_transfer' => 'تحويل بنكي', 'check' => 'شيك',
                        'credit_card' => 'بطاقة ائتمان', 'online' => 'دفع إلكتروني', default => $payment->payment_method
                    };
                    $typeName = match($payment->payment_type) {
                        'invoice' => 'فاتورة', 'salary' => 'راتب', 'expense' => 'مصروف', 'other' => 'أخرى', default => $payment->payment_type
                    };
                    $statusColor = match($payment->status) {
                        'completed' => 'bg-green-100 text-green-800', 'pending' => 'bg-amber-100 text-amber-800',
                        'cancelled' => 'bg-red-100 text-red-800', default => 'bg-gray-100 text-gray-800'
                    };
                    $statusName = match($payment->status) {
                        'completed' => 'مكتملة', 'pending' => 'معلقة', 'cancelled' => 'ملغية', default => $payment->status
                    };
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-bold text-gray-900">{{ $payment->payment_number }}</td>
                    <td class="p-4 text-center text-gray-500">{{ $payment->payment_date->format('Y/m/d') }}</td>
                    <td class="p-4 text-gray-700">{{ $payment->description }}</td>
                    <td class="p-4 text-center font-bold tabular-nums {{ $isIncoming ? 'text-green-600' : 'text-red-600' }}">{{ $isIncoming ? '+' : '-' }}{{ $money($payment->amount) }}</td>
                    <td class="p-4 text-gray-600">{{ $methodName }}</td>
                    <td class="p-4">
                        <span class="text-xs font-bold px-2 py-1 rounded-lg {{ $isIncoming ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $typeName }}</span>
                        @if($payment->client)<div class="text-xs text-gray-500 mt-1">{{ $payment->client->name }}</div>@endif
                    </td>
                    <td class="p-4 text-center"><span class="text-xs font-bold px-2 py-1 rounded-lg {{ $statusColor }}">{{ $statusName }}</span></td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('payments.show', $payment) }}" class="text-xs font-bold" style="color:{{ $themeColor }}">عرض</a>
                            @if($payment->status === 'pending')
                            <button onclick="markAsCompleted({{ $payment->id }})" class="text-xs font-bold text-green-600">تأكيد</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-10 text-center text-gray-500">لا توجد مدفوعات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())<div class="px-5 py-4 border-t">{{ $payments->links() }}</div>@endif
</div>

<script>
function markAsCompleted(paymentId) {
    if (!confirm('تأكيد هذه الدفعة؟')) return;
    fetch(`/payments/${paymentId}/mark-as-completed`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json'},
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); else alert(d.message || 'خطأ'); });
}
</script>
@endsection
