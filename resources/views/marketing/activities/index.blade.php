@extends('layouts.app')
@section('page-title', 'جدول المهام التسويقية')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);"; @endphp

@include('crm.partials.page-header', [
    'title' => 'جدول المهام التسويقية',
    'subtitle' => 'مهام يومية / أسبوعية / شهرية — بين مدير التسويق والفريق',
    'actionUrl' => auth()->user()->can('create-marketing') ? route('marketing.activities.create', request()->only('marketing_plan_id')) : null,
    'actionLabel' => 'مهمة جديدة',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4">
    @include('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['today'], 'accent' => 'purple', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'accent' => 'amber', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'دورية', 'value' => $stats['recurring'], 'accent' => 'blue', 'compact' => true])
    @if($isManager)
    <a href="{{ route('marketing.plans.index') }}" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 hover:shadow-xl transition-all flex flex-col justify-center font-tajawal h-full min-h-[108px]">
        <span class="text-xs text-gray-500">خطط الشهر</span>
        <span class="text-sm font-bold mt-1" style="color:{{ $themeColor }}">خطة التسويق ←</span>
    </a>
    @else
    @include('crm.partials.stat-card', ['label' => 'مهامي', 'value' => $activities->total(), 'accent' => 'theme', 'compact' => true])
    @endif
</div>

<form method="GET" class="mb-4 bg-white rounded-2xl shadow-lg border border-gray-200 p-4 flex flex-wrap gap-2 items-end font-tajawal text-sm">
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">التاريخ</label>
        <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="border-2 border-gray-200 rounded-xl px-3 py-2">
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">العرض</label>
        <select name="view" class="border-2 border-gray-200 rounded-xl px-3 py-2">
            <option value="week" @selected($view === 'week')>أسبوع</option>
            <option value="day" @selected($view === 'day')>يوم</option>
            <option value="month" @selected($view === 'month')>شهر</option>
        </select>
    </div>
    @if(count($plans))
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">خطة الشهر</label>
        <select name="marketing_plan_id" class="border-2 border-gray-200 rounded-xl px-3 py-2 min-w-[160px]">
            <option value="">كل المهام</option>
            @foreach($plans as $p)
            <option value="{{ $p->id }}" @selected(request('marketing_plan_id') == $p->id)>{{ $p->title }}</option>
            @endforeach
        </select>
    </div>
    @endif
    @if(count($assignableUsers))
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">الموظف</label>
        <select name="assigned_to" class="border-2 border-gray-200 rounded-xl px-3 py-2">
            <option value="">كل الفريق</option>
            @foreach($assignableUsers as $u)
            <option value="{{ $u->id }}" @selected(request('assigned_to') == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold" style="background:{{ $themeColor }}">عرض</button>
</form>

@if($view === 'month' && !empty($monthCalendar))
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">تقويم {{ $date->locale('ar')->translatedFormat('F Y') }}</div>
    <div class="p-4 grid grid-cols-7 gap-2 font-tajawal">
        @for($d = 1; $d <= $date->daysInMonth; $d++)
        @php $tasks = $monthCalendar[$d] ?? collect(); @endphp
        <div class="min-h-[80px] rounded-xl border border-gray-100 p-2 bg-gray-50/40 {{ $date->day === $d ? 'ring-2' : '' }}" @if($date->day === $d) style="ring-color:{{ $themeColor }}" @endif>
            <div class="text-xs font-bold text-gray-600 mb-1">{{ $d }}</div>
            @foreach($tasks->take(2) as $t)
            <div class="text-[10px] truncate px-1 py-0.5 rounded bg-white border mb-0.5" title="{{ $t->title }}">{{ Str::limit($t->title, 10) }}</div>
            @endforeach
        </div>
        @endfor
    </div>
</div>
@endif

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">قائمة المهام</div>
    <div class="divide-y divide-gray-100 font-tajawal">
        @forelse($activities as $activity)
        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ $activity->isOverdue() ? 'bg-amber-50/50' : '' }}">
            <div>
                <p class="font-semibold text-gray-900">{{ $activity->title }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $activity->due_at?->locale('ar')->translatedFormat('d M Y — H:i') ?? '—' }}
                    · {{ $activity->typeLabel() }}
                    · {{ $activity->assignee?->name ?? '—' }}
                    @if($activity->plan) · <a href="{{ route('marketing.plans.show', $activity->plan) }}" class="font-bold" style="color:{{ $themeColor }}">{{ Str::limit($activity->plan->title, 20) }}</a> @endif
                    @if($activity->recurrence !== 'none') · <span class="text-purple-600">{{ $activity->recurrenceLabel() }}</span> @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs px-2 py-1 rounded-lg bg-gray-100">{{ $activity->statusLabel() }}</span>
                @if($activity->status !== 'completed')
                <form action="{{ route('marketing.activities.update-status', $activity) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg text-white" style="background:{{ $themeColor }}">إتمام</button>
                </form>
                @endif
                @can('edit-marketing')
                <a href="{{ route('marketing.activities.edit', $activity) }}" class="text-xs px-3 py-1.5 rounded-lg border">تعديل</a>
                @endcan
            </div>
        </div>
        @empty
        <p class="p-8 text-center text-gray-500">لا مهام في هذه الفترة.</p>
        @endforelse
    </div>
</div>
<div class="mt-4">{{ $activities->links() }}</div>
@endsection
