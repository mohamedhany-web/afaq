@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $selectedTypes = old('property_types', isset($project) ? $project->resolvedPropertyTypes() : ['residential']);
    if (! is_array($selectedTypes)) {
        $selectedTypes = \App\Models\Project::normalizePropertyTypes($selectedTypes);
    }
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        بيانات المشروع العقاري
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 font-tajawal">
        <div class="sm:col-span-2 lg:col-span-3">
            <label class="{{ $label }}">اسم المشروع / الكمبوند *</label>
            <input name="name" required value="{{ old('name', $project->name ?? '') }}" class="{{ $input }}" placeholder="مثال: كمبوند سيتي فيو">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="sm:col-span-2 lg:col-span-3">
            <label class="{{ $label }}">الوصف</label>
            <textarea name="description" rows="3" class="{{ $input }}" placeholder="وصف المشروع، المرافق، المميزات...">{{ old('description', $project->description ?? '') }}</textarea>
        </div>
        <div>
            <label class="{{ $label }}">المدينة</label>
            <input name="city" value="{{ old('city', $project->city ?? '') }}" class="{{ $input }}" placeholder="القاهرة الجديدة">
        </div>
        <div>
            <label class="{{ $label }}">المنطقة / الموقع</label>
            <input name="location" value="{{ old('location', $project->location ?? '') }}" class="{{ $input }}" placeholder="التجمع الخامس">
        </div>
        <div>
            <label class="{{ $label }}">مساحة الأرض (م²)</label>
            <input type="number" name="land_area_m2" min="0" step="0.01" value="{{ old('land_area_m2', $project->land_area_m2 ?? '') }}" class="{{ $input }}">
        </div>
        <div class="sm:col-span-2 lg:col-span-3">
            <label class="{{ $label }}">تصنيف المشروع * <span class="font-normal text-gray-400">(يمكن اختيار أكثر من تصنيف)</span></label>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach(\App\Models\Project::PROPERTY_TYPES as $k => $t)
                <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border-2 cursor-pointer text-sm transition
                    {{ in_array($k, $selectedTypes, true) ? '' : 'border-gray-200 bg-gray-50' }}"
                    style="{{ in_array($k, $selectedTypes, true) ? 'border-color:' . $themeColor . '; background:' . $themeColor . '12; color:' . $themeColor : '' }}">
                    <input type="checkbox" name="property_types[]" value="{{ $k }}" class="rounded border-gray-300" @checked(in_array($k, $selectedTypes, true))>
                    {{ $t }}
                </label>
                @endforeach
            </div>
            @error('property_types')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">نوع التطوير</label>
            <select name="project_type" class="{{ $input }}">
                <option value="">— اختر —</option>
                @foreach(\App\Models\Project::DEVELOPMENT_TYPES as $k => $t)
                <option value="{{ $k }}" @selected(old('project_type', $project->project_type ?? '') === $k)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">حالة العرض *</label>
            <select name="listing_status" required class="{{ $input }}">
                @foreach(\App\Models\Project::LISTING_STATUSES as $k => $t)
                <option value="{{ $k }}" @selected(old('listing_status', $project->listing_status ?? 'active') === $k)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">إجمالي الوحدات</label>
            <input type="number" name="total_units" min="0" value="{{ old('total_units', $project->total_units ?? 0) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">السعر من (ج.م)</label>
            <input type="number" name="price_from" min="0" step="0.01" value="{{ old('price_from', $project->price_from ?? '') }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">السعر إلى (ج.م)</label>
            <input type="number" name="price_to" min="0" step="0.01" value="{{ old('price_to', $project->price_to ?? '') }}" class="{{ $input }}">
        </div>
    </div>
</div>
