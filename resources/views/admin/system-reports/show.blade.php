@extends('layouts.app')

@section('page-title', $meta['title'] ?? 'تقرير')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $columns = $payload['columns'] ?? [];
    $rows = $payload['rows'] ?? [];
    $exportUrl = route('admin.system-reports.export', $payload['report_key']);
    if ($supportsDateFilter && request('start_date')) {
        $exportUrl .= '?' . http_build_query(request()->only(['start_date', 'end_date']));
    }
@endphp
<div class="w-full font-tajawal">
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.system-reports.index') }}" class="text-sm font-medium mb-2 inline-flex items-center gap-1 hover:underline" style="color: {{ $themeColor }}">
                ← مركز التقارير
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $meta['title'] }}</h1>
            <p class="text-sm text-gray-600 mt-1">{{ $meta['description'] }}</p>
            <p class="text-xs text-gray-500 mt-1">آخر تحديث: {{ $payload['generated_at'] }} @if(!empty($payload['period_label'])) — {{ $payload['period_label'] }} @endif</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ $exportUrl }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md"
               style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                تصدير Excel
            </a>
        </div>
    </div>

    @if($supportsDateFilter)
    <form method="GET" class="mb-6 bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">من تاريخ</label>
            <input type="date" name="start_date" value="{{ request('start_date', $payload['filters']['start_date'] ?? '') }}"
                   class="rounded-xl border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">إلى تاريخ</label>
            <input type="date" name="end_date" value="{{ request('end_date', $payload['filters']['end_date'] ?? '') }}"
                   class="rounded-xl border-gray-300 text-sm">
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold" style="background: {{ $themeColor }}">تطبيق</button>
        <a href="{{ route('admin.system-reports.show', $payload['report_key']) }}" class="text-sm text-gray-500 hover:text-gray-700">مسح</a>
    </form>
    @endif

    @if(!empty($payload['summary']))
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        @foreach($payload['summary'] as $item)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">{{ $item['label'] }}</p>
            <p class="text-lg font-bold text-gray-900">{{ $item['value'] }}</p>
        </div>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-gray-100 flex justify-between items-center">
            <span class="text-sm font-semibold text-gray-700">البيانات التفصيلية</span>
            <span class="text-xs px-2.5 py-1 rounded-full" style="background: {{ $themeColor }}15; color: {{ $themeColor }}">{{ count($rows) }} سجل</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-right">
                <thead>
                    <tr style="background: {{ $themeColor }}; color: #fff;">
                        @foreach($columns as $col)
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">{{ $col['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                    <tr class="{{ $i % 2 ? 'bg-gray-50' : 'bg-white' }} border-b border-gray-100">
                        @foreach($columns as $col)
                        @php
                            $val = is_array($row) ? ($row[$col['key']] ?? '—') : '—';
                            if (($col['type'] ?? null) === 'money' && is_numeric($val)) {
                                $val = number_format((float) $val, 2);
                            }
                        @endphp
                        <td class="px-4 py-2.5 text-gray-800 max-w-xs truncate">{{ $val }}</td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ max(count($columns), 1) }}" class="px-4 py-12 text-center text-gray-500">لا توجد بيانات للفترة المحددة</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
