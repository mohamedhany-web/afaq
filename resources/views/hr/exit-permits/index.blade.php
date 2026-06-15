@extends('layouts.app')

@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $titles = [
        'admin' => ['title' => 'إدارة الأذونات', 'subtitle' => 'مراجعة واعتماد أذونات الخروج والتأخر لجميع الموظفين'],
        'manager' => ['title' => 'أذونات الفريق', 'subtitle' => 'مراجعة أذونات فريقك وأذوناتك الشخصية'],
        'self' => ['title' => 'أذوناتي', 'subtitle' => 'تقديم طلب إذن ومتابعة حالة الطلبات'],
    ];
    $header = $titles[$mode] ?? $titles['self'];
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-800',
        'approved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
@endphp

@section('page-title', $header['title'])

@section('content')
@include('crm.partials.page-header', [
    'title' => $header['title'],
    'subtitle' => $header['subtitle'],
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>',
])

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'معلّقة', 'value' => $stats['pending'], 'accent' => 'amber', 'href' => route('hr.exit-permits.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => 'عرض المعلّقة'])
    @include('crm.partials.stat-card', ['label' => 'معتمدة (الشهر)', 'value' => $stats['approved_month'], 'accent' => 'green', 'href' => route('hr.exit-permits.index', ['status' => 'approved']) . '#page-data', 'linkLabel' => 'عرض المعتمدة'])
    @include('crm.partials.stat-card', ['label' => 'مرفوضة (الشهر)', 'value' => $stats['rejected_month'], 'accent' => 'red', 'href' => route('hr.exit-permits.index', ['status' => 'rejected']) . '#page-data', 'linkLabel' => 'عرض المرفوضة'])
</div>

<div id="page-data" class="bg-white rounded-2xl border shadow-lg overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-lg font-bold text-gray-900">طلبات الأذونات</h3>
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" class="flex flex-wrap gap-2">
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 border rounded-xl text-sm">
                    <option value="">جميع الحالات</option>
                    @foreach(config('exit_permits.status_labels', []) as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="permit_type" onchange="this.form.submit()" class="px-3 py-2 border rounded-xl text-sm">
                    <option value="">جميع الأنواع</option>
                    @foreach($permitTypes as $key => $label)
                    <option value="{{ $key }}" @selected(request('permit_type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            @if($scope->canRequest())
            <button type="button" onclick="document.getElementById('newPermitModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">طلب إذن</button>
            @endif
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    @if($mode !== 'self')
                    <th class="px-5 py-3 text-right">الموظف</th>
                    @endif
                    <th class="px-5 py-3 text-right">النوع</th>
                    <th class="px-5 py-3 text-right">التاريخ</th>
                    <th class="px-5 py-3 text-right">الوقت / المدة</th>
                    <th class="px-5 py-3 text-right">السبب</th>
                    <th class="px-5 py-3 text-right">الحالة</th>
                    @if($scope->canApprove())
                    <th class="px-5 py-3 text-right">إجراء</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($permits as $permit)
                <tr class="hover:bg-gray-50/80">
                    @if($mode !== 'self')
                    <td class="px-5 py-4">
                        <div class="font-semibold">{{ $permit->employee?->first_name }} {{ $permit->employee?->last_name }}</div>
                        <div class="text-xs text-gray-500">{{ $permit->employee?->department?->name }}</div>
                    </td>
                    @endif
                    <td class="px-5 py-4">{{ $permit->typeLabel() }}</td>
                    <td class="px-5 py-4">{{ $permit->permit_date->format('Y/m/d') }}</td>
                    <td class="px-5 py-4 text-gray-600">
                        @if($permit->start_time && $permit->end_time)
                            {{ \Carbon\Carbon::parse($permit->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($permit->end_time)->format('H:i') }}
                        @elseif($permit->duration_minutes)
                            {{ $permit->duration_minutes }} دقيقة
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-600 max-w-[12rem] truncate" title="{{ $permit->reason }}">{{ $permit->reason }}</td>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusColors[$permit->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $permit->statusLabel() }}
                        </span>
                    </td>
                    @if($scope->canApprove())
                    <td class="px-5 py-4">
                        @if($scope->canApprovePermit($permit))
                        <div class="flex flex-col gap-2 min-w-[180px]">
                            <form method="POST" action="{{ route('hr.exit-permits.approve', $permit) }}">
                                @csrf
                                <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-bold">اعتماد</button>
                            </form>
                            <form method="POST" action="{{ route('hr.exit-permits.reject', $permit) }}" class="flex gap-1">
                                @csrf
                                <input type="text" name="rejection_reason" placeholder="سبب الرفض" required class="flex-1 border rounded-lg px-2 py-1 text-xs">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-bold">رفض</button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ ($mode !== 'self' ? 6 : 5) + ($scope->canApprove() ? 1 : 0) }}" class="px-5 py-16 text-center text-gray-500">لا توجد طلبات أذونات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($permits->hasPages())<div class="px-5 py-4 border-t">{{ $permits->links() }}</div>@endif
</div>

@if($scope->canRequest())
<div id="newPermitModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('newPermitModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border overflow-hidden">
            <div class="px-5 py-4 border-b font-bold">طلب إذن جديد</div>
            <form method="POST" action="{{ route('hr.exit-permits.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">نوع الإذن</label>
                    <select name="permit_type" required class="w-full border rounded-xl px-3 py-2 text-sm">
                        @foreach($permitTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">التاريخ</label>
                    <input type="date" name="permit_date" required value="{{ now()->toDateString() }}" class="w-full border rounded-xl px-3 py-2 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">من (اختياري)</label>
                        <input type="time" name="start_time" class="w-full border rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">إلى (اختياري)</label>
                        <input type="time" name="end_time" class="w-full border rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">المدة بالدقائق (بديل)</label>
                    <input type="number" name="duration_minutes" min="15" max="480" placeholder="مثال: 60" class="w-full border rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">السبب</label>
                    <textarea name="reason" required rows="3" class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="اذكر سبب الإذن"></textarea>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('newPermitModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">إرسال الطلب</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
