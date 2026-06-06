@extends('layouts.app')

@section('page-title', 'إدارة المبيعات')

@section('content')
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">إدارة المبيعات</h1>
                <p class="text-sm sm:text-base text-gray-600">إدارة عمليات البيع والمتابعة</p>
            </div>
            <a href="{{ route('sales.create') }}" class="bg-blue-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                عملية بيع جديدة
            </a>
        </div>
    </div>

    <!-- Sales Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Sales -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المبيعات</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $sales->total() }}</p>
                    <p class="text-xs text-blue-600 mt-1">جميع العمليات</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Won Sales -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">مبيعات مكتملة</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $sales->where('stage', 'closed_won')->count() }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $sales->total() > 0 ? round(($sales->where('stage', 'closed_won')->count() / $sales->total()) * 100, 1) : 0 }}% من إجمالي المبيعات</p>
                </div>
                <div class="p-4 bg-green-50 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Sales -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">مبيعات نشطة</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $sales->whereNotIn('stage', ['closed_won', 'closed_lost'])->count() }}</p>
                    <p class="text-xs text-orange-600 mt-1">قيد المتابعة</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-xl">
                    <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الإيرادات</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($sales->where('stage', 'closed_won')->sum('actual_value') ?? $sales->where('stage', 'closed_won')->sum('estimated_value') ?? 0) }} ج.م</p>
                    <p class="text-xs text-purple-600 mt-1">من المبيعات المكتملة</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-xl">
                    <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="البحث في المنتجات أو العملاء">
            </div>

            <!-- Stage Filter -->
            <div>
                <label for="stage" class="block text-sm font-medium text-gray-700 mb-2">المرحلة</label>
                <select name="stage" id="stage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع المراحل</option>
                    @foreach($stages as $stage)
                        <option value="{{ $stage }}" {{ request('stage') == $stage ? 'selected' : '' }}>
                            @if($stage == 'lead') عميل محتمل
                            @elseif($stage == 'prospect') عميل مؤهل
                            @elseif($stage == 'proposal') عرض سعر
                            @elseif($stage == 'negotiation') مفاوضات
                            @elseif($stage == 'closed_won') مكتمل - فائز
                            @elseif($stage == 'closed_lost') مكتمل - خاسر
                            @else {{ $stage }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Client Filter -->
            <div>
                @include('partials.client-search-select', ['required' => false, 'value' => request('client_id'), 'inputClass' => 'w-full px-3 py-2 border border-gray-300 rounded-lg', 'crmScope' => false, 'placeholder' => 'ابحث لتصفية حسب عميل...'])
            </div>

            <!-- Sales Rep Filter -->
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">مندوب المبيعات</label>
                <select name="assigned_to" id="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع المناديب</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-4 flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    تصفية
                </button>
                <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    مسح
                </a>
            </div>
        </form>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة المبيعات</h3>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">المنتج/الخدمة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">العميل</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">مندوب المبيعات</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">القيمة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">المرحلة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 w-1/6">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $sale->product_service }}</div>
                                <div class="text-sm text-gray-500">{{ $sale->lead_source ?? 'غير محدد' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <div class="flex items-center">
                                <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center ml-3">
                                    <span class="text-xs font-medium text-white">{{ substr($sale->client->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $sale->client->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $sale->client->company_name ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/6 text-sm text-gray-900">
                            {{ $sale->salesRep->name }}
                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ number_format($sale->actual_value ?? $sale->estimated_value) }} ج.م</div>
                                <div class="text-sm text-gray-500">{{ $sale->probability_percentage }}% احتمال</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($sale->stage == 'lead') bg-gray-100 text-gray-800
                                @elseif($sale->stage == 'prospect') bg-blue-100 text-blue-800
                                @elseif($sale->stage == 'proposal') bg-yellow-100 text-yellow-800
                                @elseif($sale->stage == 'negotiation') bg-orange-100 text-orange-800
                                @elseif($sale->stage == 'closed_won') bg-green-100 text-green-800
                                @elseif($sale->stage == 'closed_lost') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($sale->stage == 'lead') عميل محتمل
                                @elseif($sale->stage == 'prospect') عميل مؤهل
                                @elseif($sale->stage == 'proposal') عرض سعر
                                @elseif($sale->stage == 'negotiation') مفاوضات
                                @elseif($sale->stage == 'closed_won') مكتمل - فائز
                                @elseif($sale->stage == 'closed_lost') مكتمل - خاسر
                                @else {{ $sale->stage }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    عرض
                                </a>
                                <a href="{{ route('sales.edit', $sale) }}" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @if($sale->stage == 'closed_won')
                                    @if(isset($invoices[$sale->id]))
                                        <a href="{{ route('invoices.show', $invoices[$sale->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200" title="عرض الفاتورة الموجودة">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            عرض الفاتورة
                                        </a>
                                    @else
                                        <form action="{{ route('sales.generate-invoice', $sale) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من إنشاء الفاتورة؟');">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                فاتورة
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مبيعات</h3>
                                <p class="text-gray-500 mb-4">ابدأ بإضافة عملية بيع جديدة</p>
                                <a href="{{ route('sales.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    إضافة عملية بيع جديدة
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($sales->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection