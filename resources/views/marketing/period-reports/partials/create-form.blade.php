@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, #7c3aed08 0%, #7c3aed03 100%);">
        إنشاء تقرير {{ config('marketing_reports.period_types.'.$periodType, '') }}
    </div>
    <form action="{{ route('marketing.reports.generate') }}" method="POST" class="p-5 sm:p-6 flex flex-col sm:flex-row gap-4 items-end">
        @csrf
        <input type="hidden" name="period_type" value="{{ $periodType }}">
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">
                @if($periodType === 'daily') تاريخ اليوم @elseif($periodType === 'weekly') تاريخ ضمن الأسبوع @else شهر التقرير @endif
            </label>
            <input type="date" name="anchor_date" value="{{ old('anchor_date', today()->toDateString()) }}" max="{{ today()->toDateString() }}" required
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <button type="submit" class="w-full sm:w-auto px-8 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                style="background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);">
            إنشاء التقرير
        </button>
    </form>
    <p class="px-5 pb-5 text-xs text-gray-500 font-tajawal">يُملأ تلقائياً من بيانات الحملات والمهام والـ Leads. أكمل الملاحظات ثم ارفع التقرير.</p>
</div>
