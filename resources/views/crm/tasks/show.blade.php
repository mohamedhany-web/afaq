@extends('layouts.app')
@section('page-title', $task->title)

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $pColors = config('crm_tasks.priority_colors', []);
    $phone = $task->client?->phone;
    $wa = $phone ? 'https://wa.me/' . preg_replace('/\D+/', '', $phone) : null;
@endphp

@include('crm.partials.page-header', [
    'title' => $task->title,
    'subtitle' => $task->categoryLabel() . ' · ' . $task->priorityLabel() . ' · ' . $task->statusLabel(),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />',
    'actionUrl' => $canManage ? route('crm.tasks.edit', $task) : null,
    'actionLabel' => 'تعديل',
])

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6">
    <div class="xl:col-span-8 space-y-4">
        <div class="bg-white rounded-2xl border shadow-lg p-5 sm:p-6">
            @if($task->description)<p class="text-gray-700 font-tajawal leading-relaxed mb-4">{{ $task->description }}</p>@endif
            <dl class="grid sm:grid-cols-2 gap-3 text-sm font-tajawal">
                <div><dt class="text-gray-500 text-xs">المكلف</dt><dd class="font-bold">{{ $task->assignee?->name }}</dd></div>
                <div><dt class="text-gray-500 text-xs">المُعيِّن</dt><dd>{{ $task->assigner?->name ?? 'النظام' }}</dd></div>
                <div><dt class="text-gray-500 text-xs">الموعد النهائي</dt><dd class="font-bold tabular-nums {{ $task->isOverdue() ? 'text-red-600' : '' }}">{{ $task->due_at->format('Y/m/d H:i') }}</dd></div>
                @if($task->performance_score)<div><dt class="text-gray-500 text-xs">درجة الأداء</dt><dd class="font-bold text-green-700">{{ $task->performance_score }}%</dd></div>@endif
            </dl>
            @if($task->completion_notes)
            <div class="mt-4 p-4 rounded-xl bg-green-50 border border-green-100">
                <p class="text-xs font-bold text-green-800 mb-1">ملاحظات الإنجاز</p>
                <p class="text-sm text-green-900 font-tajawal">{{ $task->completion_notes }}</p>
            </div>
            @endif
        </div>

        @if($task->client && $isAssignee)
        <div class="bg-white rounded-2xl border p-4 flex flex-wrap gap-2 font-tajawal">
            <span class="text-xs font-bold text-gray-500 w-full mb-1">إجراءات سريعة</span>
            @if($phone)<a href="tel:{{ $phone }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold">اتصال</a>@endif
            @if($wa)<a href="{{ $wa }}" target="_blank" rel="noopener" class="px-4 py-2 rounded-xl bg-green-600 text-white text-xs font-bold">واتساب</a>@endif
            <a href="{{ route('crm.follow-ups.index') }}" class="px-4 py-2 rounded-xl border text-xs font-bold">جدولة متابعة</a>
            @if($task->sale_id)<a href="{{ route('crm.pipeline.show', $task->sale_id) }}" class="px-4 py-2 rounded-xl border text-xs font-bold">الصفقة</a>@endif
        </div>
        @endif

        <div class="bg-white rounded-2xl border overflow-hidden">
            <div class="px-5 py-3 border-b" style="{{ $headerStyle }}"><h3 class="font-bold font-tajawal">سجل التتبع</h3></div>
            <ul class="divide-y font-tajawal text-sm">
                @forelse($task->logs as $log)
                <li class="px-5 py-3">
                    <span class="font-semibold">{{ $log->action === 'transferred' ? 'تحويل' : $log->action }}</span>
                    @if($log->notes)<span class="text-gray-700"> — {{ $log->notes }}</span>@endif
                    @if($log->old_status && $log->new_status && $log->action !== 'transferred')<span class="text-gray-500"> {{ $log->old_status }} → {{ $log->new_status }}</span>@endif
                    <span class="text-gray-400 text-xs block">{{ $log->created_at->format('Y/m/d H:i') }} — {{ $log->user?->name ?? 'النظام' }}</span>
                </li>
                @empty
                <li class="p-6 text-center text-gray-400">لا سجل بعد</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="xl:col-span-4 space-y-4">
        <div class="bg-white rounded-2xl border p-5 font-tajawal space-y-3">
            <h3 class="font-bold text-sm">الإجراءات</h3>
            @if($isAssignee)
                @if($task->status === 'pending' && $task->requires_acceptance)
                <form method="POST" action="{{ route('crm.tasks.accept', $task) }}">@csrf<button class="w-full py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">قبول المهمة</button></form>
                @endif
                @if(in_array($task->status, ['pending','accepted','overdue']))
                <form method="POST" action="{{ route('crm.tasks.start', $task) }}">@csrf<button class="w-full py-2.5 rounded-xl border-2 text-sm font-bold" style="border-color:{{ $themeColor }};color:{{ $themeColor }}">بدء التنفيذ</button></form>
                @endif
                @if(in_array($task->status, ['in_progress','accepted','overdue','pending']))
                <form method="POST" action="{{ route('crm.tasks.complete', $task) }}" class="space-y-2">
                    @csrf
                    <textarea name="completion_notes" rows="3" class="w-full border-2 rounded-xl px-3 py-2 text-sm" placeholder="ملاحظات الإنجاز (إلزامية)..." required minlength="10"></textarea>
                    <button class="w-full py-2.5 rounded-xl bg-green-600 text-white text-sm font-bold">إكمال المهمة</button>
                </form>
                @endif
            @endif
            @if($canVerify && $task->status === 'completed')
            <form method="POST" action="{{ route('crm.tasks.verify', $task) }}">@csrf<button class="w-full py-2.5 rounded-xl bg-purple-600 text-white text-sm font-bold">تحقق المدير</button></form>
            @endif
            @if($canTransfer && ($assignableUsers ?? collect())->isNotEmpty() && !in_array($task->status, ['completed','verified','archived','cancelled']))
            <form method="POST" action="{{ route('crm.tasks.transfer', $task) }}" class="space-y-2 pt-2 border-t border-gray-100"
                  onsubmit="return confirm('تحويل هذه المهمة إلى المستخدم المحدد؟')">
                @csrf
                <label class="block text-xs font-bold text-gray-500">تحويل / سحب المهمة</label>
                <select name="assigned_to" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
                    @foreach($assignableUsers as $user)
                    @if((int) $user->id !== (int) $task->assigned_to)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endif
                    @endforeach
                </select>
                <button type="submit" class="w-full py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">تحويل المهمة</button>
            </form>
            @endif
            @if($canManage && !in_array($task->status, ['archived','cancelled']))
            <form method="POST" action="{{ route('crm.tasks.cancel', $task) }}" onsubmit="return confirm('إلغاء المهمة؟')">@csrf<button class="w-full py-2 text-xs text-red-600 border border-red-200 rounded-lg">إلغاء</button></form>
            @endif
        </div>
        @if($task->client)
        <div class="bg-white rounded-2xl border p-4 text-sm font-tajawal">
            <p class="text-xs text-gray-500">العميل</p>
            <a href="{{ $task->client->profileUrl() }}" class="font-bold" style="color:{{ $themeColor }}">{{ $task->client->name }}</a>
        </div>
        @endif
    </div>
</div>
<a href="{{ route('crm.tasks.index') }}" class="inline-block mt-6 text-sm font-tajawal" style="color:{{ $themeColor }}">← كل المهام</a>
@endsection
