@extends('layouts.app')

@section('page-title', 'إضافة عقد جديد')

@section('content')
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">إضافة عقد جديد</h1>
                <p class="text-gray-600">أضف عقد جديد للعملاء</p>
            </div>
            <a href="{{ route('contracts.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 flex items-center shadow-sm">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <form action="{{ route('contracts.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">عنوان العقد <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('title') border-red-500 @enderror"
                           placeholder="أدخل عنوان العقد">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">وصف العقد</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('description') border-red-500 @enderror"
                              placeholder="أدخل وصف العقد">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Client -->
                <div>
                    @include('partials.client-search-select', ['required' => true, 'value' => old('client_id'), 'inputClass' => 'w-full px-4 py-3 border border-gray-300 rounded-xl', 'crmScope' => false])
                    @error('client_id')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">المشروع المرتبط</label>
                    <select name="project_id" id="project_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('project_id') border-red-500 @enderror">
                        <option value="">اختر المشروع</option>
                        @if($projects && $projects->count() > 0)
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} ({{ $project->status_name }})
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>لا توجد مشاريع متاحة</option>
                        @endif
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    @if($projects && $projects->count() == 0)
                        <p class="mt-1 text-sm text-orange-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            لا توجد مشاريع متاحة للربط. تأكد من وجود مشاريع بحالة "قيد التخطيط" أو "قيد التنفيذ"
                        </p>
                    @endif
                </div>

                <!-- Contract Type -->
                <div>
                    <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-2">نوع العقد <span class="text-red-500">*</span></label>
                    <select name="contract_type" id="contract_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('contract_type') border-red-500 @enderror">
                        <option value="">اختر نوع العقد</option>
                        @foreach($contractTypes as $type)
                            <option value="{{ $type }}" {{ old('contract_type') == $type ? 'selected' : '' }}>
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
                    @error('contract_type')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('status') border-red-500 @enderror">
                        <option value="">اختر الحالة</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                @if($status == 'draft') مسودة
                                @elseif($status == 'active') نشط
                                @else {{ $status }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ البدء <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Value -->
                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-2">قيمة العقد <span class="text-red-500">*</span></label>
                    <input type="number" name="value" id="value" value="{{ old('value') }}" required step="0.01" min="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('value') border-red-500 @enderror"
                           placeholder="أدخل قيمة العقد">
                    @error('value')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Renewal Notice Days -->
                <div>
                    <label for="renewal_notice_days" class="block text-sm font-medium text-gray-700 mb-2">أيام إشعار التجديد</label>
                    <input type="number" name="renewal_notice_days" id="renewal_notice_days" value="{{ old('renewal_notice_days', 30) }}" min="0" max="365"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('renewal_notice_days') border-red-500 @enderror"
                           placeholder="عدد الأيام قبل انتهاء العقد">
                    @error('renewal_notice_days')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Auto Renewal -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" name="auto_renewal" id="auto_renewal" value="1" {{ old('auto_renewal') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="auto_renewal" class="mr-2 block text-sm text-gray-900">
                            تجديد تلقائي
                        </label>
                    </div>
                </div>

                <!-- Approved By -->
                <div>
                    <label for="approved_by" class="block text-sm font-medium text-gray-700 mb-2">معتمد من</label>
                    <select name="approved_by" id="approved_by"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('approved_by') border-red-500 @enderror">
                        <option value="">اختر المعتمد</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('approved_by') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('approved_by')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div>
                <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">الشروط والأحكام</label>
                <textarea name="terms_conditions" id="terms_conditions" rows="6"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('terms_conditions') border-red-500 @enderror"
                          placeholder="أدخل شروط وأحكام العقد">{{ old('terms_conditions') }}</textarea>
                @error('terms_conditions')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('contracts.index') }}" class="px-6 py-3 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    إضافة العقد
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

