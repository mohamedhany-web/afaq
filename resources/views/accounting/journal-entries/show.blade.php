@extends('layouts.app')

@section('page-title', 'عرض القيد المحاسبي')

@section('content')
<div class="w-full max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">عرض القيد المحاسبي</h1>
                <p class="text-sm sm:text-base text-gray-600">تفاصيل القيد المحاسبي رقم {{ $journalEntry->reference }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.journal-entries') }}" class="bg-gray-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-gray-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة للقائمة
                </a>
                @if($journalEntry->status !== 'posted')
                <a href="{{ route('accounting.journal-entries.edit', $journalEntry) }}" class="bg-blue-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    تعديل
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Entry Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">بيانات القيد المحاسبي</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">التاريخ</label>
                    <div class="text-base text-gray-900 font-medium">
                        {{ $journalEntry->date->format('Y/m/d') }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">رقم المرجع</label>
                    <div class="text-base text-gray-900 font-medium">
                        {{ $journalEntry->reference }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة</label>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $journalEntry->status_color }}">
                            {{ $journalEntry->status_in_arabic }}
                        </span>
                        @if(!$journalEntry->is_balanced)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 mr-2">
                            غير متوازن
                        </span>
                        @endif
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                    <div class="text-base text-gray-900">
                        {{ $journalEntry->description }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Entry Lines Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">بنود القيد المحاسبي</h3>
        </div>
        <div class="overflow-x-auto">
            @if($journalEntry->lines->count() > 0)
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">كود الحساب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم الحساب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">مدين</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">دائن</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($journalEntry->lines as $index => $line)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $line->account->code ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $line->account->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $line->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium text-gray-900">
                            @if($line->debit > 0)
                                {{ $money($line->debit) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium text-gray-900">
                            @if($line->credit > 0)
                                {{ $money($line->credit) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                            الإجمالي
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                            {{ $money($journalEntry->total_debit) }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                            {{ $money($journalEntry->total_credit) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="px-6 py-12 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-lg font-medium text-gray-900 mb-2">لا توجد بنود</p>
                <p class="text-gray-600">لم يتم إضافة أي بنود لهذا القيد المحاسبي</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Balance Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">ملخص الميزان</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-600 mb-2">إجمالي المدين</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $money($journalEntry->total_debit) }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-600 mb-2">إجمالي الدائن</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $money($journalEntry->total_credit) }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-600 mb-2">حالة الميزان</div>
                    @if($journalEntry->is_balanced)
                    <div class="flex items-center text-green-600">
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-lg font-bold">متوازن</span>
                    </div>
                    @else
                    <div class="flex items-center text-red-600">
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-lg font-bold">غير متوازن</span>
                        <span class="text-sm mr-2">(الفرق: {{ $money(abs($journalEntry->balance_difference)) }})</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">معلومات إضافية</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">المنشئ</label>
                    <div class="text-base text-gray-900">
                        {{ $journalEntry->creator->name ?? 'غير محدد' }}
                    </div>
                </div>
                @if($journalEntry->approved_by)
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">المعتمد</label>
                    <div class="text-base text-gray-900">
                        {{ $journalEntry->approver->name ?? 'غير محدد' }}
                    </div>
                    @if($journalEntry->approved_at)
                    <div class="text-sm text-gray-500 mt-1">
                        بتاريخ: {{ $journalEntry->approved_at->format('Y/m/d H:i') }}
                    </div>
                    @endif
                </div>
                @endif
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الإنشاء</label>
                    <div class="text-base text-gray-900">
                        {{ $journalEntry->created_at->format('Y/m/d H:i') }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">آخر تحديث</label>
                    <div class="text-base text-gray-900">
                        {{ $journalEntry->updated_at->format('Y/m/d H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showNotification('{{ session('success') }}', 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showNotification('{{ session('error') }}', 'error');
    });
</script>
@endif

<script>
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-medium transform translate-x-full transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
@endsection

