@php
    $m = $report->metrics ?? [];
    $v = fn (string $section, string $key) => data_get($m, "{$section}.{$key}", 0);
    $money = fn (string $section, string $key) => number_format((float) data_get($m, "{$section}.{$key}", 0), 2);
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();

    $wd = data_get($m, 'work_day', []);
    $workDayItems = [];
    if (!empty($wd['tracked'])) {
        if (!empty($wd['on_leave'])) {
            $workDayItems[] = ['label' => 'الحالة', 'value' => 'إجازة معتمدة', 'tone' => 'blue'];
        } else {
            $workDayItems[] = ['label' => 'الساعات المطلوبة', 'value' => ($wd['required_hours'] ?? 8) . ' س'];
            $workDayItems[] = ['label' => 'بدء العمل', 'value' => !empty($wd['day_started']) ? ($wd['check_in'] ?? '—') : 'لم يبدأ'];
            $workDayItems[] = ['label' => 'نهاية العمل', 'value' => $wd['check_out'] ?? ($wd['scheduled_end'] ? 'متوقع ' . $wd['scheduled_end'] : '—')];
            $workDayItems[] = ['label' => 'ساعات فعلية', 'value' => isset($wd['total_hours']) ? $wd['total_hours'] . ' س' : '—'];
            if (!empty($wd['auto_checkout'])) {
                $workDayItems[] = ['label' => 'إيقاف', 'value' => 'تلقائي', 'tone' => 'amber'];
            }
        }
    }

    $sections = [
        [
            'num' => '⏱',
            'title' => 'Work Day',
            'subtitle' => 'يوم العمل والحضور',
            'highlight' => true,
            'items' => $workDayItems ?: [['label' => '—', 'value' => 'غير مرتبط بسجل موظف']],
        ],
        [
            'num' => 1,
            'title' => 'Lead Summary',
            'subtitle' => 'ملخص العملاء',
            'items' => [
                ['label' => 'عملاء جدد', 'value' => $v('lead_summary', 'new_leads_received')],
                ['label' => 'تم التواصل', 'value' => $v('lead_summary', 'leads_contacted')],
                ['label' => 'مؤهلون', 'value' => $v('lead_summary', 'qualified_leads')],
                ['label' => 'غير مؤهلين', 'value' => $v('lead_summary', 'unqualified_leads')],
            ],
        ],
        [
            'num' => 2,
            'title' => 'Communication',
            'subtitle' => 'نشاط التواصل',
            'items' => [
                ['label' => 'مكالمات أُجريت', 'value' => $v('communication', 'calls_made')],
                ['label' => 'مكالمات مُجابة', 'value' => $v('communication', 'calls_answered')],
                ['label' => 'واتساب', 'value' => $v('communication', 'whatsapp_conversations')],
                ['label' => 'بريد إلكتروني', 'value' => $v('communication', 'emails_sent')],
            ],
        ],
        [
            'num' => 3,
            'title' => 'Meetings & Visits',
            'subtitle' => 'اجتماعات ومعاينات',
            'items' => [
                ['label' => 'اجتماعات مجدولة', 'value' => $v('meetings_visits', 'meetings_scheduled')],
                ['label' => 'اجتماعات منجزة', 'value' => $v('meetings_visits', 'meetings_completed')],
                ['label' => 'معاينات عقارية', 'value' => $v('meetings_visits', 'property_visits_conducted')],
            ],
        ],
        [
            'num' => 4,
            'title' => 'Pipeline',
            'subtitle' => 'مسار المبيعات',
            'items' => [
                ['label' => '→ مؤهل', 'value' => $v('pipeline_progress', 'leads_to_qualified')],
                ['label' => '→ تفاوض', 'value' => $v('pipeline_progress', 'leads_to_negotiation')],
                ['label' => 'عروض مرسلة', 'value' => $v('pipeline_progress', 'proposals_sent')],
                ['label' => 'عقود مرسلة', 'value' => $v('pipeline_progress', 'contracts_sent')],
            ],
        ],
        [
            'num' => 5,
            'title' => 'Deals',
            'subtitle' => 'الصفقات',
            'items' => [
                ['label' => 'رابحة', 'value' => $v('deals', 'deals_closed_won'), 'tone' => 'green'],
                ['label' => 'خاسرة', 'value' => $v('deals', 'deals_closed_lost'), 'tone' => 'red'],
                ['label' => 'إيراد متوقع', 'value' => $money('deals', 'expected_revenue_new_opportunities'), 'tone' => 'blue', 'is_money' => true],
            ],
        ],
        [
            'num' => 6,
            'title' => 'Follow-Ups',
            'subtitle' => 'المتابعات',
            'items' => [
                ['label' => 'مكتملة', 'value' => $v('follow_ups', 'follow_ups_completed')],
                ['label' => 'متأخرة', 'value' => $v('follow_ups', 'overdue_follow_ups'), 'tone' => 'amber'],
                ['label' => 'مجدولة غداً', 'value' => $v('follow_ups', 'follow_ups_scheduled_tomorrow')],
            ],
        ],
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-4">
    @foreach($sections as $section)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-gray-100 flex items-start gap-3"
             style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, transparent 100%);">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold text-white font-tajawal"
                  style="background: {{ $themeColor }};">{{ $section['num'] }}</span>
            <div class="min-w-0">
                <h3 class="font-bold text-gray-900 text-sm font-tajawal leading-tight">{{ $section['title'] }}</h3>
                <p class="text-xs text-gray-500 font-tajawal">{{ $section['subtitle'] }}</p>
            </div>
        </div>
        <dl class="p-4 space-y-2 flex-1">
            @foreach($section['items'] as $item)
            @php
                $tone = $item['tone'] ?? 'gray';
                $bg = match($tone) {
                    'green' => 'bg-green-50',
                    'red' => 'bg-red-50',
                    'blue' => 'bg-blue-50',
                    'amber' => 'bg-amber-50',
                    default => 'bg-gray-50',
                };
                $valColor = match($tone) {
                    'green' => 'text-green-900',
                    'red' => 'text-red-900',
                    'blue' => 'text-blue-900',
                    'amber' => 'text-amber-900',
                    default => 'text-gray-900',
                };
            @endphp
            <div class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 {{ $bg }}">
                <dt class="text-xs font-semibold text-gray-600 font-tajawal">{{ $item['label'] }}</dt>
                <dd class="text-lg font-bold {{ $valColor }} font-tajawal tabular-nums shrink-0">{{ $item['value'] }}</dd>
            </div>
            @endforeach
        </dl>
    </div>
    @endforeach
</div>
