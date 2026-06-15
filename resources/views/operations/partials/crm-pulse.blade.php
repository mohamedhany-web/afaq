@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $today = now()->toDateString();
    $yesterday = now()->subDay()->toDateString();
    $monthLabel = now()->locale('ar')->translatedFormat('F Y');

    $cards = [
        [
            'key' => 'today_comments',
            'label' => 'تعليقات اليوم',
            'value' => $crmPulse['today_comments'],
            'accent' => 'theme',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
            'href' => route('operations.follow-ups.index', ['bucket' => 'today', 'date' => $today]) . '#page-data',
            'linkLabel' => 'عرض التفاعلات',
        ],
        [
            'key' => 'yesterday_comments',
            'label' => 'تعليقات الأمس',
            'value' => $crmPulse['yesterday_comments'],
            'accent' => 'blue',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'href' => route('operations.follow-ups.index', ['bucket' => 'completed', 'date' => $yesterday]) . '#page-data',
            'linkLabel' => 'عرض الأمس',
        ],
        [
            'key' => 'missed_reminders_yesterday',
            'label' => 'متأخرة من الأمس',
            'value' => $crmPulse['missed_reminders_yesterday'],
            'accent' => 'red',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'href' => route('operations.follow-ups.index', ['bucket' => 'overdue']) . '#page-data',
            'linkLabel' => 'متابعة الفائت',
        ],
        [
            'key' => 'today_reminders',
            'label' => 'تذكيرات اليوم',
            'value' => $crmPulse['today_reminders'],
            'accent' => 'amber',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
            'href' => route('operations.follow-ups.index', ['bucket' => 'today', 'date' => $today]) . '#page-data',
            'linkLabel' => 'جدول اليوم',
        ],
        [
            'key' => 'done_deals',
            'label' => 'صفقات منجزة',
            'value' => $crmPulse['done_deals'],
            'accent' => 'green',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won', 'show_closed' => 1]) . '#pipeline-kanban',
            'linkLabel' => 'عرض المنجزة',
            'hint' => $monthLabel,
        ],
        [
            'key' => 'potential_clients',
            'label' => 'عملاء محتملون',
            'value' => $crmPulse['potential_clients'],
            'accent' => 'purple',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
            'href' => auth()->user()->clientsHubUrl(['status' => 'prospect']) . '#page-data',
            'linkLabel' => 'عرض المحتملين',
        ],
        [
            'key' => 'cancelled_deals',
            'label' => 'صفقات ملغاة',
            'value' => $crmPulse['cancelled_deals'],
            'accent' => 'red',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_lost', 'show_closed' => 1]) . '#pipeline-kanban',
            'linkLabel' => 'عرض الملغاة',
            'hint' => $monthLabel,
        ],
    ];
@endphp

<div class="mb-2">
    <h2 class="text-sm font-bold text-gray-800 font-tajawal">نبض CRM اليومي</h2>
    <p class="text-xs text-gray-500 font-tajawal">تعليقات · تذكيرات · صفقات · عملاء محتملون</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3 mb-4">
    @foreach($cards as $card)
    @include('crm.partials.stat-card', [
        'label' => $card['label'],
        'value' => $card['value'],
        'accent' => $card['accent'],
        'icon' => $card['icon'],
        'href' => $card['href'],
        'linkLabel' => $card['linkLabel'],
        'compact' => true,
    ])
    @endforeach
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6 font-tajawal" id="crm-pulse-details">
    @foreach($cards as $card)
    @php $items = $crmPulse['recent'][$card['key']] ?? collect(); @endphp
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-4 py-3 border-b flex items-center justify-between gap-2"
             style="background: linear-gradient(135deg, {{ $themeColor }}06, transparent);">
            <div>
                <h3 class="text-sm font-bold text-gray-900">{{ $card['label'] }}</h3>
                @if(!empty($card['hint']))
                <p class="text-[10px] text-gray-400">{{ $card['hint'] }}</p>
                @endif
            </div>
            <a href="{{ $card['href'] }}" class="text-[10px] font-bold hover:underline shrink-0" style="color:{{ $themeColor }}">{{ $card['linkLabel'] }}</a>
        </div>
        <ul class="divide-y divide-gray-100 max-h-52 overflow-y-auto">
            @forelse($items as $row)
                @if($card['key'] === 'today_comments' || $card['key'] === 'yesterday_comments')
                <li class="px-4 py-3 text-sm">
                    <a href="{{ $row->client?->profileUrl() ?? '#' }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $row->client?->name ?? '—' }}</a>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $row->user?->name ?? '—' }} · {{ $row->typeLabel() }}</p>
                    @if($row->notes)
                    <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $row->notes }}</p>
                    @endif
                </li>
                @elseif($card['key'] === 'missed_reminders_yesterday')
                <li class="px-4 py-3 text-sm">
                    @if($row['type'] === 'follow_up')
                    @php $fu = $row['item']; @endphp
                    <a href="{{ $fu->client?->profileUrl() ?? '#' }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $fu->client?->name ?? '—' }}</a>
                    <p class="text-xs text-gray-500">متابعة · {{ $fu->user?->name }} — {{ $fu->scheduled_at?->format('H:i') }} · {{ $fu->typeLabel() }}</p>
                    @else
                    @php $task = $row['item']; @endphp
                    <a href="{{ route('crm.tasks.show', $task) }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $task->title }}</a>
                    <p class="text-xs text-gray-500">مهمة · {{ $task->assignee?->name }} — {{ $task->due_at?->format('H:i') }}</p>
                    @endif
                </li>
                @elseif($card['key'] === 'today_reminders')
                <li class="px-4 py-3 text-sm">
                    @if($row['type'] === 'follow_up')
                    @php $fu = $row['item']; @endphp
                    <a href="{{ $fu->client?->profileUrl() ?? '#' }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $fu->client?->name ?? '—' }}</a>
                    <p class="text-xs text-gray-500">متابعة · {{ $fu->user?->name }} — {{ $fu->scheduled_at?->format('H:i') }}</p>
                    @else
                    @php $task = $row['item']; @endphp
                    <a href="{{ route('crm.tasks.show', $task) }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $task->title }}</a>
                    <p class="text-xs text-gray-500">مهمة · {{ $task->assignee?->name }} — {{ $task->due_at?->format('H:i') }}</p>
                    @endif
                </li>
                @elseif($card['key'] === 'done_deals' || $card['key'] === 'cancelled_deals')
                <li class="px-4 py-3 text-sm">
                    <a href="{{ route('crm.pipeline.show', $row) }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $row->client?->name ?? $row->product_service }}</a>
                    <p class="text-xs text-gray-500">{{ $row->salesRep?->name ?? '—' }} · {{ number_format($row->estimated_value) }}</p>
                </li>
                @elseif($card['key'] === 'potential_clients')
                <li class="px-4 py-3 text-sm">
                    <a href="{{ $row->profileUrl() }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $row->name }}</a>
                    <p class="text-xs text-gray-500">
                        {{ $row->assignedEmployee ? trim($row->assignedEmployee->first_name.' '.$row->assignedEmployee->last_name) : 'غير مُعيَّن' }}
                        · {{ $row->lead_stage ?? $row->status }}
                    </p>
                </li>
                @endif
            @empty
                <li class="px-4 py-8 text-center text-xs text-gray-400">لا توجد بيانات</li>
            @endforelse
        </ul>
    </div>
    @endforeach
</div>
