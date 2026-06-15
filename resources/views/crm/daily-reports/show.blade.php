@extends('layouts.app')

@section('page-title', 'تقرير مبيعات يومي')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $m = $report->metrics ?? [];
    $v = fn (string $s, string $k) => data_get($m, "{$s}.{$k}", 0);
    $isSubmitted = $report->isSubmitted();
@endphp

@include('crm.partials.page-header', [
    'title' => 'التقرير اليومي للمبيعات',
    'subtitle' => ($report->author?->name ?? '—') . ' — ' . $report->report_date->format('Y-m-d'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
])

<div class="w-full space-y-6">
    @include('crm.daily-reports.partials.alerts')

    {{-- شريط معلومات وأزرار --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                @if($isSubmitted)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-800 font-tajawal">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        مرفوع
                    </span>
                    @if($report->submitted_at)
                    <span class="text-xs text-gray-500 font-tajawal">في {{ $report->submitted_at->format('Y-m-d H:i') }}</span>
                    @endif
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 font-tajawal">مسودة</span>
                    @if($canEdit)
                    <span class="text-xs text-gray-500 font-tajawal">أكمل الأقسام 7–8 ثم ارفع التقرير</span>
                    @endif
                @endif
                @if(!empty($m['generated_at']))
                <span class="text-xs text-gray-400 font-tajawal border-r border-gray-200 pr-3 mr-1">
                    بيانات النظام: {{ \Carbon\Carbon::parse($m['generated_at'])->format('H:i Y-m-d') }}
                </span>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('crm.daily-reports.index') }}"
                   class="px-4 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">
                    رجوع
                </a>
                @if($canEdit)
                <form action="{{ route('crm.daily-reports.refresh', $report) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-xl border-2 border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 font-tajawal">
                        تحديث من النظام
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ملخص سريع --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-8 gap-3">
        @php $compactCard = ['compact' => true, 'href' => '#report-body', 'linkLabel' => 'عرض التفاصيل']; @endphp
        @include('crm.partials.stat-card', array_merge($compactCard, ['label' => 'عملاء جدد', 'value' => $v('lead_summary', 'new_leads_received'), 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>']))
        @include('crm.partials.stat-card', array_merge($compactCard, ['label' => 'مكالمات', 'value' => $v('communication', 'calls_made'), 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>']))
        @include('crm.partials.stat-card', array_merge($compactCard, ['label' => 'اجتماعات', 'value' => $v('meetings_visits', 'meetings_completed'), 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>']))
        @include('crm.partials.stat-card', array_merge($compactCard, ['label' => 'صفقات رابحة', 'value' => $v('deals', 'deals_closed_won'), 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>']))
        @include('crm.partials.stat-card', array_merge($compactCard, ['label' => 'متابعات', 'value' => $v('follow_ups', 'follow_ups_completed'), 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>']))
        @include('crm.partials.stat-card', array_merge($compactCard, ['label' => 'متأخرة', 'value' => $v('follow_ups', 'overdue_follow_ups'), 'accent' => 'red', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>']))
        @include('crm.partials.stat-card', array_merge($compactCard, ['label' => 'عروض', 'value' => $v('pipeline_progress', 'proposals_sent'), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>']))
        @include('crm.partials.stat-card', array_merge($compactCard, ['label' => 'غداً', 'value' => $v('follow_ups', 'follow_ups_scheduled_tomorrow'), 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>']))
    </div>

    @if($canEdit)
    <div id="report-body" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        {{-- بيانات النظام (1–6) --}}
        <div class="xl:col-span-8 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-900 font-tajawal text-lg">بيانات النظام (تلقائية)</h2>
                <span class="text-xs text-gray-400 font-tajawal">الأقسام 1–6</span>
            </div>
            @include('crm.daily-reports.partials.metrics-grid', ['report' => $report])
        </div>

        {{-- إدخال يدوي (7–8) — ثابت على الشاشات الكبيرة --}}
        <div class="xl:col-span-4 xl:sticky xl:top-4 space-y-4" id="daily-report-manual-root">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-900 font-tajawal text-lg">إدخال يدوي</h2>
                <span class="text-xs text-gray-400 font-tajawal">7–8</span>
            </div>
            @include('crm.daily-reports.partials.manual-fields', ['report' => $report])

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 space-y-3">
                <p class="text-xs text-gray-500 font-tajawal text-center">احفظ التعديلات أو ارفع التقرير للإدارة</p>
                <div class="flex flex-col gap-2">
                    <form action="{{ route('crm.daily-reports.update', $report) }}" method="POST" id="daily-report-save-form">
                        @csrf
                        @method('PUT')
                        <button type="button" onclick="syncDailyReportManualFields(); document.getElementById('daily-report-save-form').requestSubmit();"
                                class="w-full px-4 py-3 rounded-xl bg-gray-800 text-white text-sm font-bold font-tajawal hover:bg-gray-900 transition">
                            حفظ المسودة
                        </button>
                    </form>
                    <form action="{{ route('crm.daily-reports.submit', $report) }}" method="POST" id="daily-report-submit-form">
                        @csrf
                        <button type="button"
                                onclick="if(syncDailyReportManualFields() && confirm('رفع التقرير؟ لن تتمكن من تعديله بعد الرفع.')) document.getElementById('daily-report-submit-form').requestSubmit();"
                                class="w-full px-4 py-3 rounded-xl text-white text-sm font-bold font-tajawal shadow-md hover:shadow-lg transition"
                                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                            رفع التقرير
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function syncDailyReportManualFields() {
            const root = document.getElementById('daily-report-manual-root');
            if (!root) return true;
            ['daily-report-save-form', 'daily-report-submit-form'].forEach(function (formId) {
                const form = document.getElementById(formId);
                if (!form) return;
                form.querySelectorAll('[data-sync-field]').forEach(function (el) { el.remove(); });
                ['obstacles', 'support_required', 'tomorrow_planned_calls', 'tomorrow_planned_meetings', 'tomorrow_planned_visits', 'tomorrow_priority_leads'].forEach(function (name) {
                    const src = root.querySelector('[name="' + name + '"]');
                    if (!src) return;
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = src.value;
                    input.setAttribute('data-sync-field', '1');
                    form.appendChild(input);
                });
            });
            return true;
        }
    </script>
    @endpush

    @else
    {{-- عرض للمدير / الإدارة --}}
    <div id="report-body" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-gray-900 font-tajawal text-lg">بيانات النظام</h2>
            <span class="text-xs text-gray-400 font-tajawal">الأقسام 1–6</span>
        </div>
        @include('crm.daily-reports.partials.metrics-grid', ['report' => $report])
    </div>

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-gray-900 font-tajawal text-lg">التحديات وخطة الغد</h2>
            <span class="text-xs text-gray-400 font-tajawal">الأقسام 7–8</span>
        </div>
        @include('crm.daily-reports.partials.manual-readonly', ['report' => $report])
    </div>
    @endif
</div>
@endsection
