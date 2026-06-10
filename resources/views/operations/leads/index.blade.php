@extends('layouts.app')
@section('page-title', 'توزيع العملاء')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'إدارة وتوزيع العملاء',
    'subtitle' => 'استلام من التسويق — توزيع على المبيعات — تقليل الفقد',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">{{ session('error') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'بانتظار التوزيع', 'value' => $stats['unassigned'], 'accent' => 'amber'])
    @include('crm.partials.stat-card', ['label' => 'غير متابع +3 أيام', 'value' => $stats['stale'], 'accent' => 'red'])
</div>

@if($leadKpis)
@include('operations.partials.kpi-group', ['group' => $leadKpis])
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6 font-tajawal">
    <div class="lg:col-span-2 bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex flex-wrap gap-2 items-center justify-between">
            <p class="font-bold">عملاء بانتظار التوزيع</p>
            <form method="POST" action="{{ route('operations.leads.auto-distribute') }}">@csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-white text-xs font-bold" style="background:{{ $themeColor }}">توزيع تلقائي (أقل حملاً)</button>
            </form>
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
                </tr></thead>
                <tbody>
                @forelse($leads as $lead)
                <tr class="border-t">
                    <td class="p-3"><input type="checkbox" name="client_ids[]" value="{{ $lead->id }}" form="batch-form" class="lead-check"></td>
                    <td class="p-3 font-semibold">{{ $lead->name }}</td>
                    <td class="p-3" dir="ltr">{{ $lead->phone }}</td>
                    <td class="p-3 text-xs">{{ $lead->lead_source ?? '—' }}</td>
                    <td class="p-3 text-xs text-gray-500">{{ $lead->created_at->diffForHumans() }}</td>
                    <td class="p-3">
                        <form method="POST" action="{{ route('operations.leads.assign', $lead) }}" class="flex gap-1">@csrf
                            <select name="employee_id" class="border rounded-lg text-xs px-2 py-1" required>
                                @foreach($reps as $rep)
                                <option value="{{ $rep->employee?->id }}">{{ $rep->name }}</option>
                                @endforeach
                            </select>
                            <button class="px-2 py-1 rounded-lg text-white text-xs" style="background:{{ $themeColor }}">ترحيل</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-8 text-center text-gray-500">لا يوجد عملاء بانتظار التوزيع</td></tr>
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
