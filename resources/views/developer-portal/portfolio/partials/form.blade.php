@php
    $p = $portfolio ?? null;
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full font-tajawal">
    <div class="{{ $sectionHeader }}" style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        بيانات المشروع السابق
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div class="sm:col-span-2">
            <label class="{{ $label }}">العنوان *</label>
            <input name="title" required value="{{ old('title', $p->title ?? '') }}" class="{{ $input }}" placeholder="اسم المشروع أو الكمبوند">
            @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="sm:col-span-2">
            <label class="{{ $label }}">الوصف</label>
            <textarea name="description" rows="4" class="{{ $input }}" placeholder="تفاصيل المشروع وإنجازاته...">{{ old('description', $p->description ?? '') }}</textarea>
        </div>
        <div>
            <label class="{{ $label }}">المدينة</label>
            <input name="city" value="{{ old('city', $p->city ?? '') }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">الموقع</label>
            <input name="location" value="{{ old('location', $p->location ?? '') }}" class="{{ $input }}">
        </div>
        <div>
            <label class="{{ $label }}">نوع المشروع</label>
            <input name="project_type" value="{{ old('project_type', $p->project_type ?? '') }}" class="{{ $input }}" placeholder="سكني / تجاري...">
        </div>
        <div>
            <label class="{{ $label }}">سنة التنفيذ</label>
            <input type="number" name="year" min="1900" max="2100" value="{{ old('year', $p->year ?? '') }}" class="{{ $input }}">
        </div>
        <div class="sm:col-span-2 flex items-center gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
            <input type="checkbox" name="is_published" value="1" id="is_published" class="rounded border-gray-300 w-4 h-4"
                   @checked(old('is_published', $p->is_published ?? true))>
            <label for="is_published" class="text-sm font-semibold text-gray-700 cursor-pointer">منشور للعرض في ملف المطور</label>
        </div>
    </div>
</div>
