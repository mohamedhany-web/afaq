@extends('layouts.app')

@section('page-title', 'إنشاء فاتورة جديدة')

@section('content')
<div class="w-full max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">إنشاء فاتورة جديدة</h1>
                <p class="text-sm sm:text-base text-gray-600">إنشاء وإصدار فاتورة مالية جديدة</p>
            </div>
            <a href="{{ route('financial-invoices.index') }}" class="bg-gray-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-gray-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Invoice Form -->
    <form id="invoiceForm" action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                <div class="p-2 bg-blue-500 rounded-lg ml-3 flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                المعلومات الأساسية
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الفاتورة <span class="text-red-500">*</span></label>
                    <input type="text" name="invoice_number" value="{{ $invoiceNumber }}" readonly 
                           class="w-full px-3 py-2.5 sm:px-4 sm:py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-medium text-sm sm:text-base">
                </div>
                
                <div>
                    @include('partials.client-search-select', ['required' => true, 'value' => old('client_id'), 'inputClass' => 'w-full px-3 py-2.5 sm:px-4 sm:py-3 border border-gray-300 rounded-lg text-sm sm:text-base', 'crmScope' => false])
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">المشروع</label>
                    <select name="project_id" 
                            class="w-full px-3 py-2.5 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                        <option value="">اختر المشروع (اختياري)</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الإصدار <span class="text-red-500">*</span></label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required 
                           class="w-full px-3 py-2.5 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الاستحقاق <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required 
                           class="w-full px-3 py-2.5 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base">
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg ml-3">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    بنود الفاتورة
                </h3>
                <button type="button" onclick="addInvoiceItem()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center text-sm">
                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    إضافة بند
                </button>
            </div>
            
            <div id="invoiceItems" class="space-y-4">
                <!-- Items will be added here dynamically -->
            </div>
        </div>

        <!-- Totals and Notes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Notes -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">ملاحظات</h3>
                <textarea name="notes" rows="8" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="أدخل أي ملاحظات إضافية هنا..."></textarea>
            </div>

            <!-- Summary -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl shadow-md border border-blue-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">الإجماليات</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700">المجموع الفرعي:</label>
                        <span id="subtotalDisplay" class="text-lg font-bold text-gray-900">0.00 ج.م</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700">نسبة الضريبة (%):</label>
                        <input type="number" name="tax_rate" value="0" min="0" max="100" step="0.01" 
                               onchange="calculateTotals()"
                               class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-right">
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700">قيمة الضريبة:</label>
                        <span id="taxDisplay" class="text-lg font-bold text-gray-900">0.00 ج.م</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700">الخصم:</label>
                        <input type="number" name="discount_amount" value="0" min="0" step="0.01" 
                               onchange="calculateTotals()"
                               class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-right">
                    </div>
                    
                    <div class="pt-4 border-t-2 border-blue-300">
                        <div class="flex items-center justify-between">
                            <label class="text-lg font-bold text-gray-900">الإجمالي النهائي:</label>
                            <span id="totalDisplay" class="text-2xl font-bold text-blue-900">0.00 ج.م</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 pt-6">
            <a href="{{ route('financial-invoices.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                حفظ الفاتورة
            </button>
        </div>
    </form>
</div>

<script>
let itemCounter = 0;

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addInvoiceItem();
});

function addInvoiceItem() {
    itemCounter++;
    const container = document.getElementById('invoiceItems');
    const itemHtml = `
        <div class="invoice-item border border-gray-200 rounded-lg p-4 bg-gray-50" data-item="${itemCounter}">
            <div class="flex items-start gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف <span class="text-red-500">*</span></label>
                        <input type="text" name="items[${itemCounter}][description]" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="وصف البند">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الكمية <span class="text-red-500">*</span></label>
                        <input type="number" name="items[${itemCounter}][quantity]" value="1" min="0" step="0.01" required 
                               class="item-quantity w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               onchange="calculateTotals()">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">السعر <span class="text-red-500">*</span></label>
                        <input type="number" name="items[${itemCounter}][unit_price]" value="0" min="0" step="0.01" required 
                               class="item-price w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               onchange="calculateTotals()">
                    </div>
                </div>
                
                <button type="button" onclick="removeInvoiceItem(${itemCounter})" 
                        class="mt-8 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    calculateTotals();
}

function removeInvoiceItem(itemId) {
    const items = document.querySelectorAll('.invoice-item');
    if (items.length <= 1) {
        alert('يجب أن تحتوي الفاتورة على بند واحد على الأقل');
        return;
    }
    
    const item = document.querySelector(`[data-item="${itemId}"]`);
    if (item) {
        item.remove();
        calculateTotals();
    }
}

function calculateTotals() {
    let subtotal = 0;
    
    // Calculate subtotal from all items
    document.querySelectorAll('.invoice-item').forEach(item => {
        const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').value) || 0;
        subtotal += quantity * price;
    });
    
    // Get tax rate and discount
    const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;
    const discount = parseFloat(document.querySelector('input[name="discount_amount"]').value) || 0;
    
    // Calculate tax and total
    const tax = (subtotal * taxRate) / 100;
    const total = subtotal + tax - discount;
    
    // Update displays
    document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' ج.م';
    document.getElementById('taxDisplay').textContent = tax.toFixed(2) + ' ج.م';
    document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' ج.م';
}

// Form submission
document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate at least one item
    const items = document.querySelectorAll('.invoice-item');
    if (items.length === 0) {
        alert('يجب إضافة بند واحد على الأقل');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('{{ route("financial-invoices.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم إنشاء الفاتورة بنجاح', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("financial-invoices.index") }}';
            }, 1000);
        } else {
            showNotification(data.message || 'حدث خطأ أثناء إنشاء الفاتورة', 'error');
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



