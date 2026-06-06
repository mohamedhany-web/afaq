@extends('layouts.app')

@section('page-title', 'الإشعارات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $buildQuery = fn (string $f, array $extra = []) => array_filter(array_merge(
        ['filter' => $f],
        $search ? ['search' => $search] : [],
        ($extra['archive'] ?? false) ? ['archive' => 1] : ($archive ? ['archive' => 1] : []),
        $extra
    ));
    $filterPill = fn (string $key) => $filter === $key
        ? 'text-white shadow-md font-bold'
        : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border-2 border-gray-100';
    $filterStyle = fn (string $key) => $filter === $key
        ? "background: linear-gradient(135deg, {$themeColor} 0%, {$themeColor}dd 100%);"
        : '';
    $typeMeta = function (string $type) use ($themeColor) {
        return match ($type) {
            'task', 'crm_task' => ['accent' => '#16a34a', 'label' => 'مهمة'],
            'project' => ['accent' => '#2563eb', 'label' => 'مشروع'],
            'message' => ['accent' => '#9333ea', 'label' => 'رسالة'],
            'crm_follow_up', 'crm_reminder' => ['accent' => '#d97706', 'label' => 'متابعة'],
            'crm_daily_report' => ['accent' => $themeColor, 'label' => 'تقرير'],
            default => ['accent' => '#6b7280', 'label' => 'عام'],
        };
    };
    $maxDays = config('notifications.list_max_days', 90);
@endphp

@include('crm.partials.page-header', [
    'title' => 'صندوق الإشعارات',
    'subtitle' => 'عرض ما يحتاج انتباهك — وليس آلاف السجلات دفعة واحدة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
])

<div class="w-full space-y-6">
    @if($highVolume ?? false)
    <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 px-4 py-3 text-sm font-tajawal text-amber-900">
        حجم كبير من الإشعارات ({{ number_format($totalCount) }}). ننصح بفتح تبويب <strong>غير مقروءة</strong> أو <strong>اليوم</strong>، وتفعيل التنظيف التلقائي للمقروءة.
    </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        @include('crm.partials.stat-card', ['compact' => true, 'label' => 'غير مقروءة', 'value' => $unreadCount, 'accent' => 'red', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'])
        @include('crm.partials.stat-card', ['compact' => true, 'label' => 'اليوم', 'value' => $todayCount, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'])
        @include('crm.partials.stat-card', ['compact' => true, 'label' => 'آخر 7 أيام', 'value' => $weekCount ?? 0, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'])
        @include('crm.partials.stat-card', ['compact' => true, 'label' => 'CRM', 'value' => $crmCount, 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'])
        @include('crm.partials.stat-card', ['compact' => true, 'label' => 'الإجمالي', 'value' => number_format($totalCount), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>'])
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                <span class="text-xs font-bold text-gray-400 font-tajawal w-full mb-0.5">العرض الرئيسي</span>
                <a href="{{ route('notifications.index', $buildQuery('unread')) }}"
                   class="px-3 py-2 rounded-xl text-xs sm:text-sm font-tajawal transition {{ $filterPill('unread') }}"
                   @if($filter === 'unread') style="{{ $filterStyle('unread') }}" @endif>يحتاج انتباه ({{ $unreadCount }})</a>
                <a href="{{ route('notifications.index', $buildQuery('today')) }}"
                   class="px-3 py-2 rounded-xl text-xs sm:text-sm font-tajawal transition {{ $filterPill('today') }}"
                   @if($filter === 'today') style="{{ $filterStyle('today') }}" @endif>اليوم ({{ $todayCount }})</a>
                <a href="{{ route('notifications.index', $buildQuery('week')) }}"
                   class="px-3 py-2 rounded-xl text-xs sm:text-sm font-tajawal transition {{ $filterPill('week') }}"
                   @if($filter === 'week') style="{{ $filterStyle('week') }}" @endif>آخر 7 أيام ({{ $weekCount ?? 0 }})</a>
                <a href="{{ route('notifications.index', $buildQuery('read')) }}"
                   class="px-3 py-2 rounded-xl text-xs sm:text-sm font-tajawal transition {{ $filterPill('read') }}"
                   @if($filter === 'read') style="{{ $filterStyle('read') }}" @endif>مقروءة ({{ $readCount ?? 0 }})</a>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" id="markAllReadForm" class="inline">
                    @csrf
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                    @if($archive)<input type="hidden" name="archive" value="1">@endif
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-xs font-bold font-tajawal shadow-sm"
                            style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                        تحديد المعروض كمقروء
                    </button>
                </form>
                @endif
                @if(($readCount ?? 0) > 0)
                <form action="{{ route('notifications.clear-read') }}" method="POST" class="inline"
                      onsubmit="return confirm('حذف الإشعارات المقروءة الأقدم من {{ config('notifications.prune_read_after_days', 30) }} يوماً؟')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl border-2 border-gray-200 text-gray-600 text-xs font-bold font-tajawal hover:bg-gray-50">
                        تنظيف المقروءة القديمة
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
            <span class="text-xs font-bold text-gray-400 font-tajawal w-full mb-0.5">تصفية حسب النوع</span>
            <a href="{{ route('notifications.index', $buildQuery('crm')) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal {{ $filter === 'crm' ? 'text-white font-bold' : 'bg-gray-50 text-gray-600' }}" @if($filter === 'crm') style="{{ $filterStyle('crm') }}" @endif>CRM ({{ $crmCount }})</a>
            <a href="{{ route('notifications.index', $buildQuery('message')) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal {{ $filter === 'message' ? 'text-white font-bold' : 'bg-gray-50 text-gray-600' }}" @if($filter === 'message') style="{{ $filterStyle('message') }}" @endif>رسائل ({{ $messageCount }})</a>
            <a href="{{ route('notifications.index', $buildQuery('project')) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal {{ $filter === 'project' ? 'text-white font-bold' : 'bg-gray-50 text-gray-600' }}" @if($filter === 'project') style="{{ $filterStyle('project') }}" @endif>مشاريع ({{ $projectCount }})</a>
            <a href="{{ route('notifications.index', $buildQuery('all')) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal {{ $filter === 'all' && !$archive ? 'text-white font-bold' : 'bg-gray-50 text-gray-600' }}" @if($filter === 'all' && !$archive) style="{{ $filterStyle('all') }}" @endif>آخر {{ $maxDays }} يوم</a>
            @if($filter === 'all' || $archive)
            <a href="{{ route('notifications.index', ['filter' => 'all', 'archive' => 1, 'search' => $search]) }}"
               class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal {{ $archive ? 'text-white font-bold' : 'bg-gray-50 text-gray-600' }}"
               @if($archive) style="{{ $filterStyle('all') }}" @endif>الأرشيف الكامل</a>
            @endif
        </div>

        @if($listCapped ?? false)
        <p class="text-xs text-gray-500 font-tajawal">عرض «الكل» محدود بآخر {{ $maxDays }} يوماً. للأقدم استخدم <a href="{{ route('notifications.index', ['filter' => 'all', 'archive' => 1]) }}" class="font-bold underline" style="color: {{ $themeColor }};">الأرشيف</a>.</p>
        @endif

        <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <input type="hidden" name="filter" value="{{ $filter }}">
            @if($archive)<input type="hidden" name="archive" value="1">@endif
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="بحث سريع في العنوان أو النص..."
                       class="w-full border-2 border-gray-200 rounded-xl pl-11 pr-4 py-2.5 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal shrink-0"
                    style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">بحث</button>
            @if($search)
            <a href="{{ route('notifications.index', array_filter(['filter' => $filter, 'archive' => $archive ? 1 : null])) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold font-tajawal text-center shrink-0">مسح</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        @if($notifications->count() > 0)
            @foreach($grouped as $group)
            <div class="border-b border-gray-100 last:border-b-0">
                <div class="px-4 sm:px-5 py-2.5 flex items-center justify-between sticky top-0 z-10 border-b border-gray-100"
                     style="background: linear-gradient(135deg, {{ $themeColor }}10 0%, #fff 100%);">
                    <h3 class="text-sm font-bold text-gray-800 font-tajawal">{{ $group['label'] }}</h3>
                    <span class="text-xs text-gray-400 font-tajawal tabular-nums">{{ $group['items']->count() }} إشعار</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($group['items'] as $notification)
                        @include('notifications.partials.inbox-item', compact('notification', 'themeColor', 'typeMeta'))
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="px-4 sm:px-6 py-4 border-t border-gray-100 overflow-x-auto font-tajawal text-sm text-gray-500">
                عرض {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} من {{ number_format($notifications->total()) }}
                — {{ $notifications->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-16 px-6">
                <h3 class="text-lg font-bold text-gray-900 font-tajawal">لا توجد إشعارات في هذا العرض</h3>
                <p class="mt-2 text-sm text-gray-500 font-tajawal">
                    @if($filter === 'unread')
                        لا يوجد ما يحتاج انتباهك الآن.
                    @else
                        جرّب تبويب «غير مقروءة» أو «اليوم».
                    @endif
                </p>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                   class="inline-flex mt-5 px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                   style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">غير مقروءة</a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); });
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('markAllReadForm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const orig = btn.innerHTML;
        btn.disabled = true;
        const body = new FormData(this);
        fetch('{{ route('notifications.mark-all-read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: body,
        })
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); else { btn.disabled = false; btn.innerHTML = orig; } })
        .catch(() => { btn.disabled = false; btn.innerHTML = orig; });
    });
});
</script>
@endpush
@endsection
