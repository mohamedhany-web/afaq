@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $mode = $mode ?? 'clients';
    $clearUrl = $clearUrl ?? url()->current();
    $preserve = $preserve ?? [];
    $filterKeys = $filterKeys ?? [];
    $hasActive = $hasActive ?? false;
    $showAdvanced = request()->boolean('advanced') || collect($filterKeys)->contains(fn ($k) => in_array($k, ['deal_stage', 'has_deals', 'unassigned', 'client_type', 'lead_source', 'created_from', 'created_to', 'project_id', 'min_value', 'max_value', 'updated_from', 'updated_to', 'show_closed', 'type', 'city', 'property_type', 'date_from', 'date_to', 'from', 'to', 'client_status', 'client_lead_stage', 'client_unassigned', 'overdue_only'], true) && request()->filled($k));
    $inputClass = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm';
    $labelClass = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $salesRepValue = request('sales_rep', request('user_id', request('assignee')));
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6" x-data="{ advanced: {{ $showAdvanced ? 'true' : 'false' }} }">
    <form method="GET" action="{{ $action ?? '' }}">
        @foreach($preserve as $name => $value)
            @if($value !== null && $value !== '')
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endif
        @endforeach

        <div class="flex flex-col gap-3">
            <div class="flex flex-col lg:flex-row gap-3 lg:items-end flex-wrap">
                @if(in_array('search', $filterKeys, true))
                <div class="flex-1 min-w-[200px]">
                    <label class="{{ $labelClass }}">بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="{{ $searchPlaceholder ?? 'بحث...' }}"
                           class="{{ $inputClass }}">
                </div>
                @endif

                @if(($showSalesRepFilter ?? false) && in_array('sales_rep', $filterKeys, true))
                <div class="w-full sm:w-48">
                    <label class="{{ $labelClass }}">مندوب المبيعات</label>
                    <select name="sales_rep" class="{{ $inputClass }}">
                        <option value="">كل المندوبين</option>
                        @foreach($salesReps ?? [] as $rep)
                        <option value="{{ $rep->id }}" @selected((string) $salesRepValue === (string) $rep->id)>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(in_array('status', $filterKeys, true))
                <div class="w-full sm:w-44">
                    <label class="{{ $labelClass }}">{{ $statusLabel ?? 'الحالة' }}</label>
                    <select name="status" class="{{ $inputClass }}">
                        <option value="">الكل</option>
                        @foreach($statusOptions ?? [] as $val => $txt)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $txt }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(in_array('lead_stage', $filterKeys, true))
                <div class="w-full sm:w-44">
                    <label class="{{ $labelClass }}">مرحلة الرحلة</label>
                    <select name="lead_stage" class="{{ $inputClass }}">
                        <option value="">كل المراحل</option>
                        @foreach($stageLabels ?? [] as $key => $label)
                        <option value="{{ $key }}" @selected(request('lead_stage') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(in_array('stage', $filterKeys, true))
                <div class="w-full sm:w-44">
                    <label class="{{ $labelClass }}">مرحلة الصفقة</label>
                    <select name="stage" class="{{ $inputClass }}">
                        <option value="">{{ $stagePlaceholder ?? 'كل المراحل' }}</option>
                        @foreach($stageLabels ?? [] as $key => $label)
                        <option value="{{ $key }}" @selected(request('stage') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(in_array('date', $filterKeys, true))
                <div class="w-full sm:w-40">
                    <label class="{{ $labelClass }}">التاريخ</label>
                    <input type="date" name="date" value="{{ request('date', $dateValue ?? '') }}" class="{{ $inputClass }}">
                </div>
                @endif

                @if(in_array('from', $filterKeys, true))
                <div class="w-full sm:w-40">
                    <label class="{{ $labelClass }}">من</label>
                    <input type="date" name="from" value="{{ request('from', $fromValue ?? '') }}" class="{{ $inputClass }}">
                </div>
                @endif

                @if(in_array('to', $filterKeys, true))
                <div class="w-full sm:w-40">
                    <label class="{{ $labelClass }}">إلى</label>
                    <input type="date" name="to" value="{{ request('to', $toValue ?? '') }}" class="{{ $inputClass }}">
                </div>
                @endif

                @if(in_array('listing_status', $filterKeys, true))
                <div class="w-full sm:w-44">
                    <label class="{{ $labelClass }}">حالة العرض</label>
                    <select name="listing_status" class="{{ $inputClass }}">
                        <option value="">الكل</option>
                        @foreach($listingStatuses ?? [] as $val => $txt)
                        <option value="{{ $val }}" @selected(request('listing_status') === $val)>{{ $txt }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(in_array('ownership_type', $filterKeys, true))
                <div class="w-full sm:w-44">
                    <label class="{{ $labelClass }}">نوع الملكية</label>
                    <select name="ownership_type" class="{{ $inputClass }}">
                        <option value="">الكل</option>
                        @foreach($ownershipTypes ?? [] as $val => $txt)
                        <option value="{{ $val }}" @selected(request('ownership_type') === $val)>{{ $txt }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal"
                            style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">تطبيق</button>
                    @if($hasActive)
                    <a href="{{ $clearUrl }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح الفلاتر</a>
                    @endif
                    @if(!empty($advancedKeys))
                    <button type="button" @click="advanced = !advanced"
                            class="px-4 py-2.5 rounded-xl border-2 text-sm font-semibold font-tajawal hover:bg-gray-50"
                            :class="advanced ? 'border-gray-300 text-gray-800 bg-gray-50' : 'border-gray-200 text-gray-600'"
                            style="border-color: {{ $themeColor }}30;">
                        <span x-text="advanced ? 'إخفاء المتقدم' : 'فلاتر متقدمة'"></span>
                    </button>
                    @endif
                </div>
            </div>

            @if(!empty($advancedKeys))
            <div x-show="advanced" class="pt-2 border-t border-gray-100">
                <input type="hidden" name="advanced" value="1" x-bind:disabled="!advanced">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3">
                    @foreach($advancedKeys as $key)
                        @switch($key)
                            @case('deal_stage')
                            <div>
                                <label class="{{ $labelClass }}">مرحلة صفقة العميل</label>
                                <select name="deal_stage" class="{{ $inputClass }}">
                                    <option value="">أي مرحلة</option>
                                    @foreach($stageLabels ?? [] as $k => $lbl)
                                    <option value="{{ $k }}" @selected(request('deal_stage') === $k)>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('has_deals')
                            <div>
                                <label class="{{ $labelClass }}">وجود صفقات</label>
                                <select name="has_deals" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    <option value="1" @selected(request('has_deals') === '1')>لديهم صفقات</option>
                                    <option value="0" @selected(request('has_deals') === '0')>بدون صفقات</option>
                                </select>
                            </div>
                            @break
                            @case('unassigned')
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer py-2.5 font-tajawal text-sm text-gray-700">
                                    <input type="checkbox" name="unassigned" value="1" @checked(request()->boolean('unassigned'))
                                           class="rounded border-gray-300" style="accent-color: {{ $themeColor }};">
                                    غير مُعيَّنين فقط
                                </label>
                            </div>
                            @break
                            @case('client_type')
                            <div>
                                <label class="{{ $labelClass }}">تصنيف العميل</label>
                                <select name="client_type" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    @foreach(\App\Models\Client::typeLabels() as $key => $lbl)
                                    <option value="{{ $key }}" @selected(request('client_type') === $key)>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('lead_source')
                            <div>
                                <label class="{{ $labelClass }}">مصدر العميل</label>
                                <select name="lead_source" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    @foreach(\App\Models\Client::leadSourceLabels() as $key => $lbl)
                                    <option value="{{ $key }}" @selected(request('lead_source') === $key)>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('created_from')
                            <div>
                                <label class="{{ $labelClass }}">أُضيف من</label>
                                <input type="date" name="created_from" value="{{ request('created_from') }}" class="{{ $inputClass }}">
                            </div>
                            @break
                            @case('created_to')
                            <div>
                                <label class="{{ $labelClass }}">أُضيف إلى</label>
                                <input type="date" name="created_to" value="{{ request('created_to') }}" class="{{ $inputClass }}">
                            </div>
                            @break
                            @case('project_id')
                            <div>
                                <label class="{{ $labelClass }}">المشروع</label>
                                <select name="project_id" class="{{ $inputClass }}">
                                    <option value="">كل المشاريع</option>
                                    @foreach($projects ?? [] as $project)
                                    <option value="{{ $project->id }}" @selected((string) request('project_id') === (string) $project->id)>{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('min_value')
                            <div>
                                <label class="{{ $labelClass }}">قيمة من</label>
                                <input type="number" name="min_value" value="{{ request('min_value') }}" min="0" step="1000" placeholder="0" class="{{ $inputClass }}">
                            </div>
                            @break
                            @case('max_value')
                            <div>
                                <label class="{{ $labelClass }}">قيمة إلى</label>
                                <input type="number" name="max_value" value="{{ request('max_value') }}" min="0" step="1000" class="{{ $inputClass }}">
                            </div>
                            @break
                            @case('updated_from')
                            <div>
                                <label class="{{ $labelClass }}">تحديث من</label>
                                <input type="date" name="updated_from" value="{{ request('updated_from') }}" class="{{ $inputClass }}">
                            </div>
                            @break
                            @case('updated_to')
                            <div>
                                <label class="{{ $labelClass }}">تحديث إلى</label>
                                <input type="date" name="updated_to" value="{{ request('updated_to') }}" class="{{ $inputClass }}">
                            </div>
                            @break
                            @case('show_closed')
                            <div class="flex items-end sm:col-span-2">
                                <label class="flex items-center gap-2 cursor-pointer py-2.5 font-tajawal text-sm text-gray-700">
                                    <input type="checkbox" name="show_closed" value="1" @checked(request()->boolean('show_closed'))
                                           class="rounded border-gray-300" style="accent-color: {{ $themeColor }};">
                                    إظهار الصفقات المغلقة في Kanban
                                </label>
                            </div>
                            @break
                            @case('type')
                            <div>
                                <label class="{{ $labelClass }}">نوع النشاط</label>
                                <select name="type" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    @foreach($typeLabels ?? [] as $k => $lbl)
                                    <option value="{{ $k }}" @selected(request('type') === $k)>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('client_status')
                            <div>
                                <label class="{{ $labelClass }}">حالة العميل</label>
                                <select name="client_status" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    @foreach($clientStatusOptions ?? [] as $val => $txt)
                                    <option value="{{ $val }}" @selected(request('client_status') === $val)>{{ $txt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('client_lead_stage')
                            <div>
                                <label class="{{ $labelClass }}">مرحلة العميل</label>
                                <select name="client_lead_stage" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    @foreach($stageLabels ?? [] as $k => $lbl)
                                    <option value="{{ $k }}" @selected(request('client_lead_stage') === $k)>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('client_unassigned')
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer py-2.5 font-tajawal text-sm text-gray-700">
                                    <input type="checkbox" name="client_unassigned" value="1" @checked(request()->boolean('client_unassigned'))
                                           class="rounded border-gray-300" style="accent-color: {{ $themeColor }};">
                                    عملاء غير مُعيَّنين فقط
                                </label>
                            </div>
                            @break
                            @case('overdue_only')
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer py-2.5 font-tajawal text-sm text-gray-700">
                                    <input type="checkbox" name="overdue_only" value="1" @checked(request()->boolean('overdue_only'))
                                           class="rounded border-gray-300" style="accent-color: {{ $themeColor }};">
                                    متأخرة فقط
                                </label>
                            </div>
                            @break
                            @case('priority')
                            <div>
                                <label class="{{ $labelClass }}">الأولوية</label>
                                <select name="priority" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    @foreach($priorityOptions ?? [] as $k => $lbl)
                                    <option value="{{ $k }}" @selected(request('priority') === $k)>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('listing_status')
                            <div>
                                <label class="{{ $labelClass }}">حالة العرض</label>
                                <select name="listing_status" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    @foreach($listingStatuses ?? [] as $val => $txt)
                                    <option value="{{ $val }}" @selected(request('listing_status') === $val)>{{ $txt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('property_type')
                            <div>
                                <label class="{{ $labelClass }}">نوع العقار</label>
                                <select name="property_type" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    @foreach($propertyTypes ?? [] as $val => $txt)
                                    <option value="{{ $val }}" @selected(request('property_type') === $val)>{{ $txt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('ownership_type')
                            <div>
                                <label class="{{ $labelClass }}">نوع الملكية</label>
                                <select name="ownership_type" class="{{ $inputClass }}">
                                    <option value="">الكل</option>
                                    @foreach($ownershipTypes ?? [] as $val => $txt)
                                    <option value="{{ $val }}" @selected(request('ownership_type') === $val)>{{ $txt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @break
                            @case('city')
                            <div>
                                <label class="{{ $labelClass }}">المدينة</label>
                                <input type="text" name="city" value="{{ request('city') }}" placeholder="مثال: الرياض" class="{{ $inputClass }}">
                            </div>
                            @break
                            @case('date_from')
                            <div>
                                <label class="{{ $labelClass }}">من تاريخ</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="{{ $inputClass }}">
                            </div>
                            @break
                            @case('date_to')
                            <div>
                                <label class="{{ $labelClass }}">إلى تاريخ</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="{{ $inputClass }}">
                            </div>
                            @break
                        @endswitch
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </form>
</div>
