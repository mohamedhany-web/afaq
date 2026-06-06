@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <h2 class="font-bold text-gray-900 font-tajawal">{{ $tableTitle ?? 'التقارير' }}</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">{{ $reports->total() }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-right p-4 text-xs font-bold text-gray-600 font-tajawal">التاريخ</th>
                    @if($showEmployeeColumn ?? false)
                    <th class="text-right p-4 text-xs font-bold text-gray-600 font-tajawal">الموظف</th>
                    @endif
                    <th class="text-right p-4 text-xs font-bold text-gray-600 font-tajawal">الحالة</th>
                    <th class="text-right p-4 text-xs font-bold text-gray-600 font-tajawal">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 text-sm font-tajawal">{{ $report->report_date->format('Y-m-d') }}</td>
                    @if($showEmployeeColumn ?? false)
                    <td class="p-4 text-sm font-tajawal font-medium text-gray-900">{{ $report->author?->name }}</td>
                    @endif
                    <td class="p-4">
                        @if($report->isSubmitted())
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 font-tajawal">مرفوع</span>
                        @else
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 font-tajawal">مسودة</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <a href="{{ route('crm.daily-reports.show', $report) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal text-white hover:opacity-90"
                           style="background: {{ $themeColor }};">عرض</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ ($showEmployeeColumn ?? false) ? 4 : 3 }}" class="p-8 text-center text-gray-500 text-sm font-tajawal">{{ $emptyMessage ?? 'لا توجد تقارير.' }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())
    <div class="p-4 sm:p-5 border-t border-gray-200">{{ $reports->links() }}</div>
    @endif
</div>
