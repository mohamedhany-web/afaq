@php $p = $portfolio ?? null; $input='w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm'; $label='block text-xs font-bold text-gray-500 mb-1.5'; @endphp
<div class="bg-white rounded-2xl border p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2"><label class="{{ $label }}">العنوان *</label><input name="title" required value="{{ old('title', $p->title ?? '') }}" class="{{ $input }}"></div>
    <div class="sm:col-span-2"><label class="{{ $label }}">الوصف</label><textarea name="description" rows="3" class="{{ $input }}">{{ old('description', $p->description ?? '') }}</textarea></div>
    <div><label class="{{ $label }}">المدينة</label><input name="city" value="{{ old('city', $p->city ?? '') }}" class="{{ $input }}"></div>
    <div><label class="{{ $label }}">الموقع</label><input name="location" value="{{ old('location', $p->location ?? '') }}" class="{{ $input }}"></div>
    <div><label class="{{ $label }}">نوع المشروع</label><input name="project_type" value="{{ old('project_type', $p->project_type ?? '') }}" class="{{ $input }}"></div>
    <div><label class="{{ $label }}">السنة</label><input type="number" name="year" value="{{ old('year', $p->year ?? '') }}" class="{{ $input }}"></div>
    <div class="flex items-center gap-2 pt-6"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $p->is_published ?? true))><label class="text-sm font-semibold">منشور للعرض</label></div>
</div>
