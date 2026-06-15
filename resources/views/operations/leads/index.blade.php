@extends('layouts.app')
@section('page-title', 'توزيع العملاء')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'إدارة وتوزيع العملاء',
    'subtitle' => 'استلام من التسويق — توزيع على المبيعات — تقليل الفقد',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
    'actionUrl' => auth()->user()?->can('create', \App\Models\Client::class) ? route('crm.clients.create') : null,
    'actionLabel' => 'عميل جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">{{ session('error') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'بانتظار التوزيع', 'value' => $stats['unassigned'], 'accent' => 'amber', 'href' => route('operations.leads.index', ['filter' => 'unassigned']) . '#page-data', 'linkLabel' => 'عرض المعلّقة'])
    @include('crm.partials.stat-card', ['label' => 'غير متابع +3 أيام', 'value' => $stats['stale'], 'accent' => 'red', 'href' => route('operations.leads.index', ['filter' => 'stale']) . '#page-data', 'linkLabel' => 'عرض المتأخرة'])
</div>

@if($leadKpis)
@include('operations.partials.kpi-group', ['group' => $leadKpis, 'link' => route('operations.leads.index') . '#page-data'])
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6 font-tajawal" id="page-data">
    <div class="lg:col-span-2 bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex flex-wrap gap-2 items-center justify-between">
            <div class="flex flex-wrap gap-2 items-center">
                <p class="font-bold">{{ ($filter ?? 'unassigned') === 'stale' ? 'عملاء غير متابعين (+3 أيام)' : 'عملاء بانتظار التوزيع' }}</p>
                <a href="{{ route('operations.leads.index', ['filter' => 'unassigned']) }}#page-data" class="text-xs font-bold px-2 py-1 rounded-lg {{ ($filter ?? 'unassigned') !== 'stale' ? 'text-white' : 'border text-gray-600' }}" @if(($filter ?? 'unassigned') !== 'stale') style="background:{{ $themeColor }}" @endif>بانتظار التوزيع</a>
                <a href="{{ route('operations.leads.index', ['filter' => 'stale']) }}#page-data" class="text-xs font-bold px-2 py-1 rounded-lg {{ ($filter ?? '') === 'stale' ? 'text-white' : 'border text-gray-600' }}" @if(($filter ?? '') === 'stale') style="background:{{ $themeColor }}" @endif>متأخرون</a>
            </div>
            @if(($filter ?? 'unassigned') !== 'stale')
            <form method="POST" action="{{ route('operations.leads.auto-distribute') }}">@csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-white text-xs font-bold" style="background:{{ $themeColor }}">توزيع تلقائي (أقل حملاً)</button>
            </form>
            @endif
        </div>
        <form method="GET" class="p-4 border-b"><input type="search" name="search" value="{{ request('search') }}" placeholder="بحث..." class="w-full border rounded-xl px-4 py-2 text-sm"></form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="p-3 text-right"><input type="checkbox" id="check-all"></th>
                    <th class="p-3 text-right">العميل</th>
                    <th class="p-3 text-right">الهاتف</th>
                    <th class="p-3 text-right">المصدر</th>
                    <th class="p-3 text-right">منذ</th>
                    <th class="p-3 text-right">تعيين</th>
                    <th class="p-3 text-right">إجراءات</th>
                </tr></thead>
                <tbody>
                @forelse($leads as $lead)
                <tr class="border-t">
                    <td class="p-3"><input type="checkbox" name="client_ids[]" value="{{ $lead->id }}" form="batch-form" class="lead-check"></td>
                    <td class="p-3">
                        <a href="{{ $lead->profileUrl() }}" class="font-semibold hover:underline" style="color:{{ $themeColor }}">{{ $lead->name }}</a>
                    </td>
                    <td class="p-3" dir="ltr">{{ $lead->phone }}</td>
                    <td class="p-3 text-xs">@include('crm.clients.partials.source-badge', ['source' => $lead->lead_source])</td>
                    <td class="p-3 text-xs text-gray-500">{{ $lead->created_at->diffForHumans() }}</td>
                    <td class="p-3">
                        @if(($filter ?? 'unassigned') !== 'stale')
                        <form method="POST" action="{{ route('operations.leads.assign', $lead) }}" class="flex gap-1">@csrf
                            <select name="employee_id" class="border rounded-lg text-xs px-2 py-1" required>
                                @foreach($reps as $rep)
                                <option value="{{ $rep->employee?->id }}">{{ $rep->name }}</option>
                                @endforeach
                            </select>
                            <button class="px-2 py-1 rounded-lg text-white text-xs" style="background:{{ $themeColor }}">ترحيل</button>
                        </form>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <div class="flex flex-wrap gap-1">
                            @can('viewFullDetails', $lead)
                            <a href="{{ route('crm.clients.show', $lead) }}" class="px-2 py-1 rounded-lg text-xs font-bold border hover:bg-gray-50" style="color:{{ $themeColor }};border-color:{{ $themeColor }}40">الملف الكامل</a>
                            @else
                            <a href="{{ $lead->profileUrl() }}" class="px-2 py-1 rounded-lg text-xs font-bold border hover:bg-gray-50" style="color:{{ $themeColor }};border-color:{{ $themeColor }}40">المسار</a>
                            @endcan
                            @can('update', $lead)
                            <a href="{{ route('crm.clients.edit', $lead) }}" class="px-2 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700 hover:bg-gray-200">تعديل</a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-8 text-center text-gray-500">لا يوجد عملاء بانتظار التوزيع</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $leads->links() }}</div>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border p-5">
            <p class="font-bold mb-3">حمل المندوبين</p>
            <ul class="space-y-2 text-sm">
                @foreach($repLoads as $row)
                <li class="flex justify-between gap-2 p-2 rounded-lg bg-gray-50">
                    <span>{{ $row['employee']->user?->name ?? ($row['employee']->first_name . ' ' . $row['employee']->last_name) }}</span>
                    <span class="font-bold" style="color:{{ $themeColor }}">{{ $row['load'] }} عميل</span>
                </li>
                @endforeach
            </ul>
        </div>
        <form id="batch-form" method="POST" action="{{ route('operations.leads.distribute-batch') }}" class="bg-white rounded-2xl border p-5">@csrf
            <p class="font-bold mb-2">توزيع المحدد</p>
            <select name="employee_id" class="w-full border rounded-xl px-3 py-2 text-sm mb-3">
                <option value="">تلقائي — الأقل حملاً</option>
                @foreach($reps as $rep)
                <option value="{{ $rep->employee?->id }}">{{ $rep->name }}</option>
                @endforeach
            </select>
            <button class="w-full py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">توزيع المحدد</button>
        </form>
    </div>
</div>
<script>
document.getElementById('check-all')?.addEventListener('change', function () {
    document.querySelectorAll('.lead-check').forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
