@extends('layouts.app')

@section('page-title', 'تقرير الحضور')

@section('content')
<div class="w-full">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">تقرير الحضور والانصراف</h1>
                </div>
                <p class="text-gray-600 mr-12">من {{ $start_date }} إلى {{ $end_date }}</p>
            </div>
            <a href="{{ route('reports.attendance.print', request()->query()) }}" target="_blank" class="bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-900 transition-all duration-200 flex items-center shadow-lg hover:shadow-xl">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                طباعة التقرير
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">الموظف</label>
                <select name="employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">جميع الموظفين</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    عرض
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">أيام الحضور</p>
            <p class="text-3xl font-bold text-green-600">{{ $summary['present_days'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">أيام الغياب</p>
            <p class="text-3xl font-bold text-red-600">{{ $summary['absent_days'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">أيام التأخير</p>
            <p class="text-3xl font-bold text-amber-600">{{ $summary['late_days'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">معدل الحضور</p>
            <p class="text-3xl font-bold text-blue-600">{{ $summary['attendance_rate'] }}%</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الساعات</p>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($summary['total_hours'], 0) }}</p>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">موعد الدوام</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحضور</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الانصراف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تأخير</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الساعات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $statusLabels = [
                            'present' => 'حاضر',
                            'late' => 'متأخر',
                            'absent' => 'غائب',
                            'half_day' => 'نصف يوم',
                            'leave' => 'إجازة',
                        ];
                    @endphp
                    @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->date?->format('Y-m-d') ?? $attendance->date }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->employee->first_name ?? '' }} {{ $attendance->employee->last_name ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500" dir="ltr">{{ $attendance->scheduled_check_in_at?->format('H:i') ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600" dir="ltr">{{ $attendance->check_in?->format('H:i') ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600" dir="ltr">{{ $attendance->check_out?->format('H:i') ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm {{ ($attendance->late_minutes ?? 0) > 0 ? 'text-amber-700 font-semibold' : 'text-gray-400' }}">
                            {{ ($attendance->late_minutes ?? 0) > 0 ? $attendance->late_minutes . ' د' : '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $attendance->total_hours ?? 0 }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium font-tajawal
                                @if($attendance->status === 'present') bg-green-100 text-green-800
                                @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                @elseif($attendance->status === 'half_day') bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $statusLabels[$attendance->status] ?? $attendance->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500 font-tajawal">لا توجد سجلات في هذه الفترة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
