@extends('layouts.app')
@section('page-title', ($plan ?? null) ? 'تعديل خطة تسويق' : 'خطة تسويق شهرية')

@section('content')
@php
    $plan = $plan ?? null;
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $months = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
@endphp

@include('crm.partials.page-header', [
    'title' => ($plan ?? null) ? 'تعديل خطة التسويق' : 'خطة تسويق شهرية جديدة',
    'subtitle' => 'توصيف الخطة، الأهداف، وربطها بحملة إن وُجدت',
    'actionUrl' => route('marketing.plans.index'),
    'actionLabel' => 'كل الخطط',
])

<form method="POST" action="{{ ($plan ?? null) ? route('marketing.plans.update', $plan) : route('marketing.plans.store') }}" class="font-tajawal space-y-6">
    @csrf
    @if($plan ?? null) @method('PUT') @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold" style="{{ $headerStyle }}">بيانات الخطة</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="{{ $label }}">عنوان الخطة *</label>
                <input name="title" required class="{{ $input }}" value="{{ old('title', optional($plan)->title) }}" placeholder="مثال: خطة تسويق يونيو 2026">
            </div>
            <div>
                <label class="{{ $label }}">الشهر *</label>
                <select name="month" class="{{ $input }}" required>
                    @foreach($months as $m => $name)
                    <option value="{{ $m }}" @selected(old('month', optional($plan)->month ?? $defaultMonth) == $m)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">السنة *</label>
                <input type="number" name="year" min="2020" max="2100" required class="{{ $input }}" value="{{ old('year', optional($plan)->year ?? $defaultYear) }}">
            </div>
            <div>
                <label class="{{ $label }}">الحملة المرتبطة</label>
                <select name="campaign_id" class="{{ $input }}">
                    <option value="">— اختياري —</option>
                    @foreach($campaigns as $c)
                    <option value="{{ $c->id }}" @selected(old('campaign_id', optional($plan)->campaign_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">الحالة</label>
                <select name="status" class="{{ $input }}">
                    @foreach($statuses as $k => $v)
                    <option value="{{ $k }}" @selected(old('status', optional($plan)->status ?? 'draft') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="{{ $label }}">توصيف الخطة (Marketing Plan) *</label>
                <textarea name="description" rows="4" required class="{{ $input }}" placeholder="وصف شامل لاستراتيجية التسويق لهذا الشهر...">{{ old('description', optional($plan)->description) }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="{{ $label }}">الأهداف والمؤشرات المستهدفة</label>
                <textarea name="objectives" rows="3" class="{{ $input }}" placeholder="عدد Leads، منشورات، فعاليات، ميزانية...">{{ old('objectives', optional($plan)->objectives) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('marketing.plans.index') }}" class="px-5 py-2.5 rounded-xl border text-sm font-semibold text-gray-600">إلغاء</a>
        <button type="submit" class="px-8 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:linear-gradient(135deg,{{ $themeColor }} 0%,{{ $themeColor }}dd 100%)">
            {{ ($plan ?? null) ? 'حفظ التعديلات' : 'إنشاء الخطة' }}
        </button>
    </div>
</form>
@endsection
