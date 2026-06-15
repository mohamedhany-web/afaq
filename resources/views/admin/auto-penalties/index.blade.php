@extends('layouts.app')
@section('page-title', 'الخصومات والعقوبات التلقائية')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

@include('crm.partials.page-header', [
    'title' => 'الخصومات والعقوبات التلقائية',
    'subtitle' => 'خصومات تلقائية عند تأخر المهام أو عدم رفع التقارير أو مخالفات الحضور',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'actionUrl' => route('crm.employee-compliance.index'),
    'actionLabel' => 'ملخص الالتزام',
])

<div class="flex flex-wrap gap-3 mb-6">
    @can('edit-settings')
    <button type="button" onclick="document.getElementById('create-rule-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md font-tajawal"
            style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        إضافة قاعدة
    </button>
    <form method="POST" action="{{ route('admin.auto-penalties.process') }}">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 text-sm font-semibold font-tajawal hover:bg-gray-50"
                style="border-color:{{ $themeColor }}40;color:{{ $themeColor }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            تطبيق العقوبات الآن
        </button>
    </form>
    @endcan
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'قواعد نشطة', 'value' => $stats['rules_active'], 'accent' => 'theme', 'compact' => true, 'href' => route('admin.auto-penalties.index') . '#page-data', 'linkLabel' => 'عرض السجل'])
    @include('crm.partials.stat-card', ['label' => 'خصومات اليوم', 'value' => $stats['applied_today'], 'accent' => 'red', 'compact' => true, 'href' => route('admin.auto-penalties.index') . '#page-data', 'linkLabel' => 'عرض السجل'])
    @include('crm.partials.stat-card', ['label' => 'إجمالي الشهر', 'value' => $money($stats['applied_month_amount']), 'accent' => 'amber', 'compact' => true, 'href' => route('admin.auto-penalties.index') . '#page-data', 'linkLabel' => 'عرض السجل'])
    @include('crm.partials.stat-card', ['label' => 'سجل العقوبات', 'value' => $stats['total_logs'], 'accent' => 'blue', 'compact' => true, 'href' => route('admin.auto-penalties.index') . '#page-data', 'linkLabel' => 'عرض السجل'])
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    @foreach($departmentSummary as $code => $dept)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 font-tajawal">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-900">{{ $dept['label'] }}</h3>
            <span class="text-xs font-bold px-2 py-1 rounded-lg bg-gray-100 text-gray-600">{{ $code }}</span>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center text-sm">
            <div><div class="text-xs text-gray-500">متأخر</div><div class="text-lg font-bold text-amber-600 tabular-nums">{{ $dept['overdue'] }}</div></div>
            <div><div class="text-xs text-gray-500">اليوم</div><div class="text-lg font-bold text-red-600 tabular-nums">{{ $dept['applied_today'] }}</div></div>
            <div><div class="text-xs text-gray-500">الشهر</div><div class="text-lg font-bold text-gray-900 tabular-nums">{{ $money($dept['applied_month']) }}</div></div>
        </div>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">قواعد العقوبات</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px] font-tajawal">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="p-4 text-right font-bold">القاعدة</th>
                    <th class="p-4 text-right font-bold">القسم</th>
                    <th class="p-4 text-right font-bold">النوع</th>
                    <th class="p-4 text-center font-bold">المبلغ</th>
                    <th class="p-4 text-center font-bold">الحالة</th>
                    @can('edit-settings')
                    <th class="p-4 text-center font-bold">إجراءات</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rules as $rule)
                <tr class="hover:bg-gray-50">
                    <td class="p-4">
                        <div class="font-bold text-gray-900">{{ $rule->name }}</div>
                        <div class="text-xs text-gray-500">{{ $rule->appliesToLabel() }} · سماح {{ $rule->grace_hours }}س</div>
                    </td>
                    <td class="p-4 text-gray-700">{{ $rule->departmentLabel() }}</td>
                    <td class="p-4 text-gray-700">{{ $rule->sourceLabel() }}</td>
                    <td class="p-4 text-center font-bold text-red-600 tabular-nums">{{ $money($rule->amount) }}</td>
                    <td class="p-4 text-center">
                        @if($rule->is_active)
                            <span class="text-xs font-bold text-green-700 bg-green-50 px-2 py-1 rounded-lg">نشط</span>
                        @else
                            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">موقوف</span>
                        @endif
                    </td>
                    @can('edit-settings')
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <button type="button" onclick="document.getElementById('edit-rule-{{ $rule->id }}').classList.remove('hidden')"
                                    class="text-xs font-bold hover:underline" style="color:{{ $themeColor }}">تعديل</button>
                            <form method="POST" action="{{ route('admin.auto-penalties.toggle', $rule) }}" class="inline">@csrf @method('PATCH')
                                <button class="text-xs font-bold text-blue-600 hover:underline">{{ $rule->is_active ? 'إيقاف' : 'تفعيل' }}</button>
                            </form>
                            @if(!$rule->logs_count)
                            <form method="POST" action="{{ route('admin.auto-penalties.destroy', $rule) }}" class="inline" onsubmit="return confirm('حذف القاعدة؟')">@csrf @method('DELETE')
                                <button class="text-xs font-bold text-red-600 hover:underline">حذف</button>
                            </form>
                            @endif
                        </div>
                    </td>
                    @endcan
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center text-gray-500">لا توجد قواعد. شغّل البذرة أو أضف قاعدة جديدة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="{{ $headerStyle }}">آخر الخصومات المطبّقة</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[800px] font-tajawal">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="p-4 text-right font-bold">الموظف</th>
                    <th class="p-4 text-right font-bold">القاعدة</th>
                    <th class="p-4 text-right font-bold">السبب</th>
                    <th class="p-4 text-center font-bold">المبلغ</th>
                    <th class="p-4 text-center font-bold">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentLogs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-bold text-gray-900">{{ $log->user?->name ?? '—' }}</td>
                    <td class="p-4 text-gray-700">{{ $log->rule?->name ?? '—' }}</td>
                    <td class="p-4 text-gray-600 max-w-xs truncate" title="{{ $log->reason }}">{{ $log->reason }}</td>
                    <td class="p-4 text-center font-bold text-red-600 tabular-nums">{{ $money($log->amount) }}</td>
                    <td class="p-4 text-center text-gray-500">{{ $log->applied_at?->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-gray-500">لم تُطبَّق عقوبات تلقائية بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@can('edit-settings')
{{-- إنشاء قاعدة --}}
<div id="create-rule-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto font-tajawal">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="{{ $headerStyle }}">
            <h3 class="font-bold text-gray-900">إضافة قاعدة عقوبة</h3>
            <button type="button" onclick="document.getElementById('create-rule-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.auto-penalties.store') }}" class="p-6 space-y-4">
            @csrf
            @include('admin.auto-penalties.partials.rule-form-fields', ['prefix' => 'create'])
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-bold text-sm" style="background:{{ $themeColor }}">حفظ</button>
                <button type="button" onclick="document.getElementById('create-rule-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border text-sm font-semibold">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- تعديل القواعد --}}
@foreach($rules as $rule)
<div id="edit-rule-{{ $rule->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto font-tajawal">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="{{ $headerStyle }}">
            <h3 class="font-bold text-gray-900">تعديل: {{ $rule->name }}</h3>
            <button type="button" onclick="document.getElementById('edit-rule-{{ $rule->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.auto-penalties.update', $rule) }}" class="p-6 space-y-4">
            @csrf @method('PUT')
            @include('admin.auto-penalties.partials.rule-form-fields', ['rule' => $rule, 'prefix' => 'edit-'.$rule->id])
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-bold text-sm" style="background:{{ $themeColor }}">حفظ التعديل</button>
                <button type="button" onclick="document.getElementById('edit-rule-{{ $rule->id }}').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border text-sm font-semibold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
document.querySelectorAll('.rule-source-type').forEach(function (select) {
    const toggle = () => {
        const wrap = select.closest('form')?.querySelector('.rule-report-period-wrap');
        if (!wrap) return;
        const show = ['daily_sales_report', 'marketing_report'].includes(select.value);
        wrap.classList.toggle('hidden', !show);
    };
    select.addEventListener('change', toggle);
    toggle();
});
</script>
@endcan
@endsection
