@extends('layouts.app')
@section('page-title', 'الحضور والانصراف')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $dateValue = $selectedDate->toDateString();
@endphp

@include('crm.partials.page-header', [
    'title' => 'الحضور والانصراف',
    'subtitle' => $isToday
        ? 'متابعة يومية كاملة — ' . $selectedDate->translatedFormat('l j F Y')
        : 'سجل يوم ' . $selectedDate->translatedFormat('l j F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
])

{{-- أزرار تسجيل الحضور — للموظفين فقط (ليس للإدمن) --}}
@if(($canClockIn ?? false) && $isToday)
<div class="mb-6 flex flex-wrap items-center justify-between gap-4 font-tajawal">
    <div>
        @if(!$todayAttendance || !$todayAttendance->check_in)
        <button id="startDayBtn" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white font-bold shadow-md hover:shadow-lg transition-all" style="background:linear-gradient(135deg,#16a34a,#15803d)">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            بدء يوم العمل
        </button>
        @elseif($todayAttendance->current_status === 'checkout_pending')
        <div class="flex flex-wrap items-center gap-3">
            <div class="px-5 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 font-bold">
                طلب الانصراف لدى العمليات — بانتظار الموافقة
            </div>
            <button id="cancelCheckoutBtn" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-amber-300 text-amber-900 text-sm font-bold hover:bg-amber-100">
                إلغاء الطلب
            </button>
        </div>
        @elseif(!$todayAttendance->check_out)
        <div class="flex flex-wrap items-center gap-3">
            <button id="checkOutBtn" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white font-bold shadow-md hover:shadow-lg transition-all" style="background:linear-gradient(135deg,#dc2626,#b91c1c)">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                تسجيل انصراف
            </button>
            <div class="relative" id="timerControlDropdown">
                <button id="timerControlBtn" type="button" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl border-2 border-gray-200 text-gray-700 font-semibold text-sm bg-white hover:bg-gray-50">
                    <span id="timerControlText">الاستراحة</span>
                </button>
                <div id="timerControlMenu" class="hidden absolute top-full right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border z-50 py-2">
                    <button id="startBreakBtn" type="button" class="w-full text-right px-4 py-3 text-sm hover:bg-gray-50 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> بدء الاستراحة
                    </button>
                    <button id="endBreakBtn" type="button" class="hidden w-full text-right px-4 py-3 text-sm hover:bg-gray-50 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> انتهاء الاستراحة
                    </button>
                </div>
            </div>
        </div>
        @else
        <div class="px-5 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 font-bold">
            اكتمل يومك — {{ $todayAttendance->total_hours }} ساعة
        </div>
        @endif
    </div>
</div>

@if($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out && $todayAttendance->current_status !== 'checkout_pending')
<div class="mb-6 bg-white rounded-2xl border p-5 sm:p-6 font-tajawal" id="workTimeCard">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-gray-900 mb-1">وقت العمل الحالي</h3>
            <p class="text-sm text-gray-500">بدأت في {{ $todayAttendance->check_in->format('H:i') }}</p>
            <div id="breakStatus" class="hidden mt-2 text-sm text-amber-700 font-semibold">في الاستراحة منذ: <span id="breakStartTime"></span></div>
        </div>
        <div class="text-center">
            <div id="workTimer" class="text-4xl font-bold tabular-nums" style="color:{{ $themeColor }}">00:00:00</div>
            <p class="text-xs text-gray-500 mt-1">ساعات العمل الفعلية</p>
        </div>
    </div>
</div>
@endif
@endif

{{-- إحصائيات اليوم --}}
<div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الموظفين', 'value' => $stats['total_employees'], 'accent' => 'theme', 'compact' => true, 'href' => route('attendances.index') . '#page-data', 'linkLabel' => 'عرض السجل'])
    @include('crm.partials.stat-card', ['label' => 'حاضرون / سجّلوا', 'value' => $stats['present_today'], 'accent' => 'green', 'compact' => true, 'href' => route('attendances.index', ['status' => 'present']) . '#page-data', 'linkLabel' => 'عرض الحاضرين'])
    @include('crm.partials.stat-card', ['label' => 'غائبون', 'value' => $stats['absent_today'], 'accent' => 'red', 'compact' => true, 'href' => route('attendances.index', ['status' => 'absent']) . '#page-data', 'linkLabel' => 'عرض الغائبين'])
    @include('crm.partials.stat-card', ['label' => 'متأخرون', 'value' => $stats['late_today'], 'accent' => 'amber', 'compact' => true, 'href' => route('attendances.index', ['status' => 'late']) . '#page-data', 'linkLabel' => 'عرض المتأخرين'])
    @if($isToday)
    @include('crm.partials.stat-card', ['label' => 'يعملون الآن', 'value' => $stats['working_now'], 'accent' => 'blue', 'compact' => true, 'href' => route('attendances.index', ['status' => 'working']) . '#page-data', 'linkLabel' => 'عرض النشطين'])
    @endif
    @include('crm.partials.stat-card', ['label' => 'معدل الحضور', 'value' => $stats['attendance_rate'] . '%', 'accent' => 'purple', 'compact' => true, 'href' => route('attendances.index') . '#page-data', 'linkLabel' => 'عرض السجل'])
</div>

{{-- فلاتر --}}
@if($canViewRoster)
<div class="bg-white rounded-2xl border p-4 sm:p-5 mb-6 font-tajawal">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">التاريخ</label>
            <input type="date" name="date" value="{{ $dateValue }}" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
        </div>
        @if($canViewAll && $departments->isNotEmpty())
        <div class="w-full sm:w-44">
            <label class="block text-xs font-bold text-gray-500 mb-1">القسم</label>
            <select name="department_id" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <option value="">كل الأقسام</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        @if($employeesList->count() > 1)
        <div class="w-full sm:w-48">
            <label class="block text-xs font-bold text-gray-500 mb-1">الموظف</label>
            <select name="employee_id" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <option value="">الكل</option>
                @foreach($employeesList as $emp)
                <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="w-full sm:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <option value="">الكل</option>
                <option value="present" @selected(request('status') === 'present')>مكتمل</option>
                <option value="working" @selected(request('status') === 'working')>يعمل الآن</option>
                <option value="checkout_pending" @selected(request('status') === 'checkout_pending')>انصراف بانتظار العمليات</option>
                <option value="on_break" @selected(request('status') === 'on_break')>في استراحة</option>
                <option value="late" @selected(request('status') === 'late')>متأخر</option>
                <option value="absent" @selected(request('status') === 'absent')>غائب</option>
                <option value="on_leave" @selected(request('status') === 'on_leave')>في إجازة</option>
                <option value="off_day" @selected(request('status') === 'off_day')>إجازة أسبوعية</option>
                <option value="half_day" @selected(request('status') === 'half_day')>ناقص</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">عرض</button>
        @if(request()->hasAny(['department_id','employee_id','status']) || request('date') !== now()->toDateString())
        <a href="{{ route('attendances.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-bold">اليوم</a>
        @endif
    </form>
</div>
@endif

{{-- جدول الحضور اليومي الكامل --}}
<div id="page-data" class="bg-white rounded-2xl shadow-lg border overflow-hidden mb-6 font-tajawal">
    <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-2">
        <h2 class="font-bold text-gray-900">
            @if($scopeMode === 'self')
                حضوري — {{ $selectedDate->format('Y/m/d') }}
            @else
                سجل الحضور اليومي — {{ $roster->count() }} موظف
            @endif
        </h2>
        @if($stats['on_leave'] > 0 || $stats['off_day'] > 0)
        <p class="text-xs text-gray-500">
            @if($stats['on_leave'] > 0){{ $stats['on_leave'] }} في إجازة @endif
            @if($stats['off_day'] > 0) · {{ $stats['off_day'] }} إجازة أسبوعية @endif
        </p>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[1000px]">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-3 text-right font-bold">الموظف</th>
                    <th class="p-3 text-right font-bold">القسم</th>
                    <th class="p-3 text-right font-bold">الدوام المقرر</th>
                    <th class="p-3 text-right font-bold">الحضور</th>
                    <th class="p-3 text-right font-bold">الانصراف</th>
                    <th class="p-3 text-right font-bold">الاستراحة</th>
                    <th class="p-3 text-right font-bold">الساعات</th>
                    <th class="p-3 text-right font-bold">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($roster as $row)
            @php
                $emp = $row['employee'];
                $att = $row['attendance'];
                $fullName = trim($emp->first_name . ' ' . $emp->last_name);
            @endphp
            <tr class="hover:bg-gray-50/80">
                <td class="p-3">
                    <p class="font-semibold text-gray-900">{{ $fullName }}</p>
                    <p class="text-xs text-gray-500">{{ $emp->position }}</p>
                </td>
                <td class="p-3 text-xs text-gray-600">{{ $emp->department?->name ?? '—' }}</td>
                <td class="p-3 text-xs font-mono" dir="ltr">{{ $row['scheduled_in'] }} — {{ $row['scheduled_out'] }}</td>
                <td class="p-3">
                    @if($att?->check_in)
                    <span class="font-mono font-semibold" dir="ltr">{{ $att->check_in->format('H:i') }}</span>
                    @if($row['is_late'])
                    <span class="block text-xs text-orange-600">+{{ $row['late_minutes'] }} د</span>
                    @endif
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="p-3">
                    @if($att?->check_out)
                    <span class="font-mono font-semibold" dir="ltr">{{ $att->check_out->format('H:i') }}</span>
                    @if($row['is_early'])
                    <span class="block text-xs text-red-600">مبكر</span>
                    @endif
                    @elseif($att?->current_status === 'checkout_pending')
                    <span class="text-xs text-amber-700 font-semibold">بانتظار العمليات</span>
                    @elseif($att?->check_in)
                    <span class="text-xs text-blue-600 font-semibold">لم ينصرف</span>
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="p-3 text-xs text-gray-600">
                    @if($att?->break_duration_minutes)
                    {{ $att->break_duration_minutes }} د
                    @elseif($att?->current_status === 'on_break')
                    <span class="text-amber-600 font-semibold">جارية</span>
                    @else
                    —
                    @endif
                </td>
                <td class="p-3">
                    @if($att?->total_hours)
                    <span class="font-bold">{{ $att->total_hours }}h</span>
                    @elseif($att?->check_in && !$att->check_out)
                    <span class="text-xs text-blue-600">جاري</span>
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="p-3">
                    @include('attendances.partials.status-badge', ['label' => $row['status_label'], 'color' => $row['status_color']])
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="p-12 text-center text-gray-500">لا توجد سجلات للعرض</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- سجل شخصي (آخر 14 يوم) --}}
@if($employee && $personalHistory->isNotEmpty())
<div class="bg-white rounded-2xl border overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b font-bold text-gray-900">سجلي الشخصي — آخر {{ $personalHistory->count() }} يوم</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-right">التاريخ</th>
                    <th class="p-3 text-right">الحضور</th>
                    <th class="p-3 text-right">الانصراف</th>
                    <th class="p-3 text-right">الساعات</th>
                    <th class="p-3 text-right">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y">
            @foreach($personalHistory as $att)
            <tr>
                <td class="p-3">{{ $att->date->format('Y/m/d') }}</td>
                <td class="p-3 font-mono" dir="ltr">{{ $att->check_in?->format('H:i') ?? '—' }}</td>
                <td class="p-3 font-mono" dir="ltr">{{ $att->check_out?->format('H:i') ?? '—' }}</td>
                <td class="p-3">{{ $att->total_hours ? $att->total_hours . 'h' : '—' }}</td>
                <td class="p-3">
                    @php
                        $statusMap = ['present'=>'مكتمل','late'=>'متأخر','half_day'=>'ناقص','absent'=>'غائب'];
                        $statusColors = ['present'=>'green','late'=>'orange','half_day'=>'red','absent'=>'gray'];
                    @endphp
                    @include('attendances.partials.status-badge', [
                        'label' => $statusMap[$att->status] ?? $att->status,
                        'color' => $statusColors[$att->status] ?? 'gray',
                    ])
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if(($canClockIn ?? false) && $isToday)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDayBtn = document.getElementById('startDayBtn');
    if (startDayBtn) startDayBtn.addEventListener('click', e => { e.preventDefault(); checkIn(); });

    const timerControlBtn = document.getElementById('timerControlBtn');
    const timerControlMenu = document.getElementById('timerControlMenu');
    if (timerControlBtn && timerControlMenu) {
        timerControlBtn.addEventListener('click', e => { e.stopPropagation(); timerControlMenu.classList.toggle('hidden'); });
        document.addEventListener('click', () => timerControlMenu.classList.add('hidden'));
    }

    document.getElementById('checkOutBtn')?.addEventListener('click', checkOut);
    document.getElementById('cancelCheckoutBtn')?.addEventListener('click', cancelCheckout);
    document.getElementById('startBreakBtn')?.addEventListener('click', startBreak);
    document.getElementById('endBreakBtn')?.addEventListener('click', endBreak);

    const workTimer = document.getElementById('workTimer');
    if (workTimer) startWorkTimer();

    function csrfHeaders() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token?.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        };
    }

    function checkIn() {
        fetch('{{ route("attendances.check-in") }}', { method: 'POST', headers: csrfHeaders() })
            .then(r => r.json()).then(data => {
                if (data?.success) { notify(data.message, 'success'); setTimeout(() => location.reload(), 800); }
                else notify(data?.error || 'خطأ', 'error');
            }).catch(() => notify('خطأ في تسجيل الحضور', 'error'));
    }

    function checkOut() {
        if (!confirm('إرسال طلب الانصراف لمدير العمليات؟ لن يُسجَّل الانصراف إلا بعد الموافقة.')) return;
        fetch('{{ route("attendances.check-out") }}', { method: 'POST', headers: csrfHeaders() })
            .then(r => r.json()).then(data => {
                if (data?.success) { notify(data.message, 'success'); setTimeout(() => location.reload(), 800); }
                else notify(data?.error || 'خطأ', 'error');
            }).catch(() => notify('خطأ في الانصراف', 'error'));
    }

    function cancelCheckout() {
        const notes = prompt('سبب إلغاء طلب الانصراف:');
        if (!notes || !notes.trim()) return;
        fetch('{{ route("attendances.cancel-checkout") }}', {
            method: 'POST',
            headers: csrfHeaders(),
            body: JSON.stringify({ notes: notes.trim() }),
        })
            .then(r => r.json()).then(data => {
                if (data?.success) { notify(data.message, 'success'); setTimeout(() => location.reload(), 800); }
                else notify(data?.error || 'خطأ', 'error');
            }).catch(() => notify('تعذر إلغاء الطلب', 'error'));
    }

    function startBreak() {
        fetch('{{ route("attendances.start-break") }}', { method: 'POST', headers: csrfHeaders() })
            .then(r => r.json()).then(data => {
                if (data?.success) { notify(data.message, 'success'); updateBreakUI(true, data.break_start_time); }
                else notify(data?.error || 'خطأ', 'error');
            });
    }

    function endBreak() {
        fetch('{{ route("attendances.end-break") }}', { method: 'POST', headers: csrfHeaders() })
            .then(r => r.json()).then(data => {
                if (data?.success) { notify(data.message, 'success'); updateBreakUI(false); }
                else notify(data?.error || 'خطأ', 'error');
            });
    }

    function updateBreakUI(onBreak, breakStart = null) {
        document.getElementById('breakStatus')?.classList.toggle('hidden', !onBreak);
        document.getElementById('startBreakBtn')?.classList.toggle('hidden', onBreak);
        document.getElementById('endBreakBtn')?.classList.toggle('hidden', !onBreak);
        document.getElementById('timerControlText').textContent = onBreak ? 'في الاستراحة' : 'الاستراحة';
        if (breakStart) document.getElementById('breakStartTime').textContent = breakStart;
    }

    let workTimerInterval = null;
    function startWorkTimer() {
        if (workTimerInterval) clearInterval(workTimerInterval);
        function tick() {
            fetch('{{ route("attendances.current-work-time") }}')
                .then(r => r.json())
                .then(data => {
                    if (data.work_time) workTimer.textContent = data.work_time;
                    if (data.current_status === 'on_break') updateBreakUI(true, data.break_start_time);
                    else if (data.current_status === 'working') updateBreakUI(false);
                    if (data.current_status === 'completed' && workTimerInterval) clearInterval(workTimerInterval);
                });
        }
        tick();
        workTimerInterval = setInterval(tick, 1000);
    }

    function notify(message, type) {
        const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-blue-600' };
        const el = document.createElement('div');
        el.className = `fixed top-4 left-4 ${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-lg z-50 text-sm font-bold`;
        el.textContent = message;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }
});
</script>
@endif
@endsection
