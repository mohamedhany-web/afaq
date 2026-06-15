@extends('layouts.app')
@section('page-title', 'تقرير تسويق')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $m = $report->metrics ?? [];
    $v = fn ($s, $k) => data_get($m, "{$s}.{$k}", 0);
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
@endphp

@include('crm.partials.page-header', [
    'title' => 'تقرير ' . $report->periodLabel(),
    'subtitle' => ($report->author?->name ?? '—') . ' — ' . $report->periodRangeLabel(),
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>@endif

<div class="mb-4 flex flex-wrap gap-2 items-center">
    @if($report->isSubmitted())
    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 font-tajawal">مرفوع {{ $report->submitted_at?->format('Y-m-d H:i') }}</span>
    @else
    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 font-tajawal">مسودة — أكمل وارفع</span>
    @endif
    <a href="{{ route('marketing.reports.index', ['period' => $report->period_type]) }}" class="text-sm text-gray-600 font-tajawal mr-auto">← العودة</a>
    @if($canEdit)
    <form action="{{ route('marketing.reports.refresh', $report) }}" method="POST" class="inline">@csrf
        <button type="submit" class="px-4 py-2 rounded-xl border text-sm font-tajawal">تحديث من النظام</button>
    </form>
    @endif
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'مهام مكتملة', 'value' => $v('activities', 'completed'), 'accent' => 'purple', 'href' => '#report-body', 'linkLabel' => 'عرض التقرير'])
    @include('crm.partials.stat-card', ['label' => 'Leads جديدة', 'value' => $v('leads', 'created'), 'accent' => 'blue', 'href' => '#report-body', 'linkLabel' => 'عرض التقرير'])
    @include('crm.partials.stat-card', ['label' => 'مهام مجدولة', 'value' => $v('activities', 'assigned'), 'accent' => 'theme', 'href' => '#report-body', 'linkLabel' => 'عرض التقرير'])
    @include('crm.partials.stat-card', ['label' => 'حملات نشطة', 'value' => $v('campaigns', 'active_involved'), 'accent' => 'green', 'href' => '#report-body', 'linkLabel' => 'عرض التقرير'])
</div>

@if(!empty($m['team']))
<div class="mb-6 p-5 rounded-2xl bg-purple-50 border border-purple-100 font-tajawal">
    <p class="font-bold text-purple-900 mb-2">ملخص الفريق (من النظام)</p>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
        <div>أعضاء: <strong>{{ $m['team']['members_count'] ?? 0 }}</strong></div>
        <div>Leads الفريق: <strong>{{ $m['team']['leads_created'] ?? 0 }}</strong></div>
        <div>تقارير اليوم: <strong>{{ $m['team']['daily_reports_submitted_today'] ?? 0 }}</strong></div>
        <div>ناقص اليوم: <strong class="text-red-700">{{ $m['team']['daily_reports_missing_today'] ?? 0 }}</strong></div>
    </div>
</div>
@endif

@if($canEdit)
<div class="bg-white rounded-2xl shadow-lg border p-5 sm:p-6 space-y-4 font-tajawal">
    <form id="report-fields">
        <div><label class="{{ $label }}">ملخص الأنشطة *</label><textarea name="activities_summary" rows="4" required class="{{ $input }}">{{ old('activities_summary', $report->activities_summary) }}</textarea></div>
        <div><label class="{{ $label }}">تقدم الحملات</label><textarea name="campaigns_progress" rows="3" class="{{ $input }}">{{ old('campaigns_progress', $report->campaigns_progress) }}</textarea></div>
        @if(in_array($report->period_type, ['weekly', 'monthly'], true))
        <div><label class="{{ $label }}">ملخص أداء الفريق *</label><textarea name="team_summary" rows="4" required class="{{ $input }}">{{ old('team_summary', $report->team_summary) }}</textarea></div>
        @endif
        <div><label class="{{ $label }}">معوقات</label><textarea name="obstacles" rows="2" class="{{ $input }}">{{ old('obstacles', $report->obstacles) }}</textarea></div>
        <div><label class="{{ $label }}">دعم مطلوب</label><textarea name="support_required" rows="2" class="{{ $input }}">{{ old('support_required', $report->support_required) }}</textarea></div>
        <div><label class="{{ $label }}">خطة الفترة القادمة</label><textarea name="next_period_plan" rows="2" class="{{ $input }}">{{ old('next_period_plan', $report->next_period_plan) }}</textarea></div>
    </form>
    <div class="flex flex-wrap gap-3 pt-2">
        <form action="{{ route('marketing.reports.update', $report) }}" method="POST">
            @csrf @method('PUT')
            <button type="submit" onclick="copyReportFields(event)" class="px-6 py-3 rounded-xl border font-semibold text-sm">حفظ مسودة</button>
        </form>
        <form action="{{ route('marketing.reports.submit', $report) }}" method="POST">
            @csrf
            <button type="submit" onclick="copyReportFields(event)" class="px-8 py-3 rounded-xl text-white font-semibold text-sm" style="background:#7c3aed">رفع التقرير</button>
        </form>
    </div>
</div>
<script>
function copyReportFields(e) {
    const src = document.getElementById('report-fields');
    const form = e.target.closest('form');
    src.querySelectorAll('textarea').forEach(el => {
        let hidden = form.querySelector(`[name="${el.name}"]`);
        if (!hidden) { hidden = document.createElement('input'); hidden.type = 'hidden'; hidden.name = el.name; form.appendChild(hidden); }
        hidden.value = el.value;
    });
}
</script>
@else
<div class="bg-white rounded-2xl shadow-lg border p-5 sm:p-6 space-y-4 font-tajawal text-sm">
    <div><strong>ملخص الأنشطة:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $report->activities_summary ?? '—' }}</p></div>
    <div><strong>تقدم الحملات:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $report->campaigns_progress ?? '—' }}</p></div>
    @if($report->team_summary)<div><strong>ملخص الفريق:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $report->team_summary }}</p></div>@endif
    <div><strong>معوقات:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $report->obstacles ?? '—' }}</p></div>
    <div><strong>خطة قادمة:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $report->next_period_plan ?? '—' }}</p></div>
</div>
@endif
@endsection
