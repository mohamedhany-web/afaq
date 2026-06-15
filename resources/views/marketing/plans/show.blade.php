@extends('layouts.app')
@section('page-title', $plan->title)

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $input = 'w-full border-2 border-gray-200 rounded-xl px-3 py-2 font-tajawal text-sm';
    $daysInMonth = \Carbon\Carbon::create($plan->year, $plan->month, 1)->daysInMonth;
    $firstDow = \Carbon\Carbon::create($plan->year, $plan->month, 1)->dayOfWeek; // 0=Sun
@endphp

@include('crm.partials.page-header', [
    'title' => $plan->title,
    'subtitle' => $plan->periodLabel() . ' — ' . $plan->statusLabel(),
    'actionUrl' => route('marketing.plans.index'),
    'actionLabel' => 'كل الخطط',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي المهام', 'value' => $stats['total'], 'accent' => 'theme', 'compact' => true, 'href' => '#plan-tasks', 'linkLabel' => 'عرض المهام'])
    @include('crm.partials.stat-card', ['label' => 'مكتملة', 'value' => $stats['completed'], 'accent' => 'green', 'compact' => true, 'href' => '#plan-tasks', 'linkLabel' => 'عرض المهام'])
    @include('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'accent' => 'amber', 'compact' => true, 'href' => '#plan-tasks', 'linkLabel' => 'عرض المهام'])
    @include('crm.partials.stat-card', ['label' => 'التقدم', 'value' => $plan->progressPercent() . '%', 'accent' => 'purple', 'compact' => true, 'href' => '#plan-tasks', 'linkLabel' => 'عرض المهام'])
</div>

@if($isManager)
<div class="flex flex-wrap gap-2 mb-6 font-tajawal">
    @if($plan->status !== 'active')
    <form action="{{ route('marketing.plans.activate', $plan) }}" method="POST">@csrf
        <button type="submit" class="px-4 py-2 rounded-xl bg-green-600 text-white text-sm font-semibold">تفعيل الخطة</button>
    </form>
    @endif
    <a href="{{ route('marketing.plans.edit', $plan) }}" class="px-4 py-2 rounded-xl border text-sm font-semibold">تعديل التوصيف</a>
    <a href="{{ route('marketing.activities.index', ['view' => 'month', 'date' => $plan->year.'-'.str_pad($plan->month,2,'0',STR_PAD_LEFT).'-01', 'marketing_plan_id' => $plan->id]) }}" class="px-4 py-2 rounded-xl text-white text-sm font-semibold" style="background:{{ $themeColor }}">عرض جدول الشهر</a>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">توصيف الخطة</div>
        <div class="p-5 sm:p-6 space-y-4 text-sm font-tajawal text-gray-700 leading-relaxed">
            <div>
                <h3 class="text-xs font-bold text-gray-500 mb-2 uppercase">الوصف</h3>
                <p class="whitespace-pre-wrap">{{ $plan->description ?: '—' }}</p>
            </div>
            @if($plan->objectives)
            <div>
                <h3 class="text-xs font-bold text-gray-500 mb-2 uppercase">الأهداف</h3>
                <p class="whitespace-pre-wrap">{{ $plan->objectives }}</p>
            </div>
            @endif
            @if($plan->campaign)
            <p class="text-xs text-gray-500">الحملة: <a href="{{ route('marketing.campaigns.show', $plan->campaign) }}" class="font-bold" style="color:{{ $themeColor }}">{{ $plan->campaign->name }}</a></p>
            @endif
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">ملخص الفريق</div>
        <div class="p-5 space-y-2 text-sm font-tajawal">
            <p><span class="text-gray-500">مدير الخطة:</span> <strong>{{ $plan->manager?->name ?? '—' }}</strong></p>
            @php $byUser = $plan->activities->groupBy('assigned_to'); @endphp
            @foreach($byUser as $uid => $tasks)
            <div class="flex justify-between py-2 border-b border-gray-50">
                <span>{{ $tasks->first()->assignee?->name ?? 'غير معيّن' }}</span>
                <span class="font-bold" style="color:{{ $themeColor }}">{{ $tasks->count() }} مهمة</span>
            </div>
            @endforeach
            @if($byUser->isEmpty())<p class="text-gray-400 text-center py-4">لم تُوزَّع مهام بعد</p>@endif
        </div>
    </div>
</div>

{{-- تقويم الشهر --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b font-bold font-tajawal flex justify-between items-center" style="{{ $headerStyle }}">
        <span>توزيع المهام على الشهر</span>
        <span class="text-xs text-gray-500 font-normal">{{ $plan->periodLabel() }}</span>
    </div>
    <div class="p-4 sm:p-5">
        <div class="grid grid-cols-7 gap-1 sm:gap-2 text-center text-xs font-bold text-gray-500 mb-2 font-tajawal">
            @foreach(['أحد','إثن','ثلا','أرب','خمي','جمع','سبت'] as $dn)<div>{{ $dn }}</div>@endforeach
        </div>
        <div class="grid grid-cols-7 gap-1 sm:gap-2 font-tajawal">
            @for($i = 0; $i < $firstDow; $i++)<div class="min-h-[72px]"></div>@endfor
            @for($day = 1; $day <= $daysInMonth; $day++)
            @php $dayTasks = $calendar[$day] ?? collect(); $isToday = now()->year == $plan->year && now()->month == $plan->month && now()->day == $day; @endphp
            <div class="min-h-[72px] sm:min-h-[88px] rounded-xl border p-1.5 text-right {{ $isToday ? 'border-2' : 'border-gray-100 bg-gray-50/50' }}" @if($isToday) style="border-color:{{ $themeColor }}" @endif>
                <div class="text-[10px] sm:text-xs font-bold text-gray-600 mb-1">{{ $day }}</div>
                @foreach($dayTasks->take(3) as $t)
                <div class="text-[9px] sm:text-[10px] truncate px-1 py-0.5 rounded mb-0.5 {{ $t->status === 'completed' ? 'bg-green-100 text-green-800' : ($t->isOverdue() ? 'bg-amber-100 text-amber-900' : 'bg-white text-gray-700 border border-gray-100') }}" title="{{ $t->title }}">
                    {{ Str::limit($t->title, 12) }}
                </div>
                @endforeach
                @if($dayTasks->count() > 3)<div class="text-[9px] text-gray-400">+{{ $dayTasks->count() - 3 }}</div>@endif
            </div>
            @endfor
        </div>
    </div>
</div>

@if($isManager)
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- إضافة مهام يدوياً --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" x-data="{ rows: [{ title: '', assigned_to: '', due_day: 1, type: 'content', priority: 'medium' }] }">
        <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">إضافة مهام للخطة</div>
        <form action="{{ route('marketing.plans.tasks.store', $plan) }}" method="POST" class="p-5 space-y-3">
            @csrf
            <template x-for="(row, idx) in rows" :key="idx">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <div class="sm:col-span-5">
                        <input type="text" :name="'tasks['+idx+'][title]'" x-model="row.title" required class="{{ $input }}" placeholder="عنوان المهمة">
                    </div>
                    <div class="sm:col-span-3">
                        <select :name="'tasks['+idx+'][assigned_to]'" x-model="row.assigned_to" required class="{{ $input }}">
                            <option value="">الموظف</option>
                            @foreach($assignableUsers as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <input type="number" :name="'tasks['+idx+'][due_day]'" x-model="row.due_day" min="1" max="31" required class="{{ $input }}" placeholder="يوم">
                    </div>
                    <div class="sm:col-span-2 flex gap-1">
                        <input type="hidden" :name="'tasks['+idx+'][type]'" value="content">
                        <input type="hidden" :name="'tasks['+idx+'][priority]'" value="medium">
                        <button type="button" @click="rows.splice(idx,1)" x-show="rows.length > 1" class="px-2 text-red-500 text-xs">حذف</button>
                    </div>
                </div>
            </template>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="rows.push({ title:'', assigned_to:'', due_day:1, type:'content', priority:'medium' })" class="px-3 py-1.5 rounded-lg border text-xs font-bold">+ مهمة</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-white text-sm font-semibold mr-auto" style="background:{{ $themeColor }}">حفظ المهام</button>
            </div>
        </form>
    </div>

    {{-- توزيع تلقائي --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">توزيع تلقائي على الشهر</div>
        <form action="{{ route('marketing.plans.distribute', $plan) }}" method="POST" class="p-5 space-y-4 font-tajawal text-sm">
            @csrf
            <p class="text-gray-600 text-xs">اكتب كل مهمة في سطر — يُوزَّعها النظام على أيام الشهر بين الموظفين المحددين.</p>
            <textarea name="task_lines" rows="6" required class="{{ $input }}" placeholder="منشور فيسبوك أسبوعي&#10;حملة بريد إلكتروني&#10;تقرير أداء أسبوعي"></textarea>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-2">الموظفون</p>
                <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto">
                    @foreach($assignableUsers as $u)
                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="employee_ids[]" value="{{ $u->id }}" class="rounded">
                        <span>{{ $u->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 rounded-xl text-white font-semibold" style="background:linear-gradient(135deg,{{ $themeColor }} 0%,{{ $themeColor }}dd 100%)">توزيع على الشهر</button>
        </form>
    </div>
</div>
@endif

{{-- قائمة المهام --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">كل مهام الخطة</div>
    <div class="divide-y divide-gray-100 font-tajawal">
        @forelse($plan->activities->sortBy('due_at') as $activity)
        <div class="px-5 py-3 flex flex-wrap items-center justify-between gap-2 {{ $activity->isOverdue() ? 'bg-amber-50/40' : '' }}">
            <div>
                <p class="font-semibold text-gray-900 text-sm">{{ $activity->title }}</p>
                <p class="text-xs text-gray-500">{{ $activity->due_at?->format('Y/m/d') }} · {{ $activity->assignee?->name }} · {{ $activity->statusLabel() }}</p>
            </div>
            <div class="flex gap-2">
                @if($activity->status !== 'completed')
                <form action="{{ route('marketing.activities.update-status', $activity) }}" method="POST">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button class="text-xs px-3 py-1 rounded-lg text-white" style="background:{{ $themeColor }}">إتمام</button>
                </form>
                @endif
                @can('edit-marketing')
                <a href="{{ route('marketing.activities.edit', $activity) }}" class="text-xs px-3 py-1 rounded-lg border">تعديل</a>
                @endcan
            </div>
        </div>
        @empty
        <p class="p-8 text-center text-gray-500 text-sm">لا مهام في هذه الخطة بعد.</p>
        @endforelse
    </div>
</div>
@endsection
