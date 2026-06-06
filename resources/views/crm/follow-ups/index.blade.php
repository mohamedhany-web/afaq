@extends('layouts.app')
@section('page-title', 'جدول المتابعات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $prevDate = $date->copy()->subDay()->toDateString();
    $nextDate = $date->copy()->addDay()->toDateString();
@endphp

@include('crm.partials.page-header', [
    'title' => 'جدول المتابعات',
    'subtitle' => 'مواعيد المكالمات والمعاينات والاجتماعات — مع تذكير تلقائي',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
])

@if($errors->any())
<div class="mb-4 rounded-2xl border-2 border-red-200 bg-red-50 px-4 py-3 font-tajawal text-sm text-red-800">
    <p class="font-bold mb-1">تعذر حفظ المتابعة:</p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-3 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'مواعيد اليوم', 'value' => $stats['today'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />'])
    @include('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'accent' => 'red', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" />'])
    @include('crm.partials.stat-card', ['label' => 'الأسبوع القادم', 'value' => $stats['upcoming'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'])
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- نموذج تسجيل --}}
    <div class="xl:col-span-1">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden sticky top-4">
            <div class="px-5 py-4 border-b font-tajawal font-bold text-gray-900"
                 style="background: linear-gradient(135deg, {{ $themeColor }}08, transparent);">
                تسجيل متابعة جديدة
            </div>
            <form action="{{ route('crm.follow-ups.store') }}" method="POST" class="p-5 space-y-3 font-tajawal" id="follow-up-create-form">
                @csrf
                @include('partials.client-search-select', [
                    'required' => true,
                    'value' => old('client_id'),
                    'inputClass' => 'w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal',
                    'crmScope' => true,
                ])
                @if($canAssignOthers)
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الموظف المسؤول</label>
                    <select name="user_id" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                        @foreach($assignableUsers as $u)
                        <option value="{{ $u->id }}" @selected($u->id === auth()->id())>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">نوع النشاط *</label>
                    <select name="interaction_type" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                        @foreach($typeLabels as $key => $label)
                        <option value="{{ $key }}" @selected(old('interaction_type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">التاريخ *</label>
                        <input type="date" name="scheduled_at" value="{{ old('scheduled_at', now()->toDateString()) }}" required
                               class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">الوقت *</label>
                        <input type="time" name="scheduled_time" value="{{ old('scheduled_time', now()->format('H:i')) }}" required
                               class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">التفاصيل *</label>
                    <textarea name="notes" rows="3" required placeholder="ملاحظات المتابعة..."
                              class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm resize-none">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-bold text-white"
                        style="background: linear-gradient(135deg, {{ $themeColor }}, {{ $themeColor }}dd);">
                    حفظ في الجدول
                </button>
            </form>
        </div>
    </div>

    {{-- الجدول --}}
    <div class="xl:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4">
            <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-end">
                <div class="flex items-center gap-2">
                    <a href="{{ route('crm.follow-ups.index', array_merge(request()->except('date'), ['date' => $prevDate])) }}"
                       class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50">‹</a>
                    <input type="date" name="date" value="{{ $date->toDateString() }}"
                           class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                    <a href="{{ route('crm.follow-ups.index', array_merge(request()->except('date'), ['date' => $nextDate])) }}"
                       class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50">›</a>
                </div>
                <select name="status" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                    <option value="">كل الحالات</option>
                    <option value="scheduled" @selected(request('status') === 'scheduled')>مجدولة</option>
                    <option value="completed" @selected(request('status') === 'completed')>مكتملة</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغاة</option>
                </select>
                @if($isManager)
                <select name="user_id" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                    <option value="">كل الموظفين</option>
                    @foreach($assignableUsers as $u)
                    <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..."
                       class="flex-1 min-w-[120px] border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
                        style="background: {{ $themeColor }};">عرض</button>
            </form>
            <p class="text-xs text-gray-500 mt-2 font-tajawal">
                عرض يوم: {{ $date->translatedFormat('l j F Y') }}
                @if(!$date->isToday())
                    — <a href="{{ route('crm.follow-ups.index', array_merge(request()->except('date'), ['date' => now()->toDateString()])) }}" class="font-bold underline" style="color: {{ $themeColor }};">العودة ليوم اليوم</a>
                @endif
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm font-tajawal">
                    <thead class="bg-gray-50 border-b">
                        <tr class="text-gray-600 text-xs">
                            <th class="text-right p-3 font-bold">الوقت</th>
                            <th class="text-right p-3 font-bold">النشاط</th>
                            <th class="text-right p-3 font-bold">العميل</th>
                            <th class="text-right p-3 font-bold">الموظف</th>
                            <th class="text-right p-3 font-bold">أضافه</th>
                            <th class="text-right p-3 font-bold">الحالة</th>
                            <th class="text-right p-3 font-bold"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($followUps as $item)
                        @php
                            $rowClass = $highlight === $item->id ? 'bg-amber-50' : '';
                            if ($item->isOverdue()) $rowClass = 'bg-red-50';
                        @endphp
                        <tr class="border-t border-gray-100 hover:bg-gray-50/80 {{ $rowClass }}">
                            <td class="p-3 whitespace-nowrap font-bold tabular-nums" dir="ltr">
                                {{ $item->scheduled_at->format('H:i') }}
                                <div class="text-[10px] text-gray-400 font-normal">{{ $item->scheduled_at->format('Y/m/d') }}</div>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-gray-100">{{ $item->typeLabel() }}</span>
                            </td>
                            <td class="p-3">
                                <a href="{{ route('crm.pipeline.client', $item->client) }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $item->client->name }}</a>
                                <div class="text-xs text-gray-500" dir="ltr">{{ $item->client->phone }}</div>
                            </td>
                            <td class="p-3 text-gray-800">{{ $item->user->name }}</td>
                            <td class="p-3 text-gray-600 text-xs">{{ $item->creator->name }}</td>
                            <td class="p-3">
                                @if($item->status === 'completed')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">مكتمل</span>
                                @elseif($item->status === 'cancelled')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 font-semibold">ملغى</span>
                                @elseif($item->isOverdue())
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">متأخر</span>
                                @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">مجدول</span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($item->status === 'scheduled')
                                <div class="flex gap-1">
                                    <form action="{{ route('crm.follow-ups.complete', $item) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs px-2 py-1 rounded-lg bg-green-50 text-green-700 font-semibold hover:bg-green-100">تم</button>
                                    </form>
                                    <form action="{{ route('crm.follow-ups.cancel', $item) }}" method="POST" onsubmit="return confirm('إلغاء الموعد؟')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs px-2 py-1 rounded-lg bg-gray-100 text-gray-600 font-semibold">إلغاء</button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-t-0 {{ $rowClass }}">
                            <td colspan="7" class="px-3 pb-3 pt-0 text-xs text-gray-600">{{ Str::limit($item->notes, 120) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-gray-400 font-tajawal">
                                لا توجد متابعات في هذا اليوم
                                @if(!$date->isToday())
                                <div class="mt-2">
                                    <a href="{{ route('crm.follow-ups.index', ['date' => now()->toDateString()]) }}" class="text-sm font-semibold underline" style="color: {{ $themeColor }};">عرض متابعات اليوم</a>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($followUps->hasPages())
            <div class="p-4 border-t">{{ $followUps->links() }}</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('follow-up-create-form')?.addEventListener('submit', function (e) {
    const root = this.querySelector('.client-search-select');
    if (!root || typeof Alpine === 'undefined') return;
    const el = root.querySelector('[x-data]');
    const data = el ? Alpine.$data(el) : null;
    const hidden = root.querySelector('input[type="hidden"][name="client_id"]');
    if (data && hidden) {
        hidden.value = data.selectedId || '';
    }
    if (!hidden?.value) {
        e.preventDefault();
        alert('اختر العميل من نتائج البحث قبل الحفظ.');
    }
});
</script>
@endpush
@endsection
