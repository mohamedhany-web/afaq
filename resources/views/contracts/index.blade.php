@extends('layouts.app')

@section('page-title', 'إدارة العقود')

@section('content')
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة العقود</h1>
                <p class="text-gray-600">إدارة العقود والاتفاقيات</p>
            </div>
            <a href="{{ route('contracts.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-200 flex items-center shadow-sm">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                عقد جديد
            </a>
        </div>
    </div>

    <!-- Contract Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Contracts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي العقود</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $contracts->total() }}</p>
                    <p class="text-xs text-blue-600 mt-1">جميع العقود</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Contracts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">عقود نشطة</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $contracts->where('status', 'active')->count() }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $contracts->total() > 0 ? round(($contracts->where('status', 'active')->count() / $contracts->total()) * 100, 1) : 0 }}% من إجمالي العقود</p>
                </div>
                <div class="p-4 bg-green-50 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Expiring Soon -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">تنتهي قريباً</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $contracts->where('status', 'active')->where('end_date', '<=', now()->addDays(30))->where('end_date', '>=', now())->count() }}</p>
                    <p class="text-xs text-orange-600 mt-1">خلال 30 يوم</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-xl">
                    <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي القيمة</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($contracts->where('status', 'active')->sum('value') ?? 0) }} ج.م</p>
                    <p class="text-xs text-purple-600 mt-1">من العقود النشطة</p>
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
        <form method="GET" action="{{ route('contracts.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="البحث في العقود أو العملاء">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الحالات</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            @if($status == 'draft') مسودة
                            @elseif($status == 'active') نشط
                            @elseif($status == 'expired') منتهي الصلاحية
                            @elseif($status == 'terminated') ملغي
                            @elseif($status == 'renewed') مجدد
                            @else {{ $status }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Contract Type Filter -->
            <div>
                <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-2">نوع العقد</label>
                <select name="contract_type" id="contract_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الأنواع</option>
                    @foreach($contractTypes as $type)
                        <option value="{{ $type }}" {{ request('contract_type') == $type ? 'selected' : '' }}>
                            @if($type == 'employment') عقد عمل
                            @elseif($type == 'service') عقد خدمة
                            @elseif($type == 'nda') اتفاقية عدم الإفشاء
                            @elseif($type == 'partnership') عقد شراكة
                            @elseif($type == 'vendor') عقد مورد
                            @else {{ $type }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Client Filter -->
            <div>
                @include('partials.client-search-select', ['required' => false, 'value' => request('client_id'), 'inputClass' => 'w-full px-3 py-2 border border-gray-300 rounded-lg', 'crmScope' => false, 'placeholder' => 'ابحث لتصفية حسب عميل...'])
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-4 flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    تصفية
                </button>
                <a href="{{ route('contracts.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    مسح
                </a>
            </div>
        </form>
    </div>

    <!-- Contracts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة العقود</h3>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/7">رقم العقد</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/7">العنوان</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/7">العميل</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/7">المشروع</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/7">القيمة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/7">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/7">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contracts as $contract)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 w-1/7">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $contract->contract_number }}</div>
                                <div class="text-sm text-gray-500">{{ $contract->contract_type_name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/7">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $contract->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($contract->description, 50) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/7">
                            <div class="flex items-center">
                                <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center ml-3">
                                    <span class="text-xs font-medium text-white">{{ substr($contract->client->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $contract->client->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $contract->client->company_name ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/7">
                            @if($contract->project)
                                <div class="flex items-center">
                                    <div class="h-8 w-8 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center ml-3">
                                        <span class="text-xs font-medium text-white">P</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $contract->project->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $contract->project->status_name ?? 'غير محدد' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-sm text-gray-500">لا يوجد مشروع مرتبط</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 w-1/7">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ number_format($contract->value) }} ج.م</div>
                                <div class="text-sm text-gray-500">
                                    @if($contract->start_date)
                                        من {{ $contract->start_date->format('Y/m/d') }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/7">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($contract->status == 'draft') bg-gray-100 text-gray-800
                                @elseif($contract->status == 'active') bg-green-100 text-green-800
                                @elseif($contract->status == 'expired') bg-red-100 text-red-800
                                @elseif($contract->status == 'terminated') bg-red-100 text-red-800
                                @elseif($contract->status == 'renewed') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $contract->status_name }}
                            </span>
                            @if($contract->is_expiring_soon)
                                <div class="text-xs text-orange-600 mt-1">ينتهي قريباً</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 w-1/7">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('contracts.show', $contract) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    عرض
                                </a>
                                <a href="{{ route('contracts.edit', $contract) }}" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @if($contract->status == 'active')
                                <form action="{{ route('contracts.generate-invoice', $contract) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        فاتورة
                                    </button>
                                </form>
                                @endif
                                @if(in_array($contract->status, ['active', 'expired']))
                                <form action="{{ route('contracts.renew', $contract) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        تجديد
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد عقود</h3>
                                <p class="text-gray-500 mb-4">ابدأ بإضافة عقد جديد</p>
                                <a href="{{ route('contracts.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    إضافة عقد جديد
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($contracts->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $contracts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection