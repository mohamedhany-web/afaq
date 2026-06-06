@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $campaign = $campaign ?? null;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="{{ $label }}">اسم الحملة *</label>
        <input type="text" name="name" value="{{ old('name', $campaign?->name) }}" required class="{{ $input }}">
    </div>
    <div>
        <label class="{{ $label }}">القناة *</label>
        <select name="channel" required class="{{ $input }}">
            @foreach($channels as $key => $chLabel)
            <option value="{{ $key }}" @selected(old('channel', $campaign?->channel) === $key)>{{ $chLabel }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="{{ $label }}">الحالة *</label>
        <select name="status" required class="{{ $input }}">
            @foreach($statuses as $key => $stLabel)
            <option value="{{ $key }}" @selected(old('status', $campaign?->status ?? 'draft') === $key)>{{ $stLabel }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="{{ $label }}">الميزانية</label>
        <input type="number" step="0.01" name="budget" value="{{ old('budget', $campaign?->budget) }}" class="{{ $input }}">
    </div>
    <div>
        <label class="{{ $label }}">المصروف</label>
        <input type="number" step="0.01" name="spent_amount" value="{{ old('spent_amount', $campaign?->spent_amount ?? 0) }}" class="{{ $input }}">
    </div>
    <div>
        <label class="{{ $label }}">هدف Leads</label>
        <input type="number" name="target_leads" value="{{ old('target_leads', $campaign?->target_leads) }}" class="{{ $input }}">
    </div>
    <div>
        <label class="{{ $label }}">المشروع العقاري</label>
        <select name="project_id" class="{{ $input }}">
            <option value="">— بدون —</option>
            @foreach($projects as $project)
            <option value="{{ $project->id }}" @selected(old('project_id', $campaign?->project_id) == $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="{{ $label }}">مدير الحملة</label>
        <select name="manager_id" class="{{ $input }}">
            <option value="">— تلقائي —</option>
            @foreach($managers as $manager)
            <option value="{{ $manager->id }}" @selected(old('manager_id', $campaign?->manager_id) == $manager->id)>{{ $manager->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="{{ $label }}">تاريخ البداية</label>
        <input type="date" name="start_date" value="{{ old('start_date', $campaign?->start_date?->format('Y-m-d')) }}" class="{{ $input }}">
    </div>
    <div>
        <label class="{{ $label }}">تاريخ النهاية</label>
        <input type="date" name="end_date" value="{{ old('end_date', $campaign?->end_date?->format('Y-m-d')) }}" class="{{ $input }}">
    </div>
    <div class="sm:col-span-2">
        <label class="{{ $label }}">الوصف</label>
        <textarea name="description" rows="3" class="{{ $input }}">{{ old('description', $campaign?->description) }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="{{ $label }}">ملاحظات</label>
        <textarea name="notes" rows="2" class="{{ $input }}">{{ old('notes', $campaign?->notes) }}</textarea>
    </div>
</div>
