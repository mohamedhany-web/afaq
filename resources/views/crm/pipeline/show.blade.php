@extends('layouts.app')
@section('page-title', $sale->product_service)

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => $sale->product_service,
    'subtitle' => 'تفاصيل الصفقة — ' . ($sale->client?->name ?? ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />',
    'actionUrl' => route('crm.pipeline.edit', $sale),
    'actionLabel' => 'تعديل',
])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 space-y-4 text-sm">
        <h3 class="font-bold text-gray-900 font-tajawal border-b pb-2">بيانات الصفقة</h3>
        <p><span class="text-gray-500">العميل:</span> <a href="{{ route('crm.clients.show', $sale->client) }}" class="font-semibold" style="color: {{ $themeColor }};">{{ $sale->client?->name }}</a></p>
        <p><span class="text-gray-500">المشروع:</span> <span class="text-gray-900">{{ $sale->project?->name ?? '—' }}</span></p>
        <p><span class="text-gray-500">المرحلة:</span> <span class="px-2 py-1 rounded-lg text-xs font-medium" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">{{ $sale->stage }}</span></p>
        <p><span class="text-gray-500">القيمة:</span> <strong class="text-gray-900">{{ \App\Helpers\SettingsHelper::formatMoney($sale->estimated_value) }}</strong></p>
        <p><span class="text-gray-500">نوع الوحدة:</span> {{ $sale->unit_type ?? '—' }}</p>
        <p><span class="text-gray-500">معاينة:</span> {{ $sale->viewing_date?->format('Y-m-d') ?? '—' }}</p>
        <p><span class="text-gray-500">مندوب المبيعات:</span> {{ $sale->salesRep?->name }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h3 class="font-bold mb-4 text-gray-900 font-tajawal">تحديث المرحلة</h3>
        <form action="{{ route('crm.pipeline.update-stage', $sale) }}" method="POST" class="space-y-3" id="deal-stage-form">
            @csrf @method('PATCH')
            <div class="flex gap-2">
                <select name="stage" id="deal-stage-select" class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal">
                    @php $stageLabels = ['lead'=>'عميل محتمل','prospect'=>'مهتم','proposal'=>'عرض سعر','negotiation'=>'تفاوض','closed_won'=>'تم البيع','closed_lost'=>'خسارة']; @endphp
                    @foreach(['lead','prospect','proposal','negotiation','closed_won','closed_lost'] as $s)
                        <option value="{{ $s }}" @selected($sale->stage==$s)>{{ $stageLabels[$s] ?? $s }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-3 rounded-xl text-white font-semibold font-tajawal" style="background: {{ $themeColor }};">تحديث</button>
            </div>
            <div id="deal-lost-fields" class="hidden space-y-2 p-4 rounded-xl bg-red-50 border border-red-100">
                <label class="block text-xs font-bold text-red-700 font-tajawal">سبب الخسارة *</label>
                <select name="lost_reason" class="w-full border-2 border-red-200 rounded-xl px-4 py-2 text-sm font-tajawal">
                    <option value="">— اختر —</option>
                    @foreach(config('crm_intelligence.lost_reasons') as $key => $label)
                        <option value="{{ $key }}" @selected($sale->lost_reason==$key)>{{ $label }}</option>
                    @endforeach
                </select>
                <textarea name="lost_reason_notes" rows="2" class="w-full border-2 border-red-200 rounded-xl px-4 py-2 text-sm font-tajawal" placeholder="تفاصيل إضافية">{{ $sale->lost_reason_notes }}</textarea>
            </div>
        </form>
        @if($sale->lost_reason)
            <p class="mt-3 text-sm text-red-600 font-tajawal">سبب الخسارة: {{ config('crm_intelligence.lost_reasons')[$sale->lost_reason] ?? $sale->lost_reason }}</p>
        @endif
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sel = document.getElementById('deal-stage-select');
            const box = document.getElementById('deal-lost-fields');
            function toggle() { box?.classList.toggle('hidden', sel?.value !== 'closed_lost'); }
            sel?.addEventListener('change', toggle);
            toggle();
        });
        </script>
        @if($sale->notes)<p class="mt-4 text-sm text-gray-600 p-4 bg-gray-50 rounded-xl">{{ $sale->notes }}</p>@endif
    </div>
</div>

@if($sale->stage === 'closed_won' || $sale->company_commission_amount)
@php $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v); @endphp
<div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b font-bold font-tajawal flex justify-between" style="background:linear-gradient(135deg,{{ $themeColor }}08 0%,{{ $themeColor }}03 100%);">
        <span>عمولة الوكيل (Freelance Scheme)</span>
        @if($sale->commission_collected)<span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800 font-semibold">محصّلة</span>@else<span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">معلّقة</span>@endif
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm font-tajawal">
        <div><span class="text-xs text-gray-500 block">نوع العملية</span><strong>{{ $sale->transactionTypeLabel() }}</strong></div>
        <div><span class="text-xs text-gray-500 block">عمولة الشركة</span><strong>{{ $money($sale->company_commission_amount ?? 0) }}</strong></div>
        <div><span class="text-xs text-gray-500 block">حصة الوكلاء</span><strong>{{ $money($commissionPreview['agent_total'] ?? 0) }}</strong></div>
        <div><span class="text-xs text-gray-500 block">باقي الشركة</span><strong>{{ $money($commissionPreview['company_retained'] ?? 0) }}</strong></div>
    </div>
    @if(!empty($commissionPreview['splits']))
    <div class="border-t divide-y">
        @foreach($commissionPreview['splits'] as $split)
        @php $u = \App\Models\User::find($split['user_id']); @endphp
        <div class="px-5 py-3 flex justify-between text-sm font-tajawal">
            <span>{{ $u?->name ?? 'وكيل' }} <span class="text-xs text-gray-400">({{ $split['agent_role'] }})</span></span>
            <span class="font-bold">{{ $money($split['amount']) }} <span class="text-xs text-gray-500">({{ $split['percent'] }}%)</span></span>
        </div>
        @endforeach
    </div>
    @endif
    @if(!$sale->commission_collected && auth()->user()?->hasRole(['super_admin','admin','sales_manager']))
    <div class="p-4 border-t">
        <form method="POST" action="{{ route('crm.pipeline.commission-collected', $sale) }}">@csrf
            <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-semibold font-tajawal" style="background:{{ $themeColor }}">تسجيل تحصيل عمولة الشركة</button>
        </form>
    </div>
    @endif
</div>
@endif
@endsection
