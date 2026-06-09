@extends('layouts.app')
@section('page-title', 'إدارة المصروفات')

@section('content')
@include('accounting.partials.context')
@include('crm.partials.page-header', array_filter([
    'title' => 'المصروفات',
    'subtitle' => 'تتبع وإدارة النفقات والمصروفات التشغيلية',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />',
    'actionUrl' => auth()->user()?->can('create-finance') ? route('expenses.create') : null,
    'actionLabel' => auth()->user()?->can('create-finance') ? 'مصروف جديد' : null,
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
]))
@include('accounting.partials.nav')

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي المصروفات', 'value' => $stats['total'], 'accent' => 'theme', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'إجمالي المبلغ', 'value' => $money($stats['total_amount']), 'accent' => 'purple', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'بانتظار الموافقة', 'value' => $stats['pending'], 'accent' => 'amber', 'compact' => true])
    @include('crm.partials.stat-card', ['label' => 'موافق عليها', 'value' => $stats['approved'], 'accent' => 'green', 'compact' => true])
</div>

@include('accounting.partials.filter-bar', [
    'action' => route('expenses.index'),
    'fields' => [
        ['name' => 'search', 'label' => 'بحث', 'placeholder' => 'رقم أو وصف المصروف...'],
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'class' => 'w-full lg:w-44', 'options' => ['' => 'الكل', 'pending' => 'معلق', 'approved' => 'موافق عليه', 'rejected' => 'مرفوض']],
    ],
])

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b flex justify-between items-center font-tajawal" style="{{ $headerStyle }}">
        <h2 class="font-bold">قائمة المصروفات</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium" style="background:{{ $themeColor }}15;color:{{ $themeColor }};">{{ $expenses->total() }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px] font-tajawal">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="p-4 text-right font-bold">رقم المصروف</th>
                    <th class="p-4 text-right font-bold">الوصف</th>
                    <th class="p-4 text-right font-bold">الفئة</th>
                    <th class="p-4 text-center font-bold">المبلغ</th>
                    <th class="p-4 text-center font-bold">التاريخ</th>
                    <th class="p-4 text-center font-bold">الحالة</th>
                    <th class="p-4 text-center font-bold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php
                    $statusColors = ['pending' => 'bg-amber-100 text-amber-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'];
                    $statusNames = ['pending' => 'معلق', 'approved' => 'موافق عليه', 'rejected' => 'مرفوض'];
                @endphp
                @forelse($expenses as $expense)
                <tr class="hover:bg-gray-50">
                    <td class="p-4">
                        <div class="font-bold text-gray-900">{{ $expense->expense_number }}</div>
                        <div class="text-xs text-gray-500">{{ $expense->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="p-4 text-gray-700">{{ Str::limit($expense->description, 50) }}</td>
                    <td class="p-4 text-gray-700">{{ $expense->expense_category }}</td>
                    <td class="p-4 text-center font-bold tabular-nums">{{ $money($expense->amount) }}</td>
                    <td class="p-4 text-center text-gray-500">{{ $expense->expense_date->format('Y/m/d') }}</td>
                    <td class="p-4 text-center">
                        <span class="text-xs font-bold px-2 py-1 rounded-lg {{ $statusColors[$expense->status] ?? 'bg-gray-100 text-gray-800' }}">{{ $statusNames[$expense->status] ?? $expense->status }}</span>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('expenses.show', $expense) }}" class="text-xs font-bold" style="color:{{ $themeColor }}">عرض</a>
                            <a href="{{ route('expenses.edit', $expense) }}" class="text-xs font-bold text-green-600">تعديل</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-10 text-center text-gray-500">لا توجد مصروفات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div class="px-5 py-4 border-t">{{ $expenses->links() }}</div>
    @endif
</div>
@endsection
