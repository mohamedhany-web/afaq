@extends('layouts.app')

@section('page-title', 'القيود المحاسبية')

@section('content')
@include('accounting.partials.context')
@include('crm.partials.page-header', [
    'title' => 'القيود المحاسبية',
    'subtitle' => 'إدارة القيود والحركات المالية',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
    'actionUrl' => route('accounting.journal-entries.create'),
    'actionLabel' => 'قيد جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])
@include('accounting.partials.nav')

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold font-tajawal flex justify-between items-center" style="{{ $headerStyle }}">
                <h3>قائمة القيود المحاسبية</h3>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">فلترة:</span>
                        <form method="GET" class="inline">
                            <select name="status" onchange="this.form.submit()" class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">جميع الحالات</option>
                                <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                                <option value="approved" @selected(request('status') === 'approved')>معتمد</option>
                                <option value="posted" @selected(request('status') === 'posted')>مرحل</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        @if($entries->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المرجع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المجموع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنشئ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($entries as $entry)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $entry->date->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $entry->reference }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            {{ $entry->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="text-right">
                                <div class="font-medium">{{ $money($entry->total_debit) }}</div>
                                <div class="text-xs text-gray-500">مدين: {{ $money($entry->total_debit) }} | دائن: {{ $money($entry->total_credit) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $entry->status_color }}">
                                {{ $entry->status_in_arabic }}
                            </span>
                            @if(!$entry->is_balanced)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                    غير متوازن
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $entry->creator->name ?? 'غير محدد' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('accounting.journal-entries.show', $entry) }}" class="text-blue-600 hover:text-blue-900">عرض</a>
                                @if($entry->status !== 'posted')
                                <a href="{{ route('accounting.journal-entries.edit', $entry) }}" class="text-green-600 hover:text-green-900">تعديل</a>
                                @endif
                                @if($entry->status === 'draft')
                                <form action="{{ route('accounting.journal-entries.approve', $entry) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900">اعتماد</button>
                                </form>
                                @endif
                                @if($entry->status === 'approved')
                                <form action="{{ route('accounting.journal-entries.post', $entry) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">ترحيل</button>
                                </form>
                                @endif
                                @if($entry->status !== 'posted')
                                <form action="{{ route('accounting.journal-entries.destroy', $entry) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا القيد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $entries->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-lg font-medium text-gray-900 mb-2">لا توجد قيود محاسبية</p>
            <p class="text-gray-600 mb-4">ابدأ بإنشاء قيد محاسبي جديد</p>
            <a href="{{ route('accounting.journal-entries.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                إنشاء قيد محاسبي جديد
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Quick Stats -->
@if($entries->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-50 rounded-lg">
                <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600">إجمالي القيود</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-50 rounded-lg">
                <svg class="w-8 h-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600">مسودة</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['draft'] }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-50 rounded-lg">
                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600">مرحلة</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['posted'] }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-50 rounded-lg">
                <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600">إجمالي المبالغ</p>
                <p class="text-2xl font-bold text-gray-900">{{ $money($stats['amount']) }}</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection