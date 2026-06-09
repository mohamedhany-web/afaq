@extends('layouts.app')

@php
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';
    $reportDate = \Carbon\Carbon::parse($date);
    $isBalanced = abs($totalDebit - $totalCredit) < 0.01;
    $typeLabels = [
        'asset' => ['الأصول', 'bg-green-100 text-green-800'],
        'liability' => ['الخصوم', 'bg-amber-100 text-amber-800'],
        'equity' => ['حقوق الملكية', 'bg-blue-100 text-blue-800'],
        'revenue' => ['الإيرادات', 'bg-green-100 text-green-800'],
        'expense' => ['المصروفات', 'bg-red-100 text-red-800'],
    ];
    $accountsByType = $accounts->groupBy('type');
@endphp

@section('page-title', 'ميزان المراجعة')

@section('content')
@include('accounting.partials.report-header', [
    'title' => 'ميزان المراجعة',
    'subtitle' => 'حتى تاريخ ' . $reportDate->format('Y/m/d'),
])

@include('accounting.partials.report-toolbar', ['filterType' => 'date', 'date' => $date])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 no-print">
    @include('crm.partials.stat-card', ['label' => 'إجمالي المدين', 'value' => $money($totalDebit), 'accent' => 'green', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'إجمالي الدائن', 'value' => $money($totalCredit), 'accent' => 'blue', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'عدد الحسابات', 'value' => $accounts->count(), 'accent' => 'purple', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'التوازن', 'value' => $isBalanced ? 'متوازن' : 'غير متوازن', 'accent' => $isBalanced ? 'green' : 'red', 'compact' => true])
</div>

<div id="report-document" class="font-tajawal">
    <div class="report-print-header text-center mb-6 pb-4 border-b-2 border-gray-900">
        <h2 class="text-xl font-bold">@include('accounting.partials.company-name')</h2>
        <h3 class="text-lg font-bold mt-3">ميزان المراجعة</h3>
        <p class="text-sm text-gray-700">حتى تاريخ: {{ $reportDate->format('Y/m/d') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 no-print">
        <div class="px-6 py-5 text-white text-center" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}cc 100%);">
            <h2 class="text-xl font-bold">@include('accounting.partials.company-name')</h2>
            <h3 class="text-base font-semibold mt-1 opacity-95">ميزان المراجعة</h3>
            <p class="text-sm opacity-90 mt-1">حتى تاريخ: {{ $reportDate->format('Y/m/d') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">أرصدة الحسابات</div>
        @if($accounts->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="p-4 text-right font-bold">رمز الحساب</th>
                        <th class="p-4 text-right font-bold">اسم الحساب</th>
                        <th class="p-4 text-center font-bold">النوع</th>
                        <th class="p-4 text-center font-bold">مدين</th>
                        <th class="p-4 text-center font-bold">دائن</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($accounts as $account)
                    <tr class="hover:bg-gray-50/60">
                        <td class="p-4 font-medium text-gray-900 tabular-nums">{{ $account->code }}</td>
                        <td class="p-4 text-gray-800">
                            @if($account->parent_id)<span class="text-gray-400 ml-1">↳</span>@endif
                            {{ $account->name }}
                        </td>
                        <td class="p-4 text-center">
                            <span class="text-xs font-bold px-2 py-1 rounded-lg {{ $typeLabels[$account->type][1] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $account->type_in_arabic ?? ($typeLabels[$account->type][0] ?? $account->type) }}
                            </span>
                        </td>
                        <td class="p-4 text-center tabular-nums font-semibold {{ $account->debit_balance > 0 ? 'text-green-600' : 'text-gray-300' }}">
                            {{ $account->debit_balance > 0 ? $money($account->debit_balance) : '—' }}
                        </td>
                        <td class="p-4 text-center tabular-nums font-semibold {{ $account->credit_balance > 0 ? 'text-blue-600' : 'text-gray-300' }}">
                            {{ $account->credit_balance > 0 ? $money($account->credit_balance) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="3" class="p-4 text-right font-bold text-gray-900">الإجمالي</td>
                        <td class="p-4 text-center font-bold text-green-700 tabular-nums">{{ $money($totalDebit) }}</td>
                        <td class="p-4 text-center font-bold text-blue-700 tabular-nums">{{ $money($totalCredit) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="p-12 text-center text-gray-500 text-sm">لا توجد حسابات نشطة.</div>
        @endif
    </div>

    <div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 p-6 text-center">
        @if($isBalanced)
        <div class="inline-flex items-center gap-2 text-green-700 font-bold text-lg">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            ميزان المراجعة متوازن
        </div>
        @else
        <div class="inline-flex items-center gap-2 text-red-700 font-bold text-lg">ميزان المراجعة غير متوازن</div>
        <p class="text-sm text-gray-600 mt-2">الفرق: {{ $money(abs($totalDebit - $totalCredit)) }}</p>
        @endif
    </div>

    @if($accountsByType->isNotEmpty())
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mt-6 no-print">
        @foreach($typeLabels as $type => $meta)
            @if(isset($accountsByType[$type]))
            @include('crm.partials.stat-card', [
                'label' => $meta[0],
                'value' => $money($accountsByType[$type]->sum('balance')),
                'accent' => match($type) { 'asset','revenue' => 'green', 'liability' => 'amber', 'equity' => 'blue', 'expense' => 'red', default => 'theme' },
                'compact' => true,
                'footer' => '<span class="text-gray-500">' . $accountsByType[$type]->count() . ' حساب</span>',
            ])
            @endif
        @endforeach
    </div>
    @endif
</div>

@include('accounting.partials.report-styles')
@endsection
