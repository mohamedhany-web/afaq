@extends('layouts.app')
@section('page-title', $team->name)

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

@include('crm.partials.page-header', [
    'title' => $team->name,
    'subtitle' => 'مدير الفريق: ' . ($team->manager?->name ?? '—') . ($team->is_active ? '' : ' · غير نشط'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
    'actionUrl' => $canManage ? route('crm.teams.edit', $team) : null,
    'actionLabel' => 'تعديل الفريق',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'الأعضاء', 'value' => $team->members_count, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />', 'href' => '#team-members', 'linkLabel' => 'عرض الأعضاء'])
    @include('crm.partials.stat-card', ['label' => 'الصفقات', 'value' => $team->sales_count, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />', 'href' => '#team-members', 'linkLabel' => 'عرض الأعضاء'])
    @include('crm.partials.stat-card', ['label' => 'قيمة المسار', 'value' => $money($pipelineValue), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />', 'href' => '#team-members', 'linkLabel' => 'عرض الأعضاء'])
    @include('crm.partials.stat-card', ['label' => 'صفقات مكتملة', 'value' => (int) ($salesStats->get('closed_won')?->cnt ?? 0), 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => '#team-members', 'linkLabel' => 'عرض الأعضاء'])
</div>

@if($team->description)
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 mb-6">
    <p class="text-sm text-gray-600 font-tajawal leading-relaxed">{{ $team->description }}</p>
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6">
    <div class="xl:col-span-5 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200" style="{{ $headerStyle }}">
            <h3 class="font-bold text-gray-900 font-tajawal">أعضاء الفريق</h3>
        </div>
        <ul class="divide-y divide-gray-100">
            <li class="px-5 py-3 flex items-center gap-3 bg-gray-50/50">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                     style="background:{{ $themeColor }}">{{ mb_substr($team->manager?->name ?? '?', 0, 1) }}</div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 text-sm font-tajawal">{{ $team->manager?->name }}</p>
                    <p class="text-xs text-gray-500 font-tajawal">مدير المبيعات</p>
                </div>
            </li>
            @forelse($team->members as $member)
            <li class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50">
                <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-sm font-bold shrink-0">
                    {{ mb_substr($member->name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-800 text-sm font-tajawal">{{ $member->name }}</p>
                    <p class="text-xs text-gray-400 font-tajawal">{{ $member->employee?->job_title ?? 'مندوب مبيعات' }}</p>
                </div>
                <a href="{{ route('crm.team-members.show', $member) }}" class="text-xs font-semibold font-tajawal shrink-0" style="color:{{ $themeColor }}">الملف</a>
            </li>
            @empty
            <li class="px-5 py-8 text-center text-gray-400 text-sm font-tajawal">لا يوجد أعضاء بعد</li>
            @endforelse
        </ul>
    </div>

    <div class="xl:col-span-7 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center" style="{{ $headerStyle }}">
            <h3 class="font-bold text-gray-900 font-tajawal">صفقات الفريق</h3>
            <a href="{{ route('crm.pipeline.index') }}" class="text-xs font-semibold font-tajawal" style="color:{{ $themeColor }}">المسار</a>
        </div>
        @if($salesStats->isNotEmpty())
        <div class="px-5 py-3 flex flex-wrap gap-2 border-b border-gray-50">
            @foreach($salesStats as $stage => $row)
            <span class="text-[10px] px-2 py-1 rounded-lg bg-gray-100 text-gray-700 font-tajawal">
                {{ $stageLabels[$stage] ?? $stage }}: <strong>{{ $row->cnt }}</strong>
            </span>
            @endforeach
        </div>
        @endif
        <div class="overflow-x-auto max-h-96 overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs sticky top-0">
                    <tr>
                        <th class="text-right p-3 font-tajawal">العميل</th>
                        <th class="text-right p-3 font-tajawal">المندوب</th>
                        <th class="text-right p-3 font-tajawal">المرحلة</th>
                        <th class="text-right p-3 font-tajawal">القيمة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($team->sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <a href="{{ route('crm.pipeline.show', $sale) }}" class="font-semibold text-gray-900 hover:underline font-tajawal">{{ $sale->client?->name ?? '—' }}</a>
                        </td>
                        <td class="p-3 text-gray-600 text-xs font-tajawal">{{ $sale->salesRep?->name ?? '—' }}</td>
                        <td class="p-3">
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 font-tajawal">{{ $stageLabels[$sale->stage] ?? $sale->stage }}</span>
                        </td>
                        <td class="p-3 tabular-nums text-xs font-semibold" style="color:{{ $themeColor }}">{{ $money($sale->estimated_value) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-8 text-center text-gray-400 font-tajawal">لا توجد صفقات مرتبطة بهذا الفريق</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    @if($isScopedManager ?? false)
    <a href="{{ route('crm.dashboard') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">لوحة الفريق</a>
    @else
    <a href="{{ route('crm.teams.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">العودة للفرق</a>
    @endif
    @if($canManage ?? false)
    <a href="{{ route('crm.teams.edit', $team) }}"
       class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal shadow-sm"
       style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
        تعديل الفريق
    </a>
    @endif
    @if($canDelete)
    <form action="{{ route('crm.teams.destroy', $team) }}" method="POST" class="inline"
          onsubmit="return confirm('حذف الفريق؟ سيتم فك ربط الصفقات.');">
        @csrf @method('DELETE')
        <button type="submit" class="px-5 py-2.5 rounded-xl border-2 border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 font-tajawal">حذف الفريق</button>
    </form>
    @endif
</div>
@endsection
