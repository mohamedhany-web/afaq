@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
@endphp
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        سجل حركات العميل
        <p class="text-xs font-normal text-gray-500 mt-1">التعديلات، التحويل بين السيلز، والحذف — مع اسم المنفّذ والتوقيت</p>
    </div>
    <div class="divide-y divide-gray-100 max-h-[28rem] overflow-y-auto">
        @forelse($activityLogs as $log)
        <article class="px-5 sm:px-6 py-4 text-sm font-tajawal">
            <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                <span class="inline-flex px-2 py-0.5 rounded-lg text-[11px] font-bold bg-gray-100 text-gray-700">{{ $log->action_name }}</span>
                <time class="text-[11px] text-gray-400" datetime="{{ $log->created_at->toIso8601String() }}">
                    {{ $log->created_at->format('Y/m/d') }} · {{ $log->created_at->format('H:i') }}
                </time>
            </div>
            <p class="text-gray-800">{{ $log->description }}</p>
            @if($log->user)
            <p class="text-xs text-gray-500 mt-1">بواسطة: <strong>{{ $log->user->name }}</strong></p>
            @endif
            @php $changes = $log->new_values['changes'] ?? $log->old_values['changes'] ?? null; @endphp
            @if(is_array($changes) && count($changes))
            <ul class="mt-2 space-y-1 text-xs text-gray-600 bg-gray-50 rounded-lg p-3">
                @foreach($changes as $change)
                <li><strong>{{ $change['label'] ?? $change['field'] }}:</strong> {{ $change['from'] ?? '—' }} ← {{ $change['to'] ?? '—' }}</li>
                @endforeach
            </ul>
            @endif
            @if(in_array($log->action, ['client_transferred', 'client_bulk_transferred'], true))
            <div class="mt-2 text-xs rounded-lg p-3 bg-blue-50 text-blue-900 space-y-1">
                @if($log->old_values['assigned_name'] ?? null)
                <p><strong>من:</strong> {{ $log->old_values['assigned_name'] }}</p>
                @endif
                @if($log->new_values['assigned_name'] ?? null)
                <p><strong>إلى:</strong> {{ $log->new_values['assigned_name'] }}</p>
                @endif
                @php
                    $taskCount = $log->new_values['tasks_transferred_count'] ?? 0;
                    $taskItems = $log->new_values['tasks_transferred'] ?? [];
                @endphp
                @if($taskCount > 0)
                <p><strong>مهام مُحوَّلة:</strong> {{ $taskCount }}</p>
                @if(is_array($taskItems) && count($taskItems))
                <ul class="list-disc list-inside text-blue-800">
                    @foreach(array_slice($taskItems, 0, 5) as $task)
                    <li>{{ $task['title'] ?? 'مهمة' }}</li>
                    @endforeach
                    @if(count($taskItems) > 5)
                    <li class="text-blue-600">+{{ count($taskItems) - 5 }} مهمة أخرى</li>
                    @endif
                </ul>
                @endif
                @endif
                @if($log->action === 'client_bulk_transferred' && ($log->new_values['clients_count'] ?? 0) > 1)
                <p class="text-blue-700"><strong>عدد العملاء:</strong> {{ $log->new_values['clients_count'] }}</p>
                @endif
            </div>
            @endif
            @if($log->old_values['delete_reason'] ?? $log->new_values['delete_reason'] ?? null)
            <p class="text-xs text-red-700 mt-1">سبب الحذف: {{ $log->old_values['delete_reason'] ?? $log->new_values['delete_reason'] }}</p>
            @endif
        </article>
        @empty
        <p class="p-8 text-center text-gray-400 text-sm font-tajawal">لا توجد حركات مسجّلة بعد.</p>
        @endforelse
    </div>
</div>
