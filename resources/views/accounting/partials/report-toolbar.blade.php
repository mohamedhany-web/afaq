@php
    $filterType = $filterType ?? 'date';
    $inputClass = 'border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-tajawal min-w-[160px]';
@endphp
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6 no-print font-tajawal">
    <form method="GET" class="flex flex-col lg:flex-row flex-wrap items-stretch lg:items-end gap-3">
        @if($filterType === 'range')
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">من تاريخ</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="{{ $inputClass }}" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">إلى تاريخ</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="{{ $inputClass }}" required>
        </div>
        @else
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">حتى تاريخ</label>
            <input type="date" name="date" value="{{ $date }}" class="{{ $inputClass }}" required>
        </div>
        @endif
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            تحديث التقرير
        </button>
        <button type="button" onclick="printReport()"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            طباعة
        </button>
    </form>
</div>
