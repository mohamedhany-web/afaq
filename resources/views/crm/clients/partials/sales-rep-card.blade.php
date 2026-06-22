@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $repName = $client->assignedSalesRepName();
@endphp
@if($repName)
<div class="mb-6 rounded-2xl border-2 overflow-hidden shadow-sm font-tajawal"
     style="border-color: {{ $themeColor }}40; background: linear-gradient(135deg, {{ $themeColor }}12 0%, {{ $themeColor }}05 100%);">
    <div class="px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shrink-0"
                 style="background: {{ $themeColor }};">
                {{ mb_substr($repName, 0, 1) }}
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">السيلز المسؤول</p>
                @if($client->assignedEmployee?->user)
                <a href="{{ route('crm.team-members.show', $client->assignedEmployee->user) }}"
                   class="text-lg font-extrabold text-gray-900 hover:underline" style="color: {{ $themeColor }};">
                    {{ $repName }}
                </a>
                @else
                <p class="text-lg font-extrabold text-gray-900">{{ $repName }}</p>
                @endif
            </div>
        </div>
        @if(($assignableReps ?? collect())->isNotEmpty() && auth()->user()->can('transfer', $client))
        <form method="POST" action="{{ route(($clientsRoutePrefix ?? 'crm.clients') . '.transfer', $client) }}"
              class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-end min-w-[240px]"
              onsubmit="return confirm('تحويل هذا العميل والمهام المحددة إلى السيلز الجديد؟')">
            @csrf
            <div class="flex-1">
                <label class="block text-[10px] font-bold text-gray-500 mb-1">تحويل / سحب إلى سيلز</label>
                <select name="employee_id" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm bg-white">
                    @foreach($assignableReps as $rep)
                    @if($rep->employee?->id && (int) $rep->employee->id !== (int) $client->assigned_to)
                    <option value="{{ $rep->employee->id }}">{{ $rep->name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer sm:pb-2">
                <input type="checkbox" name="transfer_tasks" value="1" checked class="rounded border-gray-300" style="accent-color: {{ $themeColor }};">
                تحويل المهام المرتبطة
            </label>
            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold text-white whitespace-nowrap"
                    style="background: {{ $themeColor }};">تحويل</button>
        </form>
        @endif
    </div>
</div>
@elseif(($assignableReps ?? collect())->isNotEmpty() && auth()->user()->can('transfer', $client))
<div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 font-tajawal">
    <p class="text-sm text-amber-900 mb-3 font-semibold">العميل غير مُعيَّن لسيلز بعد — يمكن تعيينه أو سحبه لفريق المبيعات</p>
    <form method="POST" action="{{ route(($clientsRoutePrefix ?? 'crm.clients') . '.transfer', $client) }}" class="flex flex-wrap gap-2 items-end">
        @csrf
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">تعيين / سحب إلى سيلز</label>
            <select name="employee_id" required class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm min-w-[200px]">
                @foreach($assignableReps as $rep)
                <option value="{{ $rep->employee?->id }}">{{ $rep->name }}</option>
                @endforeach
            </select>
        </div>
        <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer pb-2">
            <input type="checkbox" name="transfer_tasks" value="1" checked class="rounded border-gray-300" style="accent-color: {{ $themeColor }};">
            تحويل المهام المرتبطة
        </label>
        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold text-white" style="background: {{ $themeColor }};">تعيين</button>
    </form>
</div>
@endif
