@php
    $isEdit = isset($project);
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        بيانات المشروع العقاري
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="sm:col-span-2 lg:col-span-3">
            <label class="{{ $label }}">اسم المشروع / الكمبوند *</label>
            <input name="name" value="{{ old('name', $project->name ?? '') }}" required class="{{ $input }}" placeholder="مثال: كمبوند سيتي فيو">
            @error('name')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
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
            <input type="number" name="land_area_m2" min="0" step="0.01" value="{{ old('land_area_m2', $project->land_area_m2 ?? '') }}" class="{{ $input }}" placeholder="31000">
        </div>
        <div>
            <label class="{{ $label }}">نوع العقار *</label>
            <select name="property_type" required class="{{ $input }}">
                @foreach(\App\Models\Project::PROPERTY_TYPES as $val => $txt)
                    <option value="{{ $val }}" @selected(old('property_type', $project->property_type ?? 'residential') === $val)>{{ $txt }}</option>
                @endforeach
            </select>
            @error('property_type')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">نوع التطوير</label>
            <select name="project_type" class="{{ $input }}">
                <option value="">— اختر —</option>
                @foreach(\App\Models\Project::DEVELOPMENT_TYPES as $val => $txt)
                    <option value="{{ $val }}" @selected(old('project_type', $project->project_type ?? '') === $val)>{{ $txt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">حالة العرض *</label>
            <select name="listing_status" required class="{{ $input }}">
                @foreach(\App\Models\Project::LISTING_STATUSES as $val => $txt)
                    <option value="{{ $val }}" @selected(old('listing_status', $project->listing_status ?? 'active') === $val)>{{ $txt }}</option>
                @endforeach
            </select>
            @error('listing_status')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

@include('projects.partials.ownership-fields', [
    'project' => $project ?? null,
    'themeColor' => $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor(),
    'input' => $input,
    'label' => $label,
    'sectionHeader' => $sectionHeader,
])

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        الوحدات والأسعار
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div>
            <label class="{{ $label }}">إجمالي الوحدات</label>
            <input type="number" name="total_units" min="0" value="{{ old('total_units', $project->total_units ?? 0) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">وحدات مباعة</label>
            <input type="number" name="sold_units" min="0" value="{{ old('sold_units', $project->sold_units ?? 0) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">وحدات متاحة</label>
            <input type="number" name="available_units" min="0" value="{{ old('available_units', $project->available_units ?? '') }}" class="{{ $input }}" placeholder="يُحسب تلقائياً إن تُرك فارغاً">
        </div>
        <div>
            <label class="{{ $label }}">نسبة البيع</label>
            <div class="px-4 py-3 rounded-xl bg-gray-50 border-2 border-gray-200 text-sm text-gray-600 font-tajawal">
                تُحدَّث تلقائياً من الوحدات
            </div>
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

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        فريق المبيعات والإدارة
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div>
            <label class="{{ $label }}">مدير المشروع / المبيعات</label>
            <select name="project_manager_id" class="{{ $input }}">
                <option value="">— المستخدم الحالي —</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('project_manager_id', $project->project_manager_id ?? auth()->id()) == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            @include('partials.client-search-select', [
                'required' => false,
                'value' => old('client_id', $project->client_id ?? ''),
                'inputClass' => $input,
                'crmScope' => false,
                'placeholder' => 'ابحث عن شريك / مطور (اختياري)...',
            ])
        </div>
        <div class="sm:col-span-2">
            <label class="{{ $label }}">فريق المبيعات</label>
            <select name="team_members[]" multiple class="{{ $input }} min-h-[120px]">
                @php $selectedTeam = old('team_members', isset($project) ? $project->teamMembers->pluck('id')->all() : []); @endphp
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(in_array($user->id, $selectedTeam))>{{ $user->name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1 font-tajawal">اضغط Ctrl لاختيار أكثر من موظف</p>
        </div>
        <div>
            <label class="{{ $label }}">تاريخ الإطلاق</label>
            <input type="date" name="start_date" value="{{ old('start_date', isset($project) && $project->start_date ? $project->start_date->format('Y-m-d') : date('Y-m-d')) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">تاريخ التسليم المتوقع</label>
            <input type="date" name="end_date" value="{{ old('end_date', isset($project) && $project->end_date ? $project->end_date->format('Y-m-d') : '') }}" class="{{ $input }}">
        </div>
    </div>
</div>

@include('projects.partials.map-picker', ['project' => $project ?? null, 'themeColor' => $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor()])
