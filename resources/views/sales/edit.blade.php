@extends('layouts.app')

@section('page-title', 'تعديل عملية البيع')

@section('content')
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">تعديل عملية البيع</h1>
                <p class="text-gray-600">تعديل عملية البيع: {{ $sale->product_service }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.show', $sale) }}" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    عرض
                </a>
                <a href="{{ route('sales.index') }}" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <form action="{{ route('sales.update', $sale) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product/Service -->
                <div class="md:col-span-2">
                    <label for="product_service" class="block text-sm font-medium text-gray-700 mb-2">المنتج/الخدمة <span class="text-red-500">*</span></label>
                    <input type="text" name="product_service" id="product_service" value="{{ old('product_service', $sale->product_service) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('product_service') border-red-500 @enderror"
                           placeholder="أدخل اسم المنتج أو الخدمة">
                    @error('product_service')
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
                    @include('partials.client-search-select', ['required' => true, 'value' => old('client_id', $sale->client_id), 'label' => $sale->client ? \App\Http\Controllers\ClientSearchController::formatLabel($sale->client) : null, 'inputClass' => 'w-full px-4 py-3 border border-gray-300 rounded-xl', 'crmScope' => false])
                    @error('client_id')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Assigned To -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">مندوب المبيعات <span class="text-red-500">*</span></label>
                    <select name="assigned_to" id="assigned_to" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('assigned_to') border-red-500 @enderror">
                        <option value="">اختر مندوب المبيعات</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $sale->assigned_to) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Estimated Value -->
                <div>
                    <label for="estimated_value" class="block text-sm font-medium text-gray-700 mb-2">القيمة المتوقعة <span class="text-red-500">*</span></label>
                    <input type="number" name="estimated_value" id="estimated_value" value="{{ old('estimated_value', $sale->estimated_value) }}" required step="0.01" min="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('estimated_value') border-red-500 @enderror"
                           placeholder="أدخل القيمة المتوقعة">
                    @error('estimated_value')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Actual Value -->
                <div>
                    <label for="actual_value" class="block text-sm font-medium text-gray-700 mb-2">القيمة الفعلية</label>
                    <input type="number" name="actual_value" id="actual_value" value="{{ old('actual_value', $sale->actual_value) }}" step="0.01" min="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('actual_value') border-red-500 @enderror"
                           placeholder="أدخل القيمة الفعلية">
                    @error('actual_value')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Stage -->
                <div>
                    <label for="stage" class="block text-sm font-medium text-gray-700 mb-2">المرحلة <span class="text-red-500">*</span></label>
                    <select name="stage" id="stage" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('stage') border-red-500 @enderror">
                        <option value="">اختر المرحلة</option>
                        @foreach($stages as $stage)
                            <option value="{{ $stage }}" {{ old('stage', $sale->stage) == $stage ? 'selected' : '' }}>
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
                    @error('stage')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Probability Percentage -->
                <div>
                    <label for="probability_percentage" class="block text-sm font-medium text-gray-700 mb-2">نسبة الاحتمال <span class="text-red-500">*</span></label>
                    <input type="number" name="probability_percentage" id="probability_percentage" value="{{ old('probability_percentage', $sale->probability_percentage) }}" required min="0" max="100"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('probability_percentage') border-red-500 @enderror"
                           placeholder="أدخل نسبة الاحتمال من 0 إلى 100">
                    @error('probability_percentage')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Expected Close Date -->
                <div>
                    <label for="expected_close_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإغلاق المتوقع</label>
                    <input type="date" name="expected_close_date" id="expected_close_date" value="{{ old('expected_close_date', $sale->expected_close_date ? $sale->expected_close_date->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('expected_close_date') border-red-500 @enderror">
                    @error('expected_close_date')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Actual Close Date -->
                <div>
                    <label for="actual_close_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإغلاق الفعلي</label>
                    <input type="date" name="actual_close_date" id="actual_close_date" value="{{ old('actual_close_date', $sale->actual_close_date ? $sale->actual_close_date->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('actual_close_date') border-red-500 @enderror">
                    @error('actual_close_date')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Lead Source -->
                <div>
                    <label for="lead_source" class="block text-sm font-medium text-gray-700 mb-2">مصدر العميل المحتمل</label>
                    <select name="lead_source" id="lead_source"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('lead_source') border-red-500 @enderror">
                        <option value="">اختر المصدر</option>
                        @foreach($leadSources as $source)
                            <option value="{{ $source }}" {{ old('lead_source', $sale->lead_source) == $source ? 'selected' : '' }}>
                                @if($source == 'website') الموقع الإلكتروني
                                @elseif($source == 'referral') إحالة
                                @elseif($source == 'cold_call') مكالمة باردة
                                @elseif($source == 'email') البريد الإلكتروني
                                @elseif($source == 'social_media') وسائل التواصل الاجتماعي
                                @elseif($source == 'advertisement') إعلان
                                @elseif($source == 'event') فعالية
                                @elseif($source == 'other') أخرى
                                @else {{ $source }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('lead_source')
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
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $sale->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Competitors -->
            <div>
                <label for="competitors" class="block text-sm font-medium text-gray-700 mb-2">المنافسون</label>
                <div id="competitors-container">
                    @if($sale->competitors && count($sale->competitors) > 0)
                        @foreach($sale->competitors as $competitor)
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" name="competitors[]" value="{{ $competitor }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل اسم المنافس">
                            <button type="button" onclick="removeCompetitor(this)" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" name="competitors[]" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل اسم المنافس">
                            <button type="button" onclick="removeCompetitor(this)" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addCompetitor()" class="mt-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    إضافة منافس
                </button>
            </div>

            <!-- Decision Makers -->
            <div>
                <label for="decision_makers" class="block text-sm font-medium text-gray-700 mb-2">صانعو القرار</label>
                <div id="decision-makers-container">
                    @if($sale->decision_makers && count($sale->decision_makers) > 0)
                        @foreach($sale->decision_makers as $maker)
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" name="decision_makers[]" value="{{ $maker }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل اسم صانع القرار">
                            <button type="button" onclick="removeDecisionMaker(this)" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" name="decision_makers[]" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل اسم صانع القرار">
                            <button type="button" onclick="removeDecisionMaker(this)" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addDecisionMaker()" class="mt-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    إضافة صانع قرار
                </button>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" id="notes" rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('notes') border-red-500 @enderror"
                          placeholder="أدخل أي ملاحظات إضافية">{{ old('notes', $sale->notes) }}</textarea>
                @error('notes')
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
                <a href="{{ route('sales.show', $sale) }}" class="px-6 py-3 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors duration-200">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addCompetitor() {
    const container = document.getElementById('competitors-container');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="competitors[]" 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               placeholder="أدخل اسم المنافس">
        <button type="button" onclick="removeCompetitor(this)" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeCompetitor(button) {
    button.parentElement.remove();
}

function addDecisionMaker() {
    const container = document.getElementById('decision-makers-container');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="decision_makers[]" 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               placeholder="أدخل اسم صانع القرار">
        <button type="button" onclick="removeDecisionMaker(this)" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeDecisionMaker(button) {
    button.parentElement.remove();
}
</script>
@endsection
