@extends('layouts.app')

@section('page-title', 'رفع تقرير لعميل')

@section('content')
<div class="w-full max-w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">رفع تقرير لعميل</h1>
                <p class="text-gray-600">اختر العميل وارفع ملف التقرير؛ سيظهر في بوابة العميل ضمن «تقارير الخدمة».</p>
            </div>
            <a href="{{ route('client-service-reports.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 flex items-center justify-center shadow-sm shrink-0">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    @if(!\App\Models\Client::exists())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <p class="text-gray-600 text-lg">لا يوجد عملاء في النظام.</p>
            <a href="{{ route('clients.create') }}" class="mt-4 inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                إضافة عميل جديد
            </a>
        </div>
    @else
        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <form action="{{ route('client-service-reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- العميل -->
                    <div class="lg:col-span-2">
                        @include('partials.client-search-select', ['required' => true, 'value' => old('client_id'), 'inputClass' => 'w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500', 'crmScope' => false])
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 ml-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- عنوان التقرير -->
                    <div class="lg:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">عنوان التقرير <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('title') border-red-500 @enderror"
                               placeholder="مثال: تقرير أداء الخدمة — الربع الأول">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 ml-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- الوصف -->
                    <div class="lg:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">وصف أو ملاحظات (اختياري)</label>
                        <textarea name="description" id="description" rows="4" maxlength="5000"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('description') border-red-500 @enderror"
                                  placeholder="نبذة تظهر للعميل في بوابة العميل بجانب التقرير">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 ml-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- الملف -->
                    <div class="lg:col-span-2">
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">ملف التقرير <span class="text-red-500">*</span></label>
                        <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/80 px-4 py-6 transition-colors hover:border-blue-200 hover:bg-blue-50/30 @error('file') border-red-300 bg-red-50/50 @enderror">
                            <input type="file" name="file" id="file" required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.txt"
                                   class="block w-full text-sm text-gray-700 file:cursor-pointer file:ml-4 file:py-3 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:shadow-sm file:transition-colors">
                            <p class="mt-3 text-sm text-gray-600 leading-relaxed">
                                الصيغ المسموحة: PDF، Word، Excel، PowerPoint، ZIP، نص — بحد أقصى <span class="font-semibold text-gray-800">20 ميجابايت</span>.
                            </p>
                        </div>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 ml-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('client-service-reports.index') }}" class="text-center px-6 py-3 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200 font-medium">
                        إلغاء
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center shadow-sm font-semibold">
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        رفع التقرير
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
