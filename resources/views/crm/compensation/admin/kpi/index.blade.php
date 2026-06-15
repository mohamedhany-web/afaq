@extends('layouts.app')
@section('page-title', 'قوالب KPI')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $roleLabels = config('compensation.target_role_labels', []);
    $periodLabels = config('compensation.evaluation_period_labels', []);
    $repTemplates = $templates->where('target_role', 'rep');
    $mgrTemplates = $templates->where('target_role', 'manager');
@endphp

@include('crm.partials.page-header', [
    'title' => 'قوالب مؤشرات الأداء',
    'subtitle' => 'إنشاء وتعديل KPI — تطبيق على الجميع أو موظف محدد',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
    'actionUrl' => route('crm.compensation.kpi.create'),
    'actionLabel' => 'قالب جديد',
])

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-800 text-sm font-tajawal border border-green-200">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 max-w-lg">
    @include('crm.partials.stat-card', ['label' => 'قوالب المندوبين', 'value' => $repTemplates->count(), 'compact' => true, 'accent' => 'blue', 'href' => route('crm.compensation.kpi.index') . '#page-data', 'linkLabel' => 'عرض القوالب'])
    @include('crm.partials.stat-card', ['label' => 'قوالب المديرين', 'value' => $mgrTemplates->count(), 'compact' => true, 'accent' => 'purple', 'href' => route('crm.compensation.kpi.index') . '#page-data', 'linkLabel' => 'عرض القوالب'])
</div>

<div class="flex flex-wrap gap-2 mb-6 font-tajawal text-sm">
    <a href="{{ route('crm.compensation.kpi.create', ['role' => 'rep']) }}"
       class="px-4 py-2 rounded-xl border-2 font-semibold hover:shadow-sm transition-all"
       style="border-color:{{ $themeColor }}40;color:{{ $themeColor }}">+ قالب مندوب</a>
    <a href="{{ route('crm.compensation.kpi.create', ['role' => 'manager']) }}"
       class="px-4 py-2 rounded-xl border-2 border-gray-200 font-semibold hover:bg-gray-50">+ قالب مدير</a>
    <a href="{{ route('crm.compensation.profiles.index') }}" class="px-4 py-2 rounded-xl text-gray-600 border border-gray-200 hover:bg-gray-50 mr-auto">ربط يدوي بالموظفين ←</a>
</div>

@foreach([['label' => 'مندوبي المبيعات', 'items' => $repTemplates], ['label' => 'مديرو المبيعات', 'items' => $mgrTemplates]] as $section)
    @if($section['items']->isNotEmpty())
    <div class="mb-8">
        <h2 class="font-bold text-gray-800 mb-3 font-tajawal">{{ $section['label'] }}</h2>
        <div class="space-y-4">
            @foreach($section['items'] as $tpl)
            @php $assigned = $tpl->employee_profiles_count; @endphp
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap justify-between gap-3 items-start" style="{{ $headerStyle }}">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-lg text-gray-900">{{ $tpl->name }}</h3>
                            @if($tpl->is_active)
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-bold">نشط</span>
                            @else
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-bold">موقوف</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $roleLabels[$tpl->target_role] ?? $tpl->target_role }}
                            · {{ $periodLabels[$tpl->evaluation_period] ?? $tpl->evaluation_period }}
                            · {{ $tpl->items_count }} مؤشر
                            · <strong>{{ $assigned }}</strong> موظف
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('crm.compensation.kpi.edit', $tpl) }}"
                           class="px-4 py-2 rounded-xl text-white text-xs font-bold shadow-sm"
                           style="background:linear-gradient(135deg,{{ $themeColor }},{{ $themeColor }}dd)">تعديل</a>
                        <button type="button"
                                onclick="document.getElementById('assign-{{ $tpl->id }}').classList.toggle('hidden')"
                                class="px-4 py-2 rounded-xl border-2 text-xs font-bold hover:bg-gray-50"
                                style="border-color:{{ $themeColor }}40;color:{{ $themeColor }}">
                            تطبيق على الموظفين
                        </button>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                    @foreach($tpl->items as $item)
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-3 text-center">
                        <p class="text-xs font-bold text-gray-800 leading-snug">{{ $item->name }}</p>
                        <p class="text-[10px] text-gray-500 mt-1 tabular-nums">{{ $item->weight }}% · هدف {{ number_format($item->target_value, 0) }}</p>
                    </div>
                    @endforeach
                </div>
                <div id="assign-{{ $tpl->id }}" class="hidden border-t border-gray-100 p-5 bg-gray-50/50">
                    <form method="POST" action="{{ route('crm.compensation.kpi.assign', $tpl) }}" class="font-tajawal text-sm space-y-3 max-w-xl">
                        @csrf
                        <p class="font-bold text-gray-700">تطبيق سريع بدون تعديل المؤشرات</p>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="apply_assignment" value="all_role" checked class="rounded-full">
                            <span>جميع {{ $tpl->target_role === 'manager' ? 'مديري' : 'مندوبي' }} المبيعات</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="apply_assignment" value="selected" class="rounded-full" onchange="document.getElementById('emps-{{ $tpl->id }}').classList.toggle('hidden', !this.checked)">
                            <span>موظفون محددون</span>
                        </label>
                        <div id="emps-{{ $tpl->id }}" class="hidden grid grid-cols-2 gap-1 max-h-32 overflow-y-auto border rounded-lg p-2 bg-white">
                            @foreach($employees as $emp)
                            <label class="flex items-center gap-1 text-xs">
                                <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="rounded">
                                {{ $emp->name }}
                            </label>
                            @endforeach
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-lg text-white text-xs font-bold" style="background:{{ $themeColor }}">تطبيق</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endforeach

@if($templates->isEmpty())
<div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center font-tajawal">
    <p class="text-gray-500 mb-4">لا توجد قوالب KPI بعد</p>
    <a href="{{ route('crm.compensation.kpi.create') }}" class="inline-flex px-6 py-3 rounded-xl text-white font-semibold text-sm"
       style="background:{{ $themeColor }}">إنشاء أول قالب</a>
</div>
@endif

<a href="{{ route('crm.compensation.dashboard') }}" class="inline-block mt-4 text-sm font-tajawal" style="color:{{ $themeColor }}">← لوحة التعويضات</a>
@endsection
