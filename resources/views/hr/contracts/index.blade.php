@extends('layouts.app')
@section('page-title', 'عقود الموظفين')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $statusColors = [
        'draft' => 'bg-gray-100 text-gray-800',
        'active' => 'bg-green-100 text-green-800',
        'expired' => 'bg-amber-100 text-amber-800',
        'terminated' => 'bg-red-100 text-red-800',
    ];
@endphp

@include('crm.partials.page-header', [
    'title' => 'عقود الموظفين',
    'subtitle' => 'إدارة عقود العمل — رفع المستندات ومتابعة انتهاء الصلاحية',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'عقود سارية', 'value' => $stats['active'], 'accent' => 'green', 'href' => route('hr.contracts.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'عرض السارية'])
    @include('crm.partials.stat-card', ['label' => 'تنتهي خلال 30 يوم', 'value' => $stats['expiring'], 'accent' => 'amber', 'href' => route('hr.contracts.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'متابعة الانتهاء'])
    @include('crm.partials.stat-card', ['label' => 'مسودات', 'value' => $stats['draft'], 'accent' => 'theme', 'href' => route('hr.contracts.index', ['status' => 'draft']) . '#page-data', 'linkLabel' => 'عرض المسودات'])
</div>

<div id="page-data" class="bg-white rounded-2xl border shadow-lg overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-lg font-bold">سجل العقود</h3>
        <div class="flex flex-wrap gap-2">
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث..." class="border rounded-xl px-3 py-2 text-sm">
                <select name="status" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <option value="">كل الحالات</option>
                    @foreach(config('hr_contracts.status_labels', []) as $k => $v)
                    <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                <select name="employee_id" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <option value="">كل الموظفين</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" onclick="document.getElementById('newContractModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">عقد جديد</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-right">رقم العقد</th>
                    <th class="px-5 py-3 text-right">الموظف</th>
                    <th class="px-5 py-3 text-right">العنوان</th>
                    <th class="px-5 py-3 text-right">النوع</th>
                    <th class="px-5 py-3 text-right">الفترة</th>
                    <th class="px-5 py-3 text-right">الحالة</th>
                    <th class="px-5 py-3 text-right">ملف</th>
                    <th class="px-5 py-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contracts as $contract)
                <tr class="hover:bg-gray-50/80"
                    data-contract-id="{{ $contract->id }}"
                    data-title="{{ $contract->title }}"
                    data-contract-type="{{ $contract->contract_type }}"
                    data-start-date="{{ $contract->start_date->format('Y-m-d') }}"
                    data-end-date="{{ optional($contract->end_date)->format('Y-m-d') }}"
                    data-salary="{{ $contract->salary }}"
                    data-status="{{ $contract->status }}"
                    data-terms="{{ $contract->terms }}"
                    data-notes="{{ $contract->notes }}"
                    data-update-url="{{ route('hr.contracts.update', $contract) }}">
                    <td class="px-5 py-4 font-mono text-xs">{{ $contract->contract_number }}</td>
                    <td class="px-5 py-4 font-semibold">{{ $contract->employee?->first_name }} {{ $contract->employee?->last_name }}</td>
                    <td class="px-5 py-4">{{ $contract->title }}</td>
                    <td class="px-5 py-4">{{ $contract->typeLabel() }}</td>
                    <td class="px-5 py-4 text-gray-600">
                        {{ $contract->start_date->format('Y/m/d') }}
                        @if($contract->end_date) — {{ $contract->end_date->format('Y/m/d') }}@endif
                        @if($contract->isExpiringSoon())<span class="block text-xs text-amber-700">ينتهي قريباً</span>@endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $statusColors[$contract->status] ?? 'bg-gray-100 text-gray-800' }}">{{ $contract->statusLabel() }}</span>
                    </td>
                    <td class="px-5 py-4">
                        @if($contract->file_path)
                        <a href="{{ route('hr.contracts.download', $contract) }}" class="text-xs font-bold hover:underline" style="color:{{ $themeColor }}">تحميل</a>
                        @else — @endif
                    </td>
                    <td class="px-5 py-4">
                        <button type="button" onclick="openEditContract(this.closest('tr'))" class="text-xs font-bold text-blue-600">تعديل</button>
                        <form method="POST" action="{{ route('hr.contracts.destroy', $contract) }}" class="inline" onsubmit="return confirm('حذف العقد؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-600 mr-2">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-16 text-center text-gray-500">لا توجد عقود مسجّلة</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contracts->hasPages())<div class="px-5 py-4 border-t">{{ $contracts->links() }}</div>@endif
</div>

<div id="newContractModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('newContractModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 overflow-y-auto">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border my-8">
            <div class="px-5 py-4 border-b font-bold">عقد موظف جديد</div>
            <form method="POST" action="{{ route('hr.contracts.store') }}" enctype="multipart/form-data" class="p-5 space-y-3">
                @csrf
                @include('hr.contracts.partials.form-fields', ['employees' => $employees, 'contractTypes' => $contractTypes])
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('newContractModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">حفظ العقد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editContractModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('editContractModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 overflow-y-auto">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border my-8">
            <div class="px-5 py-4 border-b font-bold">تعديل العقد</div>
            <form id="editContractForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-3">
                @csrf @method('PUT')
                <div id="editContractFields"></div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('editContractModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditContract(row) {
    const d = row.dataset;
    document.getElementById('editContractForm').action = d.updateUrl;
    document.getElementById('editContractFields').innerHTML = `
        <div><label class="block text-sm font-bold mb-1">العنوان</label><input name="title" value="${d.title || ''}" required class="w-full border rounded-xl px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-bold mb-1">نوع العقد</label><select name="contract_type" class="w-full border rounded-xl px-3 py-2 text-sm">@foreach($contractTypes as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-sm font-bold mb-1">تاريخ البداية</label><input type="date" name="start_date" value="${d.startDate || ''}" required class="w-full border rounded-xl px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-bold mb-1">تاريخ النهاية</label><input type="date" name="end_date" value="${d.endDate || ''}" class="w-full border rounded-xl px-3 py-2 text-sm"></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-sm font-bold mb-1">الراتب</label><input type="number" step="0.01" name="salary" value="${d.salary || ''}" class="w-full border rounded-xl px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-bold mb-1">الحالة</label><select name="status" class="w-full border rounded-xl px-3 py-2 text-sm">@foreach(config('hr_contracts.status_labels', []) as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
        </div>
        <div><label class="block text-sm font-bold mb-1">الشروط</label><textarea name="terms" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm">${d.terms || ''}</textarea></div>
        <div><label class="block text-sm font-bold mb-1">ملاحظات</label><textarea name="notes" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm">${d.notes || ''}</textarea></div>
        <div><label class="block text-sm font-bold mb-1">ملف العقد (اختياري)</label><input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm"></div>
    `;
    document.querySelector('#editContractFields select[name="contract_type"]').value = d.contractType;
    document.querySelector('#editContractFields select[name="status"]').value = d.status;
    document.getElementById('editContractModal').classList.remove('hidden');
}
</script>
@endsection
