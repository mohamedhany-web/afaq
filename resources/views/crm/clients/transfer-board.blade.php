@extends('layouts.app')
@section('page-title', 'تحويل وسحب العملاء')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $pr = fn (string $action, mixed $params = []) => route($clientsRoutePrefix . '.' . $action, $params);
    $boardUrl = $pr('transfer-board');
    $transferUrlTemplate = preg_replace('/\/\d+(\/transfer)?(\?|$)/', '/__ID__/transfer$2', $pr('transfer', ['client' => 0]));
    $taskTransferUrlTemplate = preg_replace('/\/\d+(\/transfer)?(\?|$)/', '/__ID__/transfer$2', route($tasksRoutePrefix . '.transfer', ['task' => 0]));
    $bulkTransferUrl = $pr('bulk-transfer');
@endphp

@include('crm.partials.page-header', [
    'title' => 'تحويل وسحب العملاء والمهام',
    'subtitle' => 'اسحب البطاقات بين أعمدة السيلز — يُسجَّل كل تحويل في سجل الحركات مع اسم المنفّذ والتاريخ والوقت',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
    'secondaryUrl' => $pr('index'),
    'secondaryLabel' => 'قائمة العملاء',
    'secondaryIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>',
])

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif
<div id="transfer-toast" class="hidden fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-50 p-4 rounded-xl shadow-lg text-sm font-tajawal border"></div>

<div class="flex flex-col lg:flex-row gap-4 mb-4 font-tajawal">
  <form method="GET" action="{{ $boardUrl }}" class="flex flex-wrap gap-2 items-center flex-1">
      <input type="hidden" name="tab" value="{{ $tab }}">
      <input type="search" name="search" value="{{ $search }}" placeholder="بحث بالاسم أو رقم الهاتف..."
             class="flex-1 min-w-[12rem] border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
      <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">بحث</button>
      @if($search)
      <a href="{{ $boardUrl }}?tab={{ $tab }}" class="px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-600">مسح</a>
      @endif
  </form>
  <div class="flex flex-wrap gap-2 items-center">
      <a href="{{ $boardUrl }}?tab=clients{{ $search ? '&search=' . urlencode($search) : '' }}"
         class="px-4 py-2 rounded-xl text-sm font-bold {{ $tab === 'clients' ? 'text-white' : 'border border-gray-200 text-gray-600' }}"
         @if($tab === 'clients') style="background:{{ $themeColor }}" @endif>العملاء والأرقام</a>
      <a href="{{ $boardUrl }}?tab=tasks{{ $search ? '&search=' . urlencode($search) : '' }}"
         class="px-4 py-2 rounded-xl text-sm font-bold {{ $tab === 'tasks' ? 'text-white' : 'border border-gray-200 text-gray-600' }}"
         @if($tab === 'tasks') style="background:{{ $themeColor }}" @endif>المهام النشطة</a>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
    <div class="xl:col-span-9">
        @if($tab === 'clients')
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500 font-tajawal px-1">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="transfer-tasks-toggle" class="rounded border-gray-300" {{ $transferTasksDefault ? 'checked' : '' }} style="accent-color:{{ $themeColor }}">
                تحويل المهام المرتبطة عند سحب العميل
            </label>
            <span>اسحب البطاقة إلى عمود السيلز المستهدف · أو حدّد عدة عملاء ثم انقلهم جماعياً</span>
        </div>

        <div id="client-transfer-board"
             class="flex gap-3 overflow-x-auto pb-4 snap-x snap-mandatory"
             data-transfer-url="{{ $transferUrlTemplate }}"
             data-bulk-url="{{ $bulkTransferUrl }}"
             data-csrf="{{ csrf_token() }}">
            @foreach($clientColumns as $column)
            <div class="transfer-column shrink-0 w-64 sm:w-72 snap-start flex flex-col max-h-[calc(100vh-14rem)]"
                 data-employee-id="{{ $column['employee_id'] ?? '' }}"
                 data-column-key="{{ $column['key'] }}">
                <div class="rounded-t-xl px-3 py-2.5 border border-b-0 border-gray-200" style="{{ $headerStyle }}">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-bold text-sm text-gray-900 truncate">{{ $column['name'] }}</h3>
                        <span class="transfer-column-count text-xs font-bold px-2 py-0.5 rounded-full bg-white border border-gray-200 text-gray-600">{{ $column['total'] }}</span>
                    </div>
                    @if($column['shown'] < $column['total'])
                    <p class="text-[10px] text-gray-400 mt-0.5">يعرض {{ $column['shown'] }} من {{ $column['total'] }}</p>
                    @endif
                </div>
                <div class="transfer-drop-zone flex-1 overflow-y-auto p-2 space-y-2 bg-gray-50/80 border border-gray-200 rounded-b-xl min-h-[12rem]"
                     data-employee-id="{{ $column['employee_id'] ?? '' }}">
                    @forelse($column['clients'] as $client)
                    <article class="transfer-client-card kanban-card bg-white rounded-xl border border-gray-200 p-3 shadow-sm cursor-grab active:cursor-grabbing"
                             data-client-id="{{ $client['id'] }}"
                             data-name="{{ $client['name'] }}">
                        <div class="flex items-start gap-2">
                            <input type="checkbox" class="client-pick rounded border-gray-300 mt-0.5 shrink-0" value="{{ $client['id'] }}" onclick="event.stopPropagation()">
                            <div class="min-w-0 flex-1">
                                <a href="{{ $client['profile_url'] }}" class="font-bold text-sm text-gray-900 hover:underline block truncate" draggable="false">{{ $client['name'] }}</a>
                                <p class="text-xs text-gray-500 mt-0.5 font-mono" dir="ltr">{{ $client['phone'] ?? '—' }}</p>
                                <span class="inline-block mt-1 text-[10px] px-1.5 py-px rounded bg-gray-100 text-gray-600">{{ $client['stage_label'] }}</span>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="transfer-empty text-center py-6 text-xs text-gray-400">اسحب عميلاً هنا</div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>

        <div id="client-bulk-bar" class="hidden mt-4 p-4 rounded-2xl border-2 bg-white shadow-sm" style="border-color:{{ $themeColor }}40">
            <p class="text-sm font-bold text-gray-800 mb-2"><span id="picked-count">0</span> عميل محدّد</p>
            <div class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">تحويل إلى</label>
                    <select id="bulk-target-employee" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm min-w-[10rem]">
                        @foreach($clientColumns as $column)
                            @if($column['employee_id'])
                            <option value="{{ $column['employee_id'] }}">{{ $column['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <button type="button" id="bulk-transfer-btn"
                        class="px-5 py-2.5 rounded-xl text-white text-sm font-bold"
                        style="background:{{ $themeColor }}">تحويل المحدد</button>
            </div>
        </div>
        @else
        <div class="mb-3 text-xs text-gray-500 font-tajawal px-1">اسحب المهمة النشطة إلى عمود السيلز الجديد — يُسجَّل التحويل في سجل المهمة</div>
        <div id="task-transfer-board"
             class="flex gap-3 overflow-x-auto pb-4"
             data-transfer-url="{{ $taskTransferUrlTemplate }}"
             data-csrf="{{ csrf_token() }}">
            @foreach($taskColumns as $column)
            <div class="transfer-column shrink-0 w-64 sm:w-72 flex flex-col max-h-[calc(100vh-14rem)]"
                 data-user-id="{{ $column['user_id'] }}">
                <div class="rounded-t-xl px-3 py-2.5 border border-b-0 border-gray-200" style="{{ $headerStyle }}">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-bold text-sm text-gray-900 truncate">{{ $column['name'] }}</h3>
                        <span class="transfer-column-count text-xs font-bold px-2 py-0.5 rounded-full bg-white border border-gray-200">{{ $column['total'] }}</span>
                    </div>
                </div>
                <div class="task-drop-zone flex-1 overflow-y-auto p-2 space-y-2 bg-gray-50/80 border border-gray-200 rounded-b-xl min-h-[12rem]"
                     data-user-id="{{ $column['user_id'] }}">
                    @forelse($column['tasks'] as $task)
                    <article class="transfer-task-card kanban-card bg-white rounded-xl border border-gray-200 p-3 shadow-sm cursor-grab"
                             data-task-id="{{ $task['id'] }}">
                        <a href="{{ $task['show_url'] }}" class="font-bold text-sm text-gray-900 hover:underline block leading-snug" draggable="false">{{ $task['title'] }}</a>
                        <p class="text-[10px] text-gray-500 mt-1">{{ $task['status_label'] }} · {{ $task['priority_label'] }}</p>
                        @if($task['client_name'])
                        <p class="text-xs text-gray-600 mt-1 truncate">{{ $task['client_name'] }} <span class="font-mono text-gray-400" dir="ltr">{{ $task['client_phone'] }}</span></p>
                        @endif
                        <p class="text-[10px] text-gray-400 mt-1 tabular-nums">{{ $task['due_at'] }}</p>
                    </article>
                    @empty
                    <div class="transfer-empty text-center py-6 text-xs text-gray-400">لا مهام</div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="xl:col-span-3">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden sticky top-4">
            <div class="px-4 py-3 border-b font-bold text-sm text-gray-900" style="{{ $headerStyle }}">آخر عمليات التحويل</div>
            <div class="divide-y divide-gray-100 max-h-[calc(100vh-10rem)] overflow-y-auto text-sm font-tajawal">
                @forelse($recentLogs as $log)
                <article class="px-4 py-3">
                    <div class="flex justify-between gap-2 text-[11px] text-gray-400 mb-1">
                        <span class="font-bold text-gray-600">{{ $log->action_name }}</span>
                        <time datetime="{{ $log->created_at->toIso8601String() }}">{{ $log->created_at->format('Y/m/d H:i') }}</time>
                    </div>
                    <p class="text-gray-800 text-xs leading-relaxed">{{ $log->description }}</p>
                    @if($log->user)
                    <p class="text-[11px] text-gray-500 mt-1">بواسطة: <strong>{{ $log->user->name }}</strong></p>
                    @endif
                </article>
                @empty
                <p class="p-6 text-center text-gray-400 text-xs">لا توجد تحويلات مسجّلة بعد</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toast = document.getElementById('transfer-toast');
    function showToast(msg, ok) {
        if (!toast) return;
        toast.textContent = msg;
        toast.className = 'fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-50 p-4 rounded-xl shadow-lg text-sm font-tajawal border '
            + (ok ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800');
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 4000);
    }

    @if($tab === 'clients')
    const board = document.getElementById('client-transfer-board');
    if (board && typeof Sortable !== 'undefined') {
        const transferUrl = board.dataset.transferUrl || '';
        const bulkUrl = board.dataset.bulkUrl || '';
        const csrf = board.dataset.csrf || '';
        const tasksToggle = document.getElementById('transfer-tasks-toggle');

        function adjustCount(zone, delta) {
            const col = zone.closest('.transfer-column');
            const badge = col?.querySelector('.transfer-column-count');
            if (!badge) return;
            badge.textContent = String(Math.max(0, parseInt(badge.textContent, 10) + delta));
        }

        function ensureEmpty(zone) {
            const cards = zone.querySelectorAll('.transfer-client-card').length;
            let empty = zone.querySelector('.transfer-empty');
            if (cards === 0 && !empty) {
                empty = document.createElement('div');
                empty.className = 'transfer-empty text-center py-6 text-xs text-gray-400';
                empty.textContent = 'اسحب عميلاً هنا';
                zone.appendChild(empty);
            } else if (cards > 0 && empty) {
                empty.remove();
            }
        }

        board.querySelectorAll('.transfer-drop-zone').forEach(function (zone) {
            new Sortable(zone, {
                group: 'client-transfer-board',
                animation: 150,
                draggable: '.transfer-client-card',
                ghostClass: 'opacity-40',
                filter: 'a, input',
                preventOnFilter: true,
                onEnd: async function (evt) {
                    const card = evt.item;
                    const clientId = card.dataset.clientId;
                    const fromEmp = evt.from.dataset.employeeId || '';
                    const toEmp = evt.to.dataset.employeeId || '';
                    ensureEmpty(evt.from);
                    ensureEmpty(evt.to);

                    if (!clientId || fromEmp === toEmp) return;
                    if (!toEmp) {
                        evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                        ensureEmpty(evt.from);
                        ensureEmpty(evt.to);
                        showToast('لا يمكن سحب العميل إلى «غير معيّن» — استخدم التوزيع العكسي من قائمة العملاء.', false);
                        return;
                    }

                    const url = transferUrl.replace('__ID__', clientId);
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                employee_id: parseInt(toEmp, 10),
                                transfer_tasks: tasksToggle?.checked ? 1 : 0,
                            }),
                        });
                        const data = await res.json();
                        if (!res.ok || !data.ok) throw new Error(data.message || 'failed');
                        adjustCount(evt.from, -1);
                        adjustCount(evt.to, 1);
                        showToast(data.message, true);
                    } catch (e) {
                        evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                        ensureEmpty(evt.from);
                        ensureEmpty(evt.to);
                        showToast('تعذر التحويل — حاول مرة أخرى.', false);
                    }
                },
            });
        });

        const picks = document.querySelectorAll('.client-pick');
        const bulkBar = document.getElementById('client-bulk-bar');
        const pickedCount = document.getElementById('picked-count');
        function syncPicks() {
            const selected = [...picks].filter(p => p.checked).map(p => p.value);
            if (bulkBar) bulkBar.classList.toggle('hidden', selected.length === 0);
            if (pickedCount) pickedCount.textContent = String(selected.length);
            return selected;
        }
        picks.forEach(p => p.addEventListener('change', syncPicks));

        document.getElementById('bulk-transfer-btn')?.addEventListener('click', async function () {
            const ids = syncPicks();
            const emp = document.getElementById('bulk-target-employee')?.value;
            if (!ids.length || !emp) return;
            if (!confirm('تحويل ' + ids.length + ' عميل؟')) return;
            try {
                const body = new FormData();
                body.append('_token', csrf);
                ids.forEach(id => body.append('client_ids[]', id));
                body.append('employee_id', emp);
                if (tasksToggle?.checked) body.append('transfer_tasks', '1');
                const res = await fetch(bulkUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body,
                });
                const data = await res.json();
                if (!res.ok || !data.ok) throw new Error();
                showToast(data.message, true);
                setTimeout(() => location.reload(), 800);
            } catch (e) {
                showToast('تعذر التحويل الجماعي.', false);
            }
        });
    }
    @else
    const taskBoard = document.getElementById('task-transfer-board');
    if (taskBoard && typeof Sortable !== 'undefined') {
        const transferUrl = taskBoard.dataset.transferUrl || '';
        const csrf = taskBoard.dataset.csrf || '';

        function adjustTaskCount(zone, delta) {
            const col = zone.closest('.transfer-column');
            const badge = col?.querySelector('.transfer-column-count');
            if (badge) badge.textContent = String(Math.max(0, parseInt(badge.textContent, 10) + delta));
        }

        taskBoard.querySelectorAll('.task-drop-zone').forEach(function (zone) {
            new Sortable(zone, {
                group: 'task-transfer-board',
                animation: 150,
                draggable: '.transfer-task-card',
                ghostClass: 'opacity-40',
                filter: 'a',
                preventOnFilter: true,
                onEnd: async function (evt) {
                    const card = evt.item;
                    const taskId = card.dataset.taskId;
                    const fromUser = evt.from.dataset.userId || '';
                    const toUser = evt.to.dataset.userId || '';
                    if (!taskId || fromUser === toUser) return;

                    const url = transferUrl.replace('__ID__', taskId);
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ assigned_to: parseInt(toUser, 10) }),
                        });
                        const data = await res.json();
                        if (!res.ok || !data.ok) throw new Error();
                        adjustTaskCount(evt.from, -1);
                        adjustTaskCount(evt.to, 1);
                        showToast(data.message, true);
                    } catch (e) {
                        evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                        showToast('تعذر تحويل المهمة.', false);
                    }
                },
            });
        });
    }
    @endif
});
</script>
@endpush
