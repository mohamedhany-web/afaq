@extends('layouts.app')
@section('page-title', 'سجل حذف العملاء')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'سجل عمليات حذف العملاء',
    'subtitle' => 'تقرير بالحذف الفردي والجماعي — مع السبب والمستخدم والتاريخ',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />',
])

<div class="bg-white rounded-2xl border p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end font-tajawal text-sm">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">من تاريخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded-xl px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded-xl px-3 py-2">
        </div>
        <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-semibold" style="background: {{ $themeColor }};">تصفية</button>
        <a href="{{ route('crm.clients.deletions.index') }}" class="px-4 py-2 rounded-xl border text-sm">إعادة ضبط</a>
    </form>
</div>

<div class="bg-white rounded-2xl border overflow-hidden">
    <table class="w-full text-sm font-tajawal">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="p-4 text-right">#</th>
                <th class="p-4 text-right">التاريخ والوقت</th>
                <th class="p-4 text-right">عدد العملاء</th>
                <th class="p-4 text-right">المستخدم</th>
                <th class="p-4 text-right">سبب الحذف</th>
                <th class="p-4 text-right"></th>
            </tr>
        </thead>
        <tbody>
        @forelse($batches as $batch)
        <tr class="border-t hover:bg-gray-50">
            <td class="p-4">{{ $batch->id }}</td>
            <td class="p-4">{{ $batch->created_at->format('Y/m/d H:i') }}</td>
            <td class="p-4">
                <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $batch->isBulk() ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $batch->clients_count }} {{ $batch->isBulk() ? '(حذف جماعي)' : '(فردي)' }}
                </span>
            </td>
            <td class="p-4">{{ $batch->user?->name ?? '—' }}</td>
            <td class="p-4 max-w-xs truncate" title="{{ $batch->delete_reason }}">{{ \Illuminate\Support\Str::limit($batch->delete_reason, 80) }}</td>
            <td class="p-4">
                <a href="{{ route('crm.clients.deletions.show', $batch) }}" class="text-xs font-bold" style="color: {{ $themeColor }};">التفاصيل</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="p-10 text-center text-gray-400">لا توجد عمليات حذف مسجّلة.</td></tr>
        @endforelse
        </tbody>
    </table>
    @if($batches->hasPages())
    <div class="p-4 border-t">{{ $batches->links() }}</div>
    @endif
</div>
@endsection
