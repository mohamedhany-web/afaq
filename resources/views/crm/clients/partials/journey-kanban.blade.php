@php
    $activeStages = ['lead', 'prospect', 'proposal', 'negotiation'];
    $closedStages = ['closed_won', 'closed_lost'];
    $currentStage = $client->lead_stage ?? 'lead';
    $stageColors = [
        'lead' => ['bg' => '#6366f1', 'light' => '#eef2ff'],
        'prospect' => ['bg' => '#3b82f6', 'light' => '#eff6ff'],
        'proposal' => ['bg' => '#0ea5e9', 'light' => '#f0f9ff'],
        'negotiation' => ['bg' => '#f59e0b', 'light' => '#fffbeb'],
        'closed_won' => ['bg' => '#16a34a', 'light' => '#f0fdf4'],
        'closed_lost' => ['bg' => '#ef4444', 'light' => '#fef2f2'],
    ];
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <div>
            <h3 class="font-bold text-gray-900 font-tajawal">مسار العميل — اسحب لتحويل المرحلة</h3>
            <p class="text-xs text-gray-500 mt-1 font-tajawal">المرحلة الحالية: <strong>{{ $stageLabels[$currentStage] ?? $currentStage }}</strong></p>
        </div>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">Drag & Drop</span>
    </div>
    <div class="p-4 sm:p-5 overflow-x-auto">
        <div class="flex gap-3 min-w-max pb-2">
            @foreach(array_merge($activeStages, $closedStages) as $stage)
                @php $color = $stageColors[$stage]; @endphp
                <div class="w-56 shrink-0 rounded-xl border border-gray-200 overflow-hidden bg-gray-50/50">
                    <div class="px-3 py-2 border-b border-gray-100 text-center" style="background: {{ $color['light'] }};">
                        <span class="text-xs font-bold font-tajawal" style="color: {{ $color['bg'] }};">{{ $stageLabels[$stage] }}</span>
                    </div>
                    <div class="journey-kanban-zone kanban-drop-zone p-2 min-h-[100px]" data-journey-stage="{{ $stage }}">
                        @if($currentStage === $stage)
                        <div class="kanban-card bg-white rounded-lg p-3 border-2 shadow-sm cursor-grab active:cursor-grabbing font-tajawal"
                             style="border-color: {{ $color['bg'] }};"
                             data-client-id="{{ $client->id }}">
                            <p class="font-bold text-sm text-gray-900">
                                <a href="{{ $client->profileUrl() }}" class="hover:underline" style="color:{{ $themeColor }}">{{ $client->name }}</a>
                            </p>
                            <p class="text-xs text-gray-500 mt-1" dir="ltr">{{ $client->phone }}</p>
                        </div>
                        @else
                        <div class="kanban-empty h-16 rounded-lg border border-dashed border-gray-200 flex items-center justify-center">
                            <span class="text-[10px] text-gray-400">أفلت هنا</span>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@include('crm.partials.kanban-scripts', [
    'updateUrl' => route('crm.clients.update-lead-stage', ['client' => '__ID__']),
    'payloadKey' => 'lead_stage',
    'itemKey' => 'clientId',
])
