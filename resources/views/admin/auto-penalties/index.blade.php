@extends('layouts.app')

@section('page-title', 'الخصومات والعقوبات التلقائية')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

<div class="w-full max-w-7xl mx-auto">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">الخصومات والعقوبات التلقائية</h1>
            <p class="text-gray-600 mt-2">خصومات تلقائية عند تأخر المهمات أو عدم رفع التقارير — تشمل المديرين والموظفين في كل قسم</p>
        </div>
        @can('edit-settings')
        <form method="POST" action="{{ route('admin.auto-penalties.process') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-sm shadow-lg hover:opacity-90 transition" style="background: {{ $themeColor }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                تطبيق العقوبات الآن
            </button>
        </form>
        @endcan
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-semibold">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="text-sm font-bold text-gray-600">قواعد نشطة</div>
            <div class="text-2xl font-extrabold text-gray-900 mt-2">{{ $stats['rules_active'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="text-sm font-bold text-gray-600">خصومات اليوم</div>
            <div class="text-2xl font-extrabold text-red-600 mt-2">{{ $stats['applied_today'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="text-sm font-bold text-gray-600">إجمالي الشهر</div>
            <div class="text-2xl font-extrabold text-red-600 mt-2">{{ $money($stats['applied_month_amount']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="text-sm font-bold text-gray-600">سجل العقوبات</div>
            <div class="text-2xl font-extrabold text-gray-900 mt-2">{{ $stats['total_logs'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        @foreach($departmentSummary as $code => $dept)
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-extrabold text-gray-900">{{ $dept['label'] }}</h3>
                    <span class="text-xs font-bold px-2 py-1 rounded-lg bg-gray-100 text-gray-600">{{ $code }}</span>
                </div>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div>
                        <div class="text-xs text-gray-500">متأخر الآن</div>
                        <div class="text-xl font-bold text-amber-600">{{ $dept['overdue'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">خصومات اليوم</div>
                        <div class="text-xl font-bold text-red-600">{{ $dept['applied_today'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">خصومات الشهر</div>
                        <div class="text-xl font-bold text-gray-900">{{ $money($dept['applied_month']) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between" style="{{ $headerStyle }}">
                <div>
                    <h2 class="text-lg font-extrabold text-gray-900">قواعد العقوبات</h2>
                    <p class="text-sm text-gray-600 mt-1">إعداد الخصم لكل قسم ونوع مخالفة</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500">القاعدة</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500">القسم</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-500">النوع</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500">المبلغ</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500">الحالة</th>
                            @can('edit-settings')
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500">إجراء</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900">{{ $rule->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $rule->appliesToLabel() }} · سماح {{ $rule->grace_hours }}س</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $rule->departmentLabel() }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $rule->sourceLabel() }}</td>
                                <td class="px-4 py-3 text-center font-bold text-red-600">{{ $money($rule->amount) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($rule->is_active)
                                        <span class="text-xs font-bold text-green-700 bg-green-50 px-2 py-1 rounded-lg">نشط</span>
                                    @else
                                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">موقوف</span>
                                    @endif
                                </td>
                                @can('edit-settings')
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" action="{{ route('admin.auto-penalties.toggle', $rule) }}" class="inline">@csrf @method('PATCH')
                                        <button class="text-xs font-bold text-blue-600 hover:underline">{{ $rule->is_active ? 'إيقاف' : 'تفعيل' }}</button>
                                    </form>
                                </td>
                                @endcan
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد قواعد بعد. شغّل البذرة أو أضف قاعدة جديدة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @can('edit-settings')
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100" style="{{ $headerStyle }}">
                <h2 class="text-lg font-extrabold text-gray-900">إضافة قاعدة جديدة</h2>
                <p class="text-sm text-gray-600 mt-1">تخصيص عقوبة لقسم ونوع مخالفة محدد</p>
            </div>
            <form method="POST" action="{{ route('admin.auto-penalties.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">اسم القاعدة</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">القسم</label>
                        <select name="department_code" class="w-full rounded-lg border-gray-300">
                            <option value="">كل الأقسام</option>
                            @foreach($departments as $code => $label)
                                <option value="{{ $code }}" @selected(old('department_code') === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">نوع المخالفة</label>
                        <select name="source_type" required class="w-full rounded-lg border-gray-300" id="source-type-select">
                            @foreach($sourceTypes as $key => $label)
                                <option value="{{ $key }}" @selected(old('source_type') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="report-period-wrap" class="hidden">
                    <label class="block text-sm font-bold text-gray-700 mb-1">دورة التقرير</label>
                    <select name="report_period_type" class="w-full rounded-lg border-gray-300">
                        @foreach($reportPeriodTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">ينطبق على</label>
                        <select name="applies_to" class="w-full rounded-lg border-gray-300">
                            @foreach($appliesTo as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">مبلغ الخصم (ر.س)</label>
                        <input type="number" name="amount" step="0.01" min="0" value="{{ old('amount', 50) }}" required class="w-full rounded-lg border-gray-300">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">ساعات السماح بعد الموعد</label>
                    <input type="number" name="grace_hours" min="0" max="720" value="{{ old('grace_hours', 2) }}" required class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">وصف (اختياري)</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300">{{ old('description') }}</textarea>
                </div>
                <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
                    تفعيل القاعدة فوراً
                </label>
                <button type="submit" class="w-full py-2.5 rounded-xl text-white font-bold" style="background: {{ $themeColor }}">حفظ القاعدة</button>
            </form>
        </div>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100" style="{{ $headerStyle }}">
            <h2 class="text-lg font-extrabold text-gray-900">آخر الخصومات المطبّقة تلقائياً</h2>
            <p class="text-sm text-gray-600 mt-1">سجل العقوبات على الموظفين والمديرين</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500">الموظف</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500">القاعدة</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500">السبب</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500">المبلغ</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $log->user?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $log->rule?->name ?? '—' }}
                                @if($log->rule?->department_code)
                                    <span class="text-xs text-gray-400">({{ $log->rule->department_code }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $log->reason }}">{{ $log->reason }}</td>
                            <td class="px-6 py-4 text-center font-bold text-red-600">{{ $money($log->amount) }}</td>
                            <td class="px-6 py-4 text-center text-gray-500">{{ $log->applied_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">لم تُطبَّق عقوبات تلقائية بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@can('edit-settings')
<script>
document.getElementById('source-type-select')?.addEventListener('change', function () {
    const wrap = document.getElementById('report-period-wrap');
    const show = ['daily_sales_report', 'marketing_report'].includes(this.value);
    wrap.classList.toggle('hidden', !show);
});
document.getElementById('source-type-select')?.dispatchEvent(new Event('change'));
</script>
@endcan
@endsection
