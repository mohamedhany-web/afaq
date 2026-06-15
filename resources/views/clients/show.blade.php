@extends('layouts.app')

@section('page-title', 'تفاصيل العميل')

@section('content')
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">تفاصيل العميل</h1>
                <p class="text-gray-600">عرض معلومات العميل التفصيلية</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('clients.edit', $client) }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    تعديل
                </a>
                <a href="{{ route('clients.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Client Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Information -->
        <div class="lg:col-span-2">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-6">
                    <div class="h-16 w-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center ml-4">
                        <span class="text-2xl font-bold text-white">{{ substr($client->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h2>
                        <p class="text-gray-600">{{ $client->company_name ?? 'غير محدد' }}</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-2
                            @if($client->status == 'active') bg-green-100 text-green-800
                            @elseif($client->status == 'inactive') bg-gray-100 text-gray-800
                            @elseif($client->status == 'suspended') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($client->status == 'active') نشط
                            @elseif($client->status == 'inactive') غير نشط
                            @elseif($client->status == 'suspended') معلق
                            @else {{ $client->status }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Contact Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات الاتصال</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">البريد الإلكتروني</p>
                                    <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                </div>
                            </div>
                            @if($client->phone)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">رقم الهاتف</p>
                                        <p class="text-sm text-gray-600">{{ $client->phone }}</p>
                                    </div>
                                </div>
                                <button onclick="openWhatsAppContact('{{ $client->phone }}', '{{ addslashes($client->name) }}')" 
                                        class="text-green-600 hover:text-green-700 transition-colors p-2 rounded-lg hover:bg-green-50"
                                        title="فتح واتساب">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                </button>
                            </div>
                            @endif
                            @if($client->website)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">الموقع الإلكتروني</p>
                                    <a href="{{ $client->website }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">{{ $client->website }}</a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Business Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">المعلومات التجارية</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">نوع العميل</p>
                                    <p class="text-sm text-gray-600">
                                        @include('crm.clients.partials.type-badge', ['type' => $client->client_type])
                                    </p>
                                </div>
                            </div>
                            @if($client->industry)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">المجال</p>
                                    <p class="text-sm text-gray-600">{{ $client->industry }}</p>
                                </div>
                            </div>
                            @endif
                            @if($client->assignedEmployee)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">المكلف بالعميل</p>
                                    <p class="text-sm text-gray-600">{{ $client->assignedEmployee->first_name }} {{ $client->assignedEmployee->last_name }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($client->address || $client->city || $client->country)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">العنوان</h3>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-400 ml-3 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div class="text-sm text-gray-600">
                            @if($client->address)
                                <p>{{ $client->address }}</p>
                            @endif
                            @if($client->city)
                                <p>{{ $client->city }}</p>
                            @endif
                            @if($client->country)
                                <p>{{ $client->country }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if($client->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">ملاحظات</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $client->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Projects and Sales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Projects Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">المشاريع</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $client->projects->count() }}
                        </span>
                    </div>
                    @if($client->projects->count() > 0)
                        <div class="space-y-3">
                            @foreach($client->projects->take(3) as $project)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $project->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $project->status ?? 'غير محدد' }}</p>
                                </div>
                                <span class="text-xs text-gray-500">{{ $project->created_at->format('Y/m/d') }}</span>
                            </div>
                            @endforeach
                            @if($client->projects->count() > 3)
                            <p class="text-xs text-gray-500 text-center">و {{ $client->projects->count() - 3 }} مشروع آخر</p>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">لا توجد مشاريع</p>
                    @endif
                </div>

                <!-- Sales Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">المبيعات</h3>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $client->sales->count() }}
                        </span>
                    </div>
                    @if($client->sales->count() > 0)
                        <div class="space-y-3">
                            @foreach($client->sales->take(3) as $sale)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $sale->product_name ?? 'منتج غير محدد' }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($sale->amount) }} ج.م</p>
                                </div>
                                <span class="text-xs text-gray-500">{{ $sale->sale_date ? $sale->sale_date->format('Y/m/d') : 'غير محدد' }}</span>
                            </div>
                            @endforeach
                            @if($client->sales->count() > 3)
                            <p class="text-xs text-gray-500 text-center">و {{ $client->sales->count() - 3 }} عملية بيع أخرى</p>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">لا توجد مبيعات</p>
                    @endif
                </div>
            </div>

            <!-- تقارير الخدمة (رفع من الإدارة — تظهر في بوابة العميل) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">تقارير الخدمة</h3>
                        <p class="text-sm text-gray-600 mt-1">رفع تقارير عن الخدمة؛ يستطيع العميل عرضها وتنزيلها من بوابة العميل.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800 shrink-0">
                        {{ $client->serviceReports->count() }} تقرير
                    </span>
                </div>

                @can('edit-clients')
                <form method="POST" action="{{ route('clients.service-reports.store', $client) }}" enctype="multipart/form-data" class="mb-8 pb-8 border-b border-gray-200 space-y-4">
                    @csrf
                    @if($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">عنوان التقرير <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="مثال: تقرير أداء أسبوعي">
                            @error('title')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الملف <span class="text-red-500">*</span></label>
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.txt"
                                   class="block w-full text-sm text-gray-600 file:ml-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">PDF، Word، Excel، PowerPoint، ZIP أو نص — بحد أقصى 20 ميجابايت</p>
                            @error('file')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">وصف أو ملاحظات (اختياري)</label>
                        <textarea name="description" rows="2" maxlength="5000"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="نبذة للعميل تظهر في بوابة العميل">{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                        رفع التقرير
                    </button>
                </form>
                @endcan

                @if($client->serviceReports->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-700">العنوان</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-700">التاريخ</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-700">رفع بواسطة</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-700 w-48">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($client->serviceReports as $report)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $report->title }}</div>
                                            @if($report->original_filename)
                                                <div class="text-xs text-gray-500">{{ $report->original_filename }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $report->created_at->format('Y/m/d') }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $report->uploader?->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-left">
                                            <div class="flex flex-wrap items-center justify-end gap-2">
                                                @can('view-clients')
                                                <a href="{{ route('clients.service-reports.download', [$client, $report]) }}" class="text-blue-600 hover:text-blue-800 font-medium">تنزيل</a>
                                                @endcan
                                                @can('edit-clients')
                                                <form method="POST" action="{{ route('clients.service-reports.destroy', [$client, $report]) }}" class="inline" onsubmit="return confirm('حذف هذا التقرير؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">حذف</button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-6">لا توجد تقارير مرفوعة بعد.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">إحصائيات سريعة</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">تاريخ الإضافة</span>
                        <span class="text-sm font-medium text-gray-900">{{ $client->created_at->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">آخر تحديث</span>
                        <span class="text-sm font-medium text-gray-900">{{ $client->updated_at->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">عدد المشاريع</span>
                        <span class="text-sm font-medium text-blue-600">{{ $client->projects->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">عدد المبيعات</span>
                        <span class="text-sm font-medium text-green-600">{{ $client->sales->count() }}</span>
                    </div>
                    @if($client->sales->count() > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">إجمالي المبيعات</span>
                        <span class="text-sm font-medium text-orange-600">{{ number_format($client->sales->sum('amount')) }} ج.م</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">الإجراءات</h3>
                <div class="space-y-3">
                    <a href="{{ route('clients.edit', $client) }}" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        تعديل العميل
                    </a>
                    <a href="mailto:{{ $client->email }}" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        إرسال بريد إلكتروني
                    </a>
                    @if($client->phone)
                    <a href="tel:{{ $client->phone }}" class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        الاتصال
                    </a>
                    <button onclick="openWhatsAppContact('{{ $client->phone }}', '{{ addslashes($client->name) }}')" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                        واتساب
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
