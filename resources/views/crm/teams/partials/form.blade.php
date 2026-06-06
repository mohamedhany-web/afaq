@php
    $isEdit = isset($team);
    $selectedMembers = old('member_ids', $isEdit ? $team->members->pluck('id')->all() : []);
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900" style="{{ $headerStyle }}">
        بيانات الفريق
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div class="sm:col-span-2">
            <label class="{{ $label }}">اسم الفريق *</label>
            <input name="name" value="{{ old('name', $team->name ?? '') }}" required class="{{ $input }}" placeholder="مثال: فريق المبيعات — القاهرة">
            @error('name')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">مدير المبيعات *</label>
            @if(!empty($lockManager))
                <input type="hidden" name="manager_id" value="{{ auth()->id() }}">
                <div class="{{ $input }} bg-gray-50 text-gray-800 font-semibold">{{ auth()->user()->name }}</div>
                <p class="mt-1 text-[11px] text-gray-400 font-tajawal">أنت مدير هذا الفريق — لا يمكن تعيينه لمدير آخر</p>
            @else
                <select name="manager_id" required class="{{ $input }}">
                    <option value="">— اختر المدير —</option>
                    @foreach($managers as $m)
                    <option value="{{ $m->id }}" @selected((int) old('manager_id', $team->manager_id ?? '') === $m->id)>{{ $m->name }}</option>
                    @endforeach
                </select>
            @endif
            @error('manager_id')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
        @if($isEdit)
        <div class="flex items-end">
            <label class="flex items-center gap-2 cursor-pointer font-tajawal text-sm text-gray-700 pb-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 w-4 h-4"
                       style="accent-color: {{ $themeColor }};"
                       @checked(old('is_active', $team->is_active))>
                <span>فريق نشط</span>
            </label>
        </div>
        @endif
        <div class="sm:col-span-2">
            <label class="{{ $label }}">وصف الفريق</label>
            <textarea name="description" rows="3" class="{{ $input }}" placeholder="اختصاص الفريق، المنطقة، أو أهدافه...">{{ old('description', $team->description ?? '') }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2" style="{{ $headerStyle }}">
        <span class="font-tajawal font-bold text-gray-900">أعضاء الفريق — مندوبو المبيعات</span>
        <span class="text-xs text-gray-500 font-tajawal">اختر مندوباً واحداً أو أكثر</span>
    </div>
    <div class="p-5 sm:p-6">
        @if($agents->isEmpty())
        <p class="text-sm text-gray-400 font-tajawal text-center py-6">لا يوجد مندوبو مبيعات مسجلون في النظام</p>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-64 overflow-y-auto">
            @foreach($agents as $agent)
            <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-100 hover:border-gray-200 cursor-pointer transition-colors has-[:checked]:border-current"
                   style="--tw-border-opacity: 1;">
                <input type="checkbox" name="member_ids[]" value="{{ $agent->id }}"
                       class="rounded border-gray-300 w-4 h-4 shrink-0"
                       style="accent-color: {{ $themeColor }};"
                       @checked(in_array($agent->id, $selectedMembers))>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-gray-900 font-tajawal truncate">{{ $agent->name }}</span>
                    @if($agent->email)
                    <span class="block text-[10px] text-gray-400 truncate" dir="ltr">{{ $agent->email }}</span>
                    @endif
                </span>
            </label>
            @endforeach
        </div>
        @endif
        @error('member_ids')<p class="mt-2 text-xs text-red-600 font-tajawal">{{ $message }}</p>@enderror
    </div>
</div>
