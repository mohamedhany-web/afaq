@extends('layouts.app')
@section('page-title', $contract->user?->name)

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $sectionBg = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $statuses = config('freelance_agents.contract_statuses');
@endphp

@include('crm.partials.page-header', [
    'title' => $contract->user?->name,
    'subtitle' => 'عقد وكيل مستقل — ' . ($statuses[$contract->status] ?? $contract->status),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('crm.freelance-agents.edit', $contract),
    'actionLabel' => 'تعديل العقد',
])

<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('crm.freelance-agents.contract-print', $contract) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold font-tajawal">طباعة مسودة العقد</a>
    <a href="{{ route('crm.freelance-agents.scheme') }}" class="px-4 py-2.5 rounded-xl border-2 text-sm font-semibold font-tajawal" style="border-color:{{ $themeColor }}40;color:{{ $themeColor }}">جدول العمولات</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'التارجت الربع سنوي', 'value' => $contract->quarterly_target_deals ? $contract->quarterly_target_deals.' صفقة' : ($contract->quarterly_target_amount ? $money($contract->quarterly_target_amount) : '—'), 'accent' => 'theme', 'compact' => true, 'href' => route('crm.freelance-agents.index'), 'linkLabel' => 'عرض العقود'])
    @include('crm.partials.stat-card', ['label' => 'حالة التارجت', 'value' => $metTarget ? 'محقق' : 'غير محقق', 'accent' => $metTarget ? 'green' : 'amber', 'compact' => true, 'href' => route('crm.freelance-agents.index'), 'linkLabel' => 'عرض العقود'])
    @include('crm.partials.stat-card', ['label' => 'بداية العقد', 'value' => $contract->start_date?->format('Y/m/d'), 'accent' => 'blue', 'compact' => true, 'href' => route('crm.freelance-agents.index'), 'linkLabel' => 'عرض العقود'])
    @include('crm.partials.stat-card', ['label' => 'نهاية العقد', 'value' => $contract->end_date?->format('Y/m/d') ?? 'مفتوح', 'accent' => 'purple', 'compact' => true, 'href' => route('crm.freelance-agents.index'), 'linkLabel' => 'عرض العقود'])
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $sectionBg }}">بيانات العقد</div>
        <dl class="p-5 sm:p-6 space-y-3 text-sm font-tajawal">
            <div><dt class="text-xs font-bold text-gray-500">رقم العقد</dt><dd>{{ $contract->contract_number ?? '—' }}</dd></div>
            <div><dt class="text-xs font-bold text-gray-500">الرقم القومي</dt><dd dir="ltr">{{ $contract->national_id ?? '—' }}</dd></div>
            <div><dt class="text-xs font-bold text-gray-500">الهاتف</dt><dd dir="ltr">{{ $contract->phone ?? '—' }}</dd></div>
            <div><dt class="text-xs font-bold text-gray-500">العنوان</dt><dd>{{ $contract->address ?? '—' }}</dd></div>
            <div><dt class="text-xs font-bold text-gray-500">موقّع عن الشركة</dt><dd>{{ $contract->company_signatory_name ?? '—' }} @if($contract->company_signatory_title)({{ $contract->company_signatory_title }})@endif</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $sectionBg }}">آخر عمولات محسوبة</div>
        <div class="divide-y">
            @forelse($recentSplits as $split)
            <div class="px-5 py-3 text-sm font-tajawal">
                <div class="font-semibold">{{ $split->sale?->product_service }}</div>
                <div class="text-xs text-gray-500">{{ $split->agent_role }} — {{ $money($split->amount) }} ({{ $split->percent_of_company }}%)</div>
            </div>
            @empty
            <div class="p-6 text-gray-400 text-sm text-center">لا عمولات بعد</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
