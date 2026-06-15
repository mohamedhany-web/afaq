@extends('layouts.app')
@section('page-title', 'موافقات الانصراف')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'موافقات الانصراف',
    'subtitle' => 'طلبات انصراف الموظفين — يجب اعتمادها قبل تسجيل الخروج',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>',
    'actionUrl' => route('operations.attendance-reviews.index'),
    'actionLabel' => 'مراجعة الغياب',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-3 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'بانتظار الموافقة', 'value' => $stats['pending'], 'accent' => 'amber', 'compact' => true, 'href' => route('operations.checkout-reviews.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => 'عرض المعلّقة'])
    @include('crm.partials.stat-card', ['label' => 'معتمد اليوم', 'value' => $stats['approved'], 'accent' => 'green', 'compact' => true, 'href' => route('operations.checkout-reviews.index', ['status' => 'approved']) . '#page-data', 'linkLabel' => 'عرض المعتمد'])
    @include('crm.partials.stat-card', ['label' => 'مرفوض اليوم', 'value' => $stats['rejected'], 'accent' => 'red', 'compact' => true, 'href' => route('operations.checkout-reviews.index', ['status' => 'rejected']) . '#page-data', 'linkLabel' => 'عرض المرفوض'])
</div>

<div class="bg-white rounded-2xl border p-5 mb-6 font-tajawal">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">التاريخ</label>
            <input type="date" name="date" value="{{ $date->toDateString() }}" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
            <select name="status" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
                <option value="">معلّقة</option>
                <option value="approved" @selected(request('status') === 'approved')>معتمدة</option>
                <option value="rejected" @selected(request('status') === 'rejected')>مرفوضة</option>
                <option value="revoked" @selected(request('status') === 'revoked')>ملغاة الاعتماد</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>ملغاة من الموظف</option>
                <option value="pending" @selected(request('status') === 'pending')>كل المعلّقة</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">عرض</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal" id="page-data">
    <div class="px-5 py-4 border-b font-bold">طلبات {{ $date->format('Y-m-d') }}</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-right">الموظف</th>
                    <th class="p-3 text-right">القسم</th>
                    <th class="p-3 text-right">الحضور</th>
                    <th class="p-3 text-right">طلب الانصراف</th>
                    <th class="p-3 text-right">الساعات</th>
                    <th class="p-3 text-right">الحالة</th>
                    <th class="p-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody>
            @forelse($reviews as $review)
            <tr class="border-t border-gray-100 align-top">
                <td class="p-3 font-semibold">{{ $review->employee?->first_name }} {{ $review->employee?->last_name }}</td>
                <td class="p-3 text-xs text-gray-600">{{ $review->employee?->department?->name ?? '—' }}</td>
                <td class="p-3 font-mono" dir="ltr">{{ $review->attendance?->check_in?->format('H:i') ?? '—' }}</td>
                <td class="p-3 font-mono font-bold text-red-600" dir="ltr">{{ $review->requested_check_out_at->format('H:i') }}</td>
                <td class="p-3">{{ $review->total_hours_preview }}h
                    @if($review->is_early_departure)<span class="block text-xs text-red-600">مبكر</span>@endif
                </td>
                <td class="p-3">@include('attendances.partials.status-badge', ['label' => $review->statusLabel(), 'color' => $review->isPending() ? 'amber' : (in_array($review->status, ['approved'], true) ? 'green' : 'red')])
                    @if($review->deduction_amount)
                    <span class="block text-xs text-red-600 mt-1">خصم: {{ number_format($review->deduction_amount, 2) }}</span>
                    @endif
                </td>
                <td class="p-3">
                    @if($review->isPending())
                    <div class="flex flex-col gap-2 min-w-[200px]">
                        <form method="POST" action="{{ route('operations.checkout-reviews.approve', $review) }}" class="flex gap-1">
                            @csrf
                            <input type="text" name="notes" placeholder="ملاحظة (اختياري)" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-green-600 text-white text-xs font-bold">اعتماد</button>
                        </form>
                        <form method="POST" action="{{ route('operations.checkout-reviews.reject', $review) }}" class="flex gap-1">
                            @csrf
                            <input type="text" name="notes" required placeholder="سبب الرفض / الخصم" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-bold">رفض</button>
                        </form>
                    </div>
                    @elseif($review->isApproved())
                    <form method="POST" action="{{ route('operations.checkout-reviews.revoke', $review) }}" class="flex gap-1 min-w-[200px]">
                        @csrf
                        <input type="text" name="notes" required placeholder="سبب إلغاء الاعتماد" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                        <button type="submit" class="px-3 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold whitespace-nowrap">إلغاء الاعتماد</button>
                    </form>
                    @elseif($review->review_notes || $review->deduction_reason)
                    <p class="text-xs text-gray-500">{{ $review->review_notes }}</p>
                    @if($review->deduction_reason)<p class="text-xs text-red-600 mt-1">{{ $review->deduction_reason }}</p>@endif
                    @else
                    —
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="p-10 text-center text-gray-500">لا توجد طلبات انصراف</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($reviews->hasPages())<div class="px-5 py-4 border-t">{{ $reviews->links() }}</div>@endif
</div>
@endsection
