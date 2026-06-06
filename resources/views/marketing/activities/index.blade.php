@extends('layouts.app')
@section('page-title', 'الجدول الدوري')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'جدول المهام التسويقية',
    'subtitle' => 'مهام يومية / أسبوعية / شهرية — دورية',
    'actionUrl' => auth()->user()->can('create-marketing') ? route('marketing.activities.create') : null,
    'actionLabel' => 'مهمة جديدة',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-3 gap-3 mb-4">
    @include('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['today'], 'accent' => 'purple'])
    @include('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'accent' => 'amber'])
    @include('crm.partials.stat-card', ['label' => 'دورية', 'value' => $stats['recurring'], 'accent' => 'blue'])
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2 items-center font-tajawal text-sm">
    <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="border-2 border-gray-200 rounded-xl px-3 py-2">
    <select name="view" class="border-2 border-gray-200 rounded-xl px-3 py-2">
        <option value="week" @selected($view === 'week')>أسبوع</option>
        <option value="day" @selected($view === 'day')>يوم</option>
    </select>
    @if(count($assignableUsers))
    <select name="assigned_to" class="border-2 border-gray-200 rounded-xl px-3 py-2">
        <option value="">كل الفريق</option>
        @foreach($assignableUsers as $u)
        <option value="{{ $u->id }}" @selected(request('assigned_to') == $u->id)>{{ $u->name }}</option>
        @endforeach
    </select>
    @endif
    <button type="submit" class="px-4 py-2 rounded-xl text-white" style="background:{{ $themeColor }}">عرض</button>
</form>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
    <div class="divide-y divide-gray-100 font-tajawal">
        @forelse($activities as $activity)
        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ $activity->isOverdue() ? 'bg-amber-50/50' : '' }}">
            <div>
                <p class="font-semibold text-gray-900">{{ $activity->title }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $activity->due_at?->locale('ar')->translatedFormat('d M Y — H:i') ?? '—' }}
                    · {{ $activity->typeLabel() }}
                    · {{ $activity->assignee?->name ?? '—' }}
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
