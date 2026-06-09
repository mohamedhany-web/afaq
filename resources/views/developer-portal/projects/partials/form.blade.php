@php
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5';
@endphp
<div class="bg-white rounded-2xl border p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2"><label class="{{ $label }}">اسم المشروع *</label><input name="name" required value="{{ old('name', $project->name ?? '') }}" class="{{ $input }}"></div>
    <div class="sm:col-span-2"><label class="{{ $label }}">الوصف</label><textarea name="description" rows="3" class="{{ $input }}">{{ old('description', $project->description ?? '') }}</textarea></div>
    <div><label class="{{ $label }}">المدينة</label><input name="city" value="{{ old('city', $project->city ?? '') }}" class="{{ $input }}"></div>
    <div><label class="{{ $label }}">الموقع</label><input name="location" value="{{ old('location', $project->location ?? '') }}" class="{{ $input }}"></div>
    <div><label class="{{ $label }}">مساحة الأرض م²</label><input type="number" name="land_area_m2" min="0" step="0.01" value="{{ old('land_area_m2', $project->land_area_m2 ?? '') }}" class="{{ $input }}"></div>
    <div><label class="{{ $label }}">نوع العقار *</label><select name="property_type" required class="{{ $input }}">@foreach(\App\Models\Project::PROPERTY_TYPES as $k=>$t)<option value="{{ $k }}" @selected(old('property_type',$project->property_type??'residential')==$k)>{{ $t }}</option>@endforeach</select></div>
    <div><label class="{{ $label }}">نوع التطوير</label><select name="project_type" class="{{ $input }}"><option value="">—</option>@foreach(\App\Models\Project::DEVELOPMENT_TYPES as $k=>$t)<option value="{{ $k }}" @selected(old('project_type',$project->project_type??'')==$k)>{{ $t }}</option>@endforeach</select></div>
    <div><label class="{{ $label }}">حالة العرض *</label><select name="listing_status" required class="{{ $input }}">@foreach(\App\Models\Project::LISTING_STATUSES as $k=>$t)<option value="{{ $k }}" @selected(old('listing_status',$project->listing_status??'active')==$k)>{{ $t }}</option>@endforeach</select></div>
    <div><label class="{{ $label }}">إجمالي الوحدات</label><input type="number" name="total_units" min="0" value="{{ old('total_units', $project->total_units ?? 0) }}" class="{{ $input }}"></div>
    <div><label class="{{ $label }}">السعر من</label><input type="number" name="price_from" min="0" value="{{ old('price_from', $project->price_from ?? '') }}" class="{{ $input }}"></div>
    <div><label class="{{ $label }}">السعر إلى</label><input type="number" name="price_to" min="0" value="{{ old('price_to', $project->price_to ?? '') }}" class="{{ $input }}"></div>
</div>
