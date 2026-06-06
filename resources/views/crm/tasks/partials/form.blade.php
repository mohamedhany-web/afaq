@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $task = $task ?? null;
    $priorityColors = config('crm_tasks.priority_colors', []);
    $selectedPriority = old('priority', $task?->priority ?? 'medium');
@endphp

{{-- تفاصيل المهمة --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200" style="{{ $headerStyle }}">
        <h2 class="font-bold text-lg text-gray-900 font-tajawal">تفاصيل المهمة</h2>
        <p class="text-xs text-gray-500 mt-1">عنوان واضح ووصف يحدد المخرجات المتوقعة</p>
    </div>
    <div class="p-5 sm:p-6 space-y-4">
        <div>
            <label class="{{ $label }}">عنوان المهمة *</label>
            <input type="text" name="title" value="{{ old('title', $task?->title) }}" class="{{ $input }}" required maxlength="255"
                   placeholder="مثال: متابعة 20 عميلاً جديداً — اتصال أولي">
            @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">الوصف والمخرجات المتوقعة</label>
            <textarea name="description" rows="4" class="{{ $input }} resize-none"
                      placeholder="ما الذي يجب إنجازه؟ كيف يُقاس النجاح؟">{{ old('description', $task?->description) }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- التعيين والجدولة --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200" style="{{ $headerStyle }}">
        <h2 class="font-bold text-lg text-gray-900 font-tajawal">التعيين والجدولة</h2>
    </div>
    <div class="p-5 sm:p-6 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="{{ $label }}">تعيين إلى *</label>
                <select name="assigned_to" class="{{ $input }}" required>
                    @foreach($assignableUsers as $u)
                        <option value="{{ $u->id }}" @selected(old('assigned_to', $task?->assigned_to) == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
                @error('assigned_to')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="{{ $label }}">الأولوية *</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-1">
                    @foreach($priorities as $key => $text)
                    @php $pColor = $priorityColors[$key] ?? $themeColor; @endphp
                    <label class="relative cursor-pointer rounded-xl border-2 p-3 text-center transition-all"
                           :class="priority === '{{ $key }}' ? 'shadow-md' : 'border-gray-200 hover:border-gray-300'"
                           :style="priority === '{{ $key }}' ? 'border-color:{{ $pColor }}; background:{{ $pColor }}10' : ''">
                        <input type="radio" name="priority" value="{{ $key }}" class="sr-only" x-model="priority">
                        <span class="block w-3 h-3 rounded-full mx-auto mb-1.5" style="background:{{ $pColor }}"></span>
                        <span class="text-xs font-bold text-gray-800">{{ $text }}</span>
                    </label>
                    @endforeach
                </div>
                @error('priority')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="{{ $label }}">التصنيف *</label>
                <select name="category" class="{{ $input }}" required>
                    @foreach($categories as $key => $text)
                        <option value="{{ $key }}" @selected(old('category', $task?->category ?? 'follow_ups') === $key)>{{ $text }}</option>
                    @endforeach
                </select>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="{{ $label }}">موعد الاستحقاق *</label>
                <input type="datetime-local" name="due_at"
                       value="{{ old('due_at', $task?->due_at?->format('Y-m-d\TH:i') ?? now()->addDay()->setHour(17)->setMinute(0)->format('Y-m-d\TH:i')) }}"
                       class="{{ $input }}" required>
                @error('due_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            @if(!$task)
            <div class="sm:col-span-2 flex items-start gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                <input type="checkbox" name="requires_acceptance" value="1" id="requires_acceptance"
                       class="mt-1 rounded border-gray-300" @checked(old('requires_acceptance'))>
                <label for="requires_acceptance" class="text-sm text-gray-700 cursor-pointer">
                    <span class="font-bold block">يتطلب قبول الموظف</span>
                    <span class="text-xs text-gray-500">لن تبدأ المهمة حتى يضغط المكلف «قبول»</span>
                </label>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- الربط بالمبيعات --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200" style="{{ $headerStyle }}">
        <h2 class="font-bold text-lg text-gray-900 font-tajawal">الربط بالنشاط التجاري</h2>
        <p class="text-xs text-gray-500 mt-1">اختياري — يسهّل الوصول للعميل والصفقة من صفحة المهمة</p>
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="md:col-span-2 lg:col-span-1">
            <label class="{{ $label }}">العميل</label>
            @include('partials.client-search-select', [
                'name' => 'client_id',
                'value' => old('client_id', $task?->client_id),
                'crmScope' => true,
                'inputClass' => $input,
                'placeholder' => 'ابحث عن عميل...',
            ])
            @error('client_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="{{ $label }}">صفقة في المسار</label>
            <select name="sale_id" class="{{ $input }}">
                <option value="">— بدون —</option>
                @foreach($sales as $s)
                    <option value="{{ $s->id }}" @selected(old('sale_id', $task?->sale_id) == $s->id)>
                        {{ $s->client?->name ?? 'صفقة #' . $s->id }} — {{ $s->stage }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">مشروع عقاري</label>
            <select name="project_id" class="{{ $input }}">
                <option value="">— بدون —</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected(old('project_id', $task?->project_id) == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
