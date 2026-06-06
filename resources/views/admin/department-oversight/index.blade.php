@extends('layouts.app')

@section('page-title', 'لوحة متابعة الأقسام')

@section('content')
<div class="w-full max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">لوحة متابعة الأقسام</h1>
        <p class="text-gray-600 mt-2">نظرة شاملة على الأقسام والتقارير والمشاريع العقارية</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="text-sm font-bold text-gray-600">الأقسام</div>
            <div class="text-2xl font-extrabold text-gray-900 mt-2">{{ $stats['departments_total'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="text-sm font-bold text-gray-600">المشاريع</div>
            <div class="text-2xl font-extrabold text-gray-900 mt-2">{{ $stats['projects_total'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="text-sm font-bold text-gray-600">تقارير الأقسام</div>
            <div class="text-2xl font-extrabold text-gray-900 mt-2">{{ $stats['reports_total'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-gray-900">الأقسام</h2>
                <a class="text-sm font-bold text-blue-600 hover:underline" href="{{ route('admin.department-reports.index') }}">كل التقارير</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">القسم</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">المدير</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">الموظفون</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">المشاريع</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">آخر تقرير</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($departments as $dept)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $dept->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $dept->manager?->user?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $dept->employees_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $dept->projects_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ isset($latestReportByDept[$dept->id]) ? \Illuminate\Support\Carbon::parse($latestReportByDept[$dept->id])->format('Y-m-d') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-extrabold text-gray-900">آخر التقارير</h2>
                <p class="text-sm text-gray-600 mt-1">للمتابعة السريعة واتخاذ القرار</p>
            </div>
            <div class="p-6 space-y-4">
                @forelse($recentReports as $report)
                    <div class="rounded-xl border border-gray-200 p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="font-bold text-gray-900">{{ $report->department?->name ?? '—' }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    مدير القسم: <span class="font-semibold">{{ $report->department?->manager?->user?->name ?? '—' }}</span>
                                    <span class="text-gray-300 mx-1">|</span>
                                    مشروع: <span class="font-semibold">{{ $report->project?->name ?? '—' }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    {{ $report->created_at?->diffForHumans() }}
                                    <span class="text-gray-300 mx-1">|</span>
                                    @if($report->status === 'submitted')
                                        <span class="font-bold text-green-700">مُرسل</span>
                                    @else
                                        <span class="font-bold text-gray-800">مسودة</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.department-reports.show', $report) }}" class="text-blue-600 font-bold text-sm hover:underline">عرض</a>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-600">لا توجد تقارير بعد.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

