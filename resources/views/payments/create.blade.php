@extends('layouts.app')

@section('page-title', 'إضافة دفعة جديدة')

@section('content')
<div class="w-full max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">إضافة دفعة جديدة</h1>
                <p class="text-gray-600">تسجيل دفعة مالية واردة أو صادرة</p>
            </div>
            <a href="{{ route('payments.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 flex items-center shadow-sm">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Payment Form -->
    <form id="paymentForm" class="space-y-6">
        @csrf
        
        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <div class="p-2 bg-blue-500 rounded-lg ml-3">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                المعلومات الأساسية
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الدفعة <span class="text-red-500">*</span></label>
                    <input type="text" name="payment_number" value="{{ $paymentNumber }}" readonly 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-700 font-medium">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الدفعة <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">نوع الدفعة <span class="text-red-500">*</span></label>
                    <select name="payment_type" required onchange="updatePaymentTypeUI(this.value)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر نوع الدفعة</option>
                        <option value="invoice">دفعة فاتورة (من العميل)</option>
                        <option value="salary">دفعة راتب (للموظف)</option>
                        <option value="expense">دفعة مصروف</option>
                        <option value="other">دفعة أخرى</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">المبلغ <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">طريقة الدفع <span class="text-red-500">*</span></label>
                    <select name="payment_method" required id="payment_method" onchange="updateBankAccountField()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر طريقة الدفع</option>
                        <option value="cash">نقدي</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="check">شيك</option>
                        <option value="credit_card">بطاقة ائتمان</option>
                        <option value="online">دفع إلكتروني</option>
                    </select>
                </div>
                
                <div id="bank_account_field" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">حساب البنك</label>
                    <select name="bank_account_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر حساب البنك</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">رقم المرجع</label>
                    <input type="text" name="reference_number" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="رقم المرجع أو الشيك">
                </div>
            </div>
        </div>

        <!-- Related Information -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <div class="p-2 bg-purple-500 rounded-lg ml-3">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                معلومات الربط
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div id="invoice_field">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الفاتورة المرتبطة</label>
                    <select name="invoice_id" id="invoice_id" onchange="updateClientFromInvoice()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">لا يوجد (اختياري)</option>
                        @forelse($invoices as $invoice)
                            <option value="{{ $invoice->id }}" data-client-id="{{ $invoice->client_id ?? '' }}">
                                {{ $invoice->invoice_number }} - {{ $invoice->client->name ?? 'بدون عميل' }} - {{ number_format($invoice->balance_due ?? $invoice->total_amount, 2) }} ج.م
                                @if($invoice->payment_status == 'paid')
                                    (مدفوعة)
                                @elseif($invoice->payment_status == 'partial')
                                    (مدفوعة جزئياً)
                                @endif
                            </option>
                        @empty
                            <option value="" disabled>لا توجد فواتير متاحة</option>
                        @endforelse
                    </select>
                    @if($invoices->isEmpty())
                        <p class="mt-2 text-sm text-yellow-600 flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            لا توجد فواتير متاحة. يمكنك إنشاء فاتورة جديدة من <a href="{{ route('financial-invoices.index') }}" class="text-blue-600 hover:underline">الفواتير المالية</a>
                        </p>
                    @endif
                </div>
                
                <div id="client_field">
                    <span id="client_required" class="text-red-500 text-sm hidden">*</span>
                    @include('partials.client-search-select', ['required' => false, 'value' => old('client_id'), 'inputClass' => 'w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500', 'crmScope' => false])
                </div>
                
                <div id="employee_field" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الموظف</label>
                    <select name="employee_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر الموظف (اختياري)</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Description and Notes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">الوصف <span class="text-red-500">*</span></h3>
                <textarea name="description" rows="6" required 
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="وصف الدفعة..."></textarea>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">ملاحظات إضافية</h3>
                <textarea name="notes" rows="6" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="أي ملاحظات إضافية..."></textarea>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 pt-6">
            <a href="{{ route('payments.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                حفظ الدفعة
            </button>
        </div>
    </form>
</div>

<script>
function updatePaymentTypeUI(type) {
    const clientField = document.getElementById('client_field');
    const employeeField = document.getElementById('employee_field');
    const invoiceField = document.getElementById('invoice_field');
    const clientRequired = document.getElementById('client_required');
    
    // إخفاء جميع الحقول أولاً
    clientField.style.display = 'block';
    employeeField.style.display = 'none';
    invoiceField.style.display = 'none';
    clientRequired.style.display = 'none';
    
    // مسح القيم
    document.querySelector('select[name="employee_id"]').value = '';
    document.querySelector('select[name="invoice_id"]').value = '';
    if (typeof clearClientSearchSelect === 'function') {
        clearClientSearchSelect('client_field');
    }
    
    if (type === 'invoice') {
        // للدفعات المرتبطة بفاتورة: إظهار العميل والفاتورة
        clientField.style.display = 'block';
        invoiceField.style.display = 'block';
        employeeField.style.display = 'none';
        clientRequired.style.display = 'inline';
    } else if (type === 'salary') {
        // للدفعات المرتبطة براتب: إظهار الموظف
        clientField.style.display = 'block';
        employeeField.style.display = 'block';
        invoiceField.style.display = 'none';
    } else if (type === 'expense') {
        // للمصروفات: إخفاء الفاتورة والموظف
        clientField.style.display = 'block';
        employeeField.style.display = 'none';
        invoiceField.style.display = 'none';
    } else if (type === 'other') {
        // للدفعات الأخرى: إظهار العميل فقط
        clientField.style.display = 'block';
        employeeField.style.display = 'none';
        invoiceField.style.display = 'none';
    }
}

function updateClientFromInvoice() {
    const invoiceSelect = document.getElementById('invoice_id');
    const clientSelect = document.getElementById('client_id');
    const selectedOption = invoiceSelect.options[invoiceSelect.selectedIndex];
    
    if (selectedOption.value && selectedOption.dataset.clientId) {
        clientSelect.value = selectedOption.dataset.clientId;
    }
}

function updateBankAccountField() {
    const paymentMethod = document.getElementById('payment_method').value;
    const bankAccountField = document.getElementById('bank_account_field');
    
    if (paymentMethod === 'bank_transfer' || paymentMethod === 'check') {
        bankAccountField.style.display = 'block';
    } else {
        bankAccountField.style.display = 'none';
        document.querySelector('select[name="bank_account_id"]').value = '';
    }
}

// Form submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // تحويل القيم الفارغة إلى null للمفاتيح الأجنبية
    const fieldsToNullify = ['invoice_id', 'client_id', 'employee_id', 'bank_account_id', 'reference_number', 'notes'];
    fieldsToNullify.forEach(field => {
        const value = formData.get(field);
        if (value === '' || value === null || value === undefined) {
            // إرسال null كسلسلة نصية بدلاً من حذف الحقل
            formData.set(field, '');
        }
    });
    
    fetch('{{ route("payments.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            if (data.success) {
                showNotification('تم إضافة الدفعة بنجاح', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("payments.index") }}';
                }, 1000);
            } else {
                // عرض رسائل الخطأ من التحقق من الصحة
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join(', ');
                    showNotification(errorMessages || 'حدث خطأ أثناء إضافة الدفعة', 'error');
                } else {
                    showNotification(data.message || 'حدث خطأ أثناء إضافة الدفعة', 'error');
                }
            }
        } else {
            // إذا لم تكن الاستجابة JSON، قد يكون هناك خطأ في التحقق
            const text = await response.text();
            if (response.status === 422) {
                showNotification('يرجى التحقق من جميع الحقول المطلوبة', 'error');
            } else if (response.status === 500) {
                showNotification('حدث خطأ في الخادم، يرجى المحاولة مرة أخرى', 'error');
            } else {
                showNotification('حدث خطأ أثناء إضافة الدفعة', 'error');
            }
            console.error('Response error:', text);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ في الاتصال', 'error');
    });
});

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        notification.style.transition = 'all 0.3s';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endsection



