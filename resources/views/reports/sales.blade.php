@extends('layouts.app')

@section('page-title', 'تقرير المبيعات')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $stageLabels = [
        'lead' => 'عميل محتمل',
        'prospect' => 'مهتم',
        'proposal' => 'عرض سعر',
        'negotiation' => 'تفاوض',
        'closed_won' => 'تم البيع',
        'closed_lost' => 'خسارة',
    ];
@endphp
<div class="w-full font-tajawal">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">تقرير المبيعات</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $start_date }} — {{ $end_date }}</p>
                </div>
            </div>
            <a href="{{ route('reports.sales.print', request()->query()) }}" target="_blank" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm" style="background: {{ $themeColor }}">
                طباعة التقرير
            </a>
        </div>

        <form method="GET" class="flex flex-wrap gap-3 items-end bg-white rounded-xl border border-gray-200 p-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">من</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">إلى</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold" style="background: {{ $themeColor }}">تطبيق</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الصفقات</p>
            <p class="text-3xl font-bold text-gray-900">{{ $summary['total_sales'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-600 mb-1">إجمالي القيمة</p>
            <p class="text-2xl font-bold text-green-600">{{ $money($summary['total_amount']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-600 mb-1">متوسط الصفقة</p>
            <p class="text-2xl font-bold text-gray-900">{{ $money($summary['average_sale'] ?? 0) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ المتوقع</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">العميل</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المشروع</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المندوب</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الصفقة</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">القيمة</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المرحلة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-900">{{ $sale->expected_close_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @include('crm.partials.entity-link', ['type' => 'client', 'entity' => $sale->client])
                        </td>
                        <td class="px-4 py-3">
                            @include('crm.partials.entity-link', ['type' => 'project', 'entity' => $sale->project, 'linkClass' => 'hover:underline'])
                        </td>
                        <td class="px-4 py-3">
                            @include('crm.partials.entity-link', ['type' => 'rep', 'entity' => $sale->salesRep, 'linkClass' => 'hover:underline'])
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('crm.pipeline.show', $sale) }}" class="hover:underline" style="color: {{ $themeColor }}">{{ $sale->product_service }}</a>
                        </td>
                        <td class="px-4 py-3 font-bold text-green-600">{{ $money($sale->amount) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $stageLabels[$sale->stage] ?? $sale->stage }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">لا توجد صفقات في هذه الفترة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
