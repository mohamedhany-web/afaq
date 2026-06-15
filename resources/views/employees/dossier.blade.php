@extends('layouts.app')
@section('page-title', 'ملف الموظف — ' . ($personal['full_name'] ?? ''))

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $tabs = [
        'personal' => 'البيانات الشخصية',
        'employment' => 'بيانات التوظيف',
        'cv' => 'السيرة الذاتية',
        'documents' => 'المستندات',
        'attendance' => 'الحضور والانصراف',
        'performance' => 'تقييم الأداء',
        'notes' => 'ملاحظات إدارية',
    ];
    $employmentLabels = ['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'intern' => 'متدرب'];
    $statusLabels = ['active' => 'نشط', 'inactive' => 'غير نشط', 'on_leave' => 'في إجازة', 'terminated' => 'منتهي الخدمة'];
    $dossierUrl = route('employees.dossier', array_merge(['employee' => $employee], $listQuery));
@endphp

@include('crm.partials.page-header', [
    'title' => 'ملف الموظف — ' . $personal['full_name'],
    'subtitle' => ($roleMeta['label'] ?? '') . ' · ' . ($employment['department'] ?? '') . ' · ' . ($employment['employee_id'] ?? ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    'actionUrl' => auth()->user()?->can('edit-employees') ? route('employees.edit', array_merge(['employee' => $employee], $listQuery)) : null,
    'actionLabel' => 'تعديل البيانات',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'أيام حضور (الفترة)', 'value' => $attendance_summary['present'] + $attendance_summary['late'], 'accent' => 'green', 'href' => $dossierUrl . '?tab=attendance#dossier-content', 'linkLabel' => 'سجل الحضور'])
    @include('crm.partials.stat-card', ['label' => 'المستندات', 'value' => $documents->count(), 'accent' => 'blue', 'href' => $dossierUrl . '?tab=documents#dossier-content', 'linkLabel' => 'عرض المستندات'])
    @include('crm.partials.stat-card', ['label' => 'تقييم الأداء', 'value' => ($performance['compliance']['overall_score'] ?? '—') . (isset($performance['compliance']['overall_score']) ? '%' : ''), 'accent' => 'theme', 'href' => $dossierUrl . '?tab=performance#dossier-content', 'linkLabel' => 'التفاصيل'])
    @include('crm.partials.stat-card', ['label' => 'ملاحظات إدارية', 'value' => $notes->count(), 'accent' => 'amber', 'href' => $dossierUrl . '?tab=notes#dossier-content', 'linkLabel' => 'عرض الملاحظات'])
</div>

<div class="bg-white rounded-2xl border shadow-lg overflow-hidden font-tajawal mb-6">
    <div class="flex flex-wrap gap-1 p-2 border-b bg-gray-50/80">
        @foreach($tabs as $key => $label)
        <a href="{{ $dossierUrl }}?tab={{ $key }}#dossier-content"
           class="px-4 py-2 rounded-xl text-sm font-bold transition {{ $activeTab === $key ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}"
           @if($activeTab === $key) style="background:{{ $themeColor }}" @endif>{{ $label }}</a>
        @endforeach
    </div>

    <div id="dossier-content" class="p-5 sm:p-6">
        @if($activeTab === 'personal')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['الاسم الكامل', $personal['full_name']],
                ['البريد', $personal['email']],
                ['الهاتف', $personal['phone']],
                ['رقم الهوية', $personal['national_id'] ?? '—'],
                ['العنوان', $personal['address'] ?? '—'],
                ['جهة طوارئ', $personal['emergency_contact'] ?? '—'],
                ['هاتف الطوارئ', $personal['emergency_phone'] ?? '—'],
            ] as [$lbl, $val])
            <div><p class="text-xs font-bold text-gray-500 mb-1">{{ $lbl }}</p><p class="text-sm font-medium text-gray-900">{{ $val ?: '—' }}</p></div>
            @endforeach
        </div>

        @elseif($activeTab === 'employment')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['الرقم التوظيفي', $employment['employee_id']],
                ['المنصب', $employment['position'] ?? $roleMeta['label']],
                ['القسم', $employment['department']],
                ['نوع التوظيف', $employmentLabels[$employment['employment_type']] ?? $employment['employment_type']],
                ['تاريخ التعيين', $employment['hire_date']?->format('Y/m/d')],
                ['الراتب', $employment['salary'] ? number_format($employment['salary']) . ' ج.م' : '—'],
                ['الحالة', $statusLabels[$employment['status']] ?? $employment['status']],
                ['المدير المباشر', $employment['reports_to'] ?? '—'],
                ['جدول الدوام', $employment['schedule']],
                ['أيام الراحة', $employment['off_days']],
                ['ساعات العمل', $employment['daily_hours'] . ' ساعة'],
            ] as [$lbl, $val])
            <div><p class="text-xs font-bold text-gray-500 mb-1">{{ $lbl }}</p><p class="text-sm font-medium text-gray-900">{{ $val ?: '—' }}</p></div>
            @endforeach
        </div>
        @if($contracts->isNotEmpty())
        <div class="mt-8">
            <h4 class="font-bold text-gray-900 mb-3">العقود المسجّلة</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr>
                        <th class="p-3 text-right">رقم العقد</th><th class="p-3 text-right">العنوان</th><th class="p-3 text-right">الفترة</th><th class="p-3 text-right">الحالة</th>
                    </tr></thead>
                    <tbody>
                        @foreach($contracts as $c)
                        <tr class="border-t"><td class="p-3 font-mono text-xs">{{ $c->contract_number }}</td><td class="p-3">{{ $c->title }}</td>
                        <td class="p-3">{{ $c->start_date->format('Y/m/d') }}@if($c->end_date) — {{ $c->end_date->format('Y/m/d') }}@endif</td>
                        <td class="p-3">{{ $c->statusLabel() }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @elseif($activeTab === 'cv')
        <div class="max-w-xl">
            @if($cv)
            <div class="border rounded-2xl p-5 mb-4">
                <p class="font-bold text-gray-900 mb-1">{{ $cv->title }}</p>
                <p class="text-sm text-gray-500 mb-3">{{ $cv->original_filename }} — {{ $cv->created_at->format('Y/m/d') }}</p>
                <a href="{{ route('employees.dossier.documents.download', [$employee, $cv]) }}" class="inline-flex px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">تحميل السيرة الذاتية</a>
            </div>
            @else
            <p class="text-gray-500 mb-4">لم تُرفع سيرة ذاتية بعد.</p>
            @endif
            @if($canManageDocuments)
            <form method="POST" action="{{ route('employees.dossier.cv.store', $employee) }}" enctype="multipart/form-data" class="border rounded-2xl p-5 space-y-3">
                @csrf
                <p class="font-bold text-gray-900">{{ $cv ? 'استبدال السيرة الذاتية' : 'رفع السيرة الذاتية' }}</p>
                <input type="file" name="file" required accept=".pdf,.doc,.docx" class="w-full text-sm">
                <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">رفع CV</button>
            </form>
            @endif
        </div>

        @elseif($activeTab === 'documents')
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-gray-900">مستندات الموظف ({{ $documents->count() }})</h4>
            @if($canManageDocuments)
            <button type="button" onclick="document.getElementById('uploadDocModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">رفع مستند</button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr>
                    <th class="p-3 text-right">العنوان</th><th class="p-3 text-right">النوع</th><th class="p-3 text-right">الملف</th><th class="p-3 text-right">التاريخ</th><th class="p-3 text-right">إجراء</th>
                </tr></thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr class="border-t hover:bg-gray-50/50">
                        <td class="p-3 font-semibold">{{ $doc->title }}</td>
                        <td class="p-3">{{ $doc->typeLabel() }}</td>
                        <td class="p-3 text-gray-600 truncate max-w-[10rem]">{{ $doc->original_filename }}</td>
                        <td class="p-3 text-gray-500">{{ $doc->created_at->format('Y/m/d') }}</td>
                        <td class="p-3"><a href="{{ route('employees.dossier.documents.download', [$employee, $doc]) }}" class="text-xs font-bold" style="color:{{ $themeColor }}">تحميل</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">لا توجد مستندات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @elseif($activeTab === 'attendance')
        <form method="GET" action="{{ $dossierUrl }}" class="flex flex-wrap gap-3 mb-5 items-end">
            <input type="hidden" name="tab" value="attendance">
            @foreach($listQuery as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
            <div><label class="text-xs font-bold text-gray-500 block mb-1">من</label><input type="date" name="from" value="{{ $period['start']->format('Y-m-d') }}" class="border rounded-xl px-3 py-2 text-sm"></div>
            <div><label class="text-xs font-bold text-gray-500 block mb-1">إلى</label><input type="date" name="to" value="{{ $period['end']->format('Y-m-d') }}" class="border rounded-xl px-3 py-2 text-sm"></div>
            <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">عرض</button>
        </form>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-5">
            @include('crm.partials.stat-card', ['label' => 'إجمالي', 'value' => $attendance_summary['total'], 'accent' => 'theme', 'compact' => true])
            @include('crm.partials.stat-card', ['label' => 'حضور', 'value' => $attendance_summary['present'], 'accent' => 'green', 'compact' => true])
            @include('crm.partials.stat-card', ['label' => 'تأخير', 'value' => $attendance_summary['late'], 'accent' => 'amber', 'compact' => true])
            @include('crm.partials.stat-card', ['label' => 'غياب', 'value' => $attendance_summary['absent'], 'accent' => 'red', 'compact' => true])
            @include('crm.partials.stat-card', ['label' => 'ساعات', 'value' => $attendance_summary['total_hours'], 'accent' => 'blue', 'compact' => true])
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr>
                    <th class="p-3 text-right">التاريخ</th><th class="p-3 text-right">دخول</th><th class="p-3 text-right">خروج</th><th class="p-3 text-right">الساعات</th><th class="p-3 text-right">الحالة</th>
                </tr></thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr class="border-t">
                        <td class="p-3">{{ $att->date->format('Y/m/d') }}</td>
                        <td class="p-3" dir="ltr">{{ $att->check_in?->format('H:i') ?? '—' }}</td>
                        <td class="p-3" dir="ltr">{{ $att->check_out?->format('H:i') ?? '—' }}</td>
                        <td class="p-3">{{ $att->total_hours ?? '—' }}</td>
                        <td class="p-3">{{ $att->status }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">لا توجد سجلات في هذه الفترة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <a href="{{ route('attendances.index', ['employee_id' => $employee->id]) }}" class="inline-block mt-4 text-sm font-bold" style="color:{{ $themeColor }}">عرض السجل الكامل ←</a>

        @elseif($activeTab === 'performance')
        @php $comp = $performance['compliance'] ?? null; $kpi = $performance['kpi'] ?? null; @endphp
        @if(!$employee->user)
        <p class="text-gray-500">لا يوجد حساب مستخدم مرتبط — لا يتوفر تقييم أداء آلي.</p>
        @else
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            @if($comp)
            @include('crm.partials.stat-card', ['label' => 'التقييم الإجمالي', 'value' => $comp['overall_score'] . '%', 'accent' => ($comp['status']['color'] ?? 'theme') === 'green' ? 'green' : 'amber', 'compact' => true])
            @include('crm.partials.stat-card', ['label' => 'التزام الحضور', 'value' => $comp['attendance_compliance'] . '%', 'accent' => 'purple', 'compact' => true])
            @include('crm.partials.stat-card', ['label' => 'التقارير اليومية', 'value' => ($comp['reports']['submitted'] ?? 0) . '/' . ($comp['reports']['expected'] ?? 0), 'accent' => 'blue', 'compact' => true])
            @include('crm.partials.stat-card', ['label' => 'مهام متأخرة', 'value' => $comp['overdue_tasks'] ?? 0, 'accent' => 'red', 'compact' => true])
            @endif
        </div>
        @if($kpi && !empty($kpi['items']))
        <h4 class="font-bold text-gray-900 mb-3">مؤشرات KPI — {{ $kpi['level']['label'] ?? '' }}</h4>
        <div class="space-y-2 mb-6">
            @foreach($kpi['items'] as $item)
            <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50 text-sm">
                <span>{{ $item['label'] ?? $item['name'] ?? '—' }}</span>
                <span class="font-bold">{{ $item['score'] ?? $item['percent'] ?? '—' }}%</span>
            </div>
            @endforeach
        </div>
        @endif
        @if($employee->user->canAccessCrm())
        <a href="{{ route('crm.employee-compliance.show', $employee->user) }}" class="text-sm font-bold" style="color:{{ $themeColor }}">تقرير الالتزام التفصيلي ←</a>
        @endif
        @endif

        @elseif($activeTab === 'notes')
        @if($canManageNotes)
        <form method="POST" action="{{ route('employees.dossier.notes.store', $employee) }}" class="border rounded-2xl p-5 mb-6 space-y-3">
            @csrf
            <p class="font-bold text-gray-900">إضافة ملاحظة إدارية</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <select name="category" class="border rounded-xl px-3 py-2 text-sm">
                    @foreach(config('employee_admin_notes.categories', []) as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                </select>
                <input type="text" name="title" placeholder="عنوان (اختياري)" class="border rounded-xl px-3 py-2 text-sm">
            </div>
            <textarea name="body" required rows="3" placeholder="نص الملاحظة..." class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_confidential" value="1"> ملاحظة سرية (HR فقط)</label>
            <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">حفظ الملاحظة</button>
        </form>
        @endif
        <div class="space-y-3">
            @forelse($notes as $note)
            <div class="border rounded-2xl p-4 {{ $note->is_confidential ? 'border-amber-200 bg-amber-50/50' : '' }}">
                <div class="flex justify-between items-start gap-3 mb-2">
                    <div>
                        <p class="font-bold text-gray-900">{{ $note->title ?: $note->categoryLabel() }}</p>
                        <p class="text-xs text-gray-500">{{ $note->author?->name }} — {{ $note->created_at->format('Y/m/d H:i') }}
                            @if($note->is_confidential)<span class="text-amber-700 font-bold"> · سرية</span>@endif
                        </p>
                    </div>
                    @if($canManageNotes)
                    <form method="POST" action="{{ route('employees.dossier.notes.destroy', [$employee, $note]) }}" onsubmit="return confirm('حذف الملاحظة؟')">@csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 font-bold">حذف</button>
                    </form>
                    @endif
                </div>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $note->body }}</p>
            </div>
            @empty
            <p class="text-gray-500 text-center py-8">لا توجد ملاحظات إدارية</p>
            @endforelse
        </div>
        @endif
    </div>
</div>

<div class="flex flex-wrap gap-3">
    <a href="{{ route('employees.show', array_merge(['employee' => $employee], $listQuery)) }}" class="px-4 py-2 rounded-xl border text-sm font-bold text-gray-600">ملخص الموظف</a>
    <a href="{{ route('employees.index', $listQuery) }}" class="px-4 py-2 rounded-xl border text-sm font-bold text-gray-600">قائمة الموظفين</a>
    @if(auth()->user()?->canAccessHr())
    <a href="{{ route('hr.documents.index', ['employee_id' => $employee->id]) }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white" style="background:{{ $themeColor }}">أرشيف HR</a>
    @endif
</div>

@if($canManageDocuments)
<div id="uploadDocModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('uploadDocModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border p-5 space-y-3">
            <p class="font-bold">رفع مستند</p>
            <form method="POST" action="{{ route('employees.dossier.documents.store', $employee) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <select name="document_type" required class="w-full border rounded-xl px-3 py-2 text-sm">
                    @foreach(config('employee_documents.types', []) as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                </select>
                <input type="text" name="title" required placeholder="عنوان المستند" class="w-full border rounded-xl px-3 py-2 text-sm">
                <input type="date" name="expires_at" class="w-full border rounded-xl px-3 py-2 text-sm">
                <input type="file" name="file" required class="w-full text-sm">
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('uploadDocModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">رفع</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
