@extends('layouts.app')

@section('page-title', 'تقارير النظام')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
<div class="w-full font-tajawal">
    @include('crm.partials.page-header', [
        'title' => 'تقارير النظام الشاملة',
        'subtitle' => 'استعرض التقارير التفصيلية وصدّرها إلى Excel بتنسيق احترافي',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>',
    ])

    <div class="mb-6 rounded-2xl border p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
         style="background: {{ $themeColor }}08; border-color: {{ $themeColor }}25;">
        <p class="text-sm text-gray-700">تشمل تقارير CRM والموارد البشرية والمشاريع العقارية والتعويضات — مع إمكانية التصفية بالتاريخ وتصدير ملف Excel.</p>
        <a href="{{ route('reports.index') }}" class="text-sm font-semibold whitespace-nowrap" style="color: {{ $themeColor }}">التقارير التقليدية (HR) ←</a>
    </div>

    @foreach($grouped as $catKey => $group)
    <div class="mb-10">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="w-1.5 h-6 rounded-full" style="background: {{ $themeColor }}"></span>
            {{ $group['label'] }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($group['reports'] as $key => $report)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 flex-1">
                    <h3 class="text-base font-bold text-gray-900 mb-1">{{ $report['title'] }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $report['description'] }}</p>
                    @if($report['supports_date_filter'] ?? false)
                    <span class="inline-block mt-3 text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">فلترة بالتاريخ</span>
                    @endif
                </div>
                <div class="p-4 bg-gray-50 flex gap-2">
                    <a href="{{ route('admin.system-reports.show', $key) }}"
                       class="flex-1 text-center py-2.5 rounded-xl text-white text-sm font-semibold"
                       style="background: {{ $themeColor }}">عرض</a>
                    <a href="{{ route('admin.system-reports.export', $key) }}"
                       class="px-4 py-2.5 rounded-xl border-2 text-sm font-semibold hover:bg-white transition-colors"
                       style="border-color: {{ $themeColor }}; color: {{ $themeColor }}"
                       title="تصدير Excel">
                        Excel
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
