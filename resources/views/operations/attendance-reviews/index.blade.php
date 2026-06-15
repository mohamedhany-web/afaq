@extends('layouts.app')
@section('page-title', 'مراجعة الغياب')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'مراجعة الغياب اليومية',
    'subtitle' => 'جميع غيابات الموظفين تمر على مدير العمليات للتأكيد أو الاعتماد',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
    'actionUrl' => route('operations.attendance-reviews.flag'),
    'actionLabel' => 'تحديث القائمة',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'بانتظار المراجعة', 'value' => $stats['pending'], 'accent' => 'amber', 'href' => route('operations.attendance-reviews.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => 'عرض المعلّقة'])
    @include('crm.partials.stat-card', ['label' => 'غياب مؤكد', 'value' => $stats['confirmed_absent'], 'accent' => 'red', 'href' => route('operations.attendance-reviews.index', ['status' => 'confirmed_absent']) . '#page-data', 'linkLabel' => 'عرض الغياب'])
    @include('crm.partials.stat-card', ['label' => 'حضور مؤكد', 'value' => $stats['confirmed_present'], 'accent' => 'green', 'href' => route('operations.attendance-reviews.index', ['status' => 'confirmed_present']) . '#page-data', 'linkLabel' => 'عرض الحضور'])
    @include('crm.partials.stat-card', ['label' => 'معذور', 'value' => $stats['excused'], 'accent' => 'blue', 'href' => route('operations.attendance-reviews.index', ['status' => 'excused']) . '#page-data', 'linkLabel' => 'عرض المعذور'])
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6 font-tajawal">
    <div class="lg:col-span-2 bg-white rounded-2xl border p-5">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">تاريخ المراجعة</label>
                <input type="date" name="date" value="{{ $date->toDateString() }}" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
                <select name="status" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
                    <option value="">الكل</option>
                    <option value="pending" @selected(request('status') === 'pending')>بانتظار المراجعة</option>
                    <option value="confirmed_absent" @selected(request('status') === 'confirmed_absent')>غياب مؤكد</option>
                    <option value="confirmed_present" @selected(request('status') === 'confirmed_present')>حضور مؤكد</option>
                    <option value="excused" @selected(request('status') === 'excused')>معذور</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">عرض</button>
        </form>
    </div>
    <div class="bg-white rounded-2xl border p-5">
        <p class="font-bold text-gray-900 mb-3">الهرم الوظيفي</p>
        <ol class="space-y-2 text-sm">
            @foreach($hierarchy as $level)
            <li class="flex gap-2">
                <span class="font-bold text-gray-400">{{ $level['level'] }}.</span>
                <div>
                    <p class="font-semibold">{{ $level['label'] }}</p>
                    <p class="text-xs text-gray-500">{{ implode('، ', $level['roles']) }}</p>
                </div>
            </li>
            @endforeach
        </ol>
        <p class="text-xs text-gray-500 mt-3">مدير العمليات يراجع غياب <strong>جميع الموظفين</strong> يومياً قبل احتساب الخصومات.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal" id="page-data">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-right">الموظف</th>
                    <th class="p-3 text-right">القسم</th>
                    <th class="p-3 text-right">المدير المباشر</th>
                    <th class="p-3 text-right">السبب</th>
                    <th class="p-3 text-right">الحالة</th>
                    <th class="p-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody>
            @forelse($reviews as $review)
            <tr class="border-t border-gray-100 align-top">
                <td class="p-3">
                    <p class="font-semibold">{{ $review->employee?->first_name }} {{ $review->employee?->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ $review->employee?->position }}</p>
                </td>
                <td class="p-3">{{ $review->employee?->department?->name ?? '—' }}</td>
                <td class="p-3">{{ $review->lineManager?->name ?? '—' }}</td>
                <td class="p-3">
                    {{ $review->reasonLabel() }}
                    @if($review->has_approved_leave)<span class="block text-xs text-blue-600">إجازة معتمدة</span>@endif
                </td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded-full text-xs font-bold
                        @if($review->status === 'pending') bg-amber-100 text-amber-800
                        @elseif($review->status === 'confirmed_present') bg-green-100 text-green-800
                        @elseif($review->status === 'excused') bg-blue-100 text-blue-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ $review->statusLabel() }}
                    </span>
                    @if($review->review_notes)<p class="text-xs text-gray-500 mt-1">{{ Str::limit($review->review_notes, 60) }}</p>@endif
                </td>
                <td class="p-3">
                    @if($review->isPending())
                    <div class="flex flex-col gap-2 min-w-[200px]">
                        <form method="POST" action="{{ route('operations.attendance-reviews.confirm-present', $review) }}" class="flex gap-1">
                            @csrf
                            <input type="text" name="notes" placeholder="ملاحظة (اختياري)" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-green-600 text-white text-xs font-bold whitespace-nowrap">حضور فعلي</button>
                        </form>
                        <form method="POST" action="{{ route('operations.attendance-reviews.confirm-absent', $review) }}" class="flex gap-1">
                            @csrf
                            <input type="text" name="notes" placeholder="ملاحظة" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-red-600 text-white text-xs font-bold whitespace-nowrap">تأكيد غياب</button>
                        </form>
                        <form method="POST" action="{{ route('operations.attendance-reviews.excuse', $review) }}" class="flex gap-1">
                            @csrf
                            <input type="text" name="notes" placeholder="سبب العذر *" required class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg border text-xs font-bold whitespace-nowrap">معذور</button>
                        </form>
                    </div>
                    @elseif(in_array($review->status, ['confirmed_absent', 'auto_confirmed']))
                    <form method="POST" action="{{ route('operations.attendance-reviews.revoke', $review) }}" class="flex gap-1 min-w-[200px]">
                        @csrf
                        <input type="text" name="notes" required placeholder="سبب إلغاء قرار الغياب" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                        <button type="submit" class="px-3 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold whitespace-nowrap">إلغاء القرار</button>
                    </form>
                    @else
                    <span class="text-xs text-gray-500">{{ $review->reviewer?->name ?? '—' }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="p-8 text-center text-gray-500">لا توجد سجلات غياب لهذا التاريخ.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($reviews->hasPages())<div class="p-4">{{ $reviews->links() }}</div>@endif
</div>
@endsection
