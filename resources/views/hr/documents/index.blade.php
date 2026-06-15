@extends('layouts.app')
@section('page-title', 'ملفات الموظفين')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'ملفات الموظفين',
    'subtitle' => 'حفظ وثائق الموظفين داخل النظام — هوية، شهادات، عقود، وتقارير',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي الملفات', 'value' => $stats['total'], 'accent' => 'theme', 'href' => route('hr.documents.index') . '#page-data', 'linkLabel' => 'عرض الكل'])
    @include('crm.partials.stat-card', ['label' => 'تنتهي خلال 30 يوم', 'value' => $stats['expiring'], 'accent' => 'amber'])
    @include('crm.partials.stat-card', ['label' => 'موظفون لديهم ملفات', 'value' => $stats['employees_with_files'], 'accent' => 'blue'])
</div>

<div id="page-data" class="bg-white rounded-2xl border shadow-lg overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-lg font-bold">أرشيف الملفات</h3>
        <div class="flex flex-wrap gap-2">
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث..." class="border rounded-xl px-3 py-2 text-sm">
                <select name="employee_id" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <option value="">كل الموظفين</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
                <select name="document_type" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <option value="">كل الأنواع</option>
                    @foreach($documentTypes as $k => $v)
                    <option value="{{ $k }}" @selected(request('document_type') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">رفع ملف</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-right">الموظف</th>
                    <th class="px-5 py-3 text-right">العنوان</th>
                    <th class="px-5 py-3 text-right">النوع</th>
                    <th class="px-5 py-3 text-right">اسم الملف</th>
                    <th class="px-5 py-3 text-right">الحجم</th>
                    <th class="px-5 py-3 text-right">انتهاء</th>
                    <th class="px-5 py-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documents as $doc)
                <tr class="hover:bg-gray-50/80">
                    <td class="px-5 py-4 font-semibold">
                        <a href="{{ route('employees.dossier', $doc->employee) }}" class="hover:underline" style="color:{{ $themeColor }}">{{ $doc->employee?->first_name }} {{ $doc->employee?->last_name }}</a>
                    </td>
                    <td class="px-5 py-4">{{ $doc->title }}</td>
                    <td class="px-5 py-4">{{ $doc->typeLabel() }}</td>
                    <td class="px-5 py-4 text-gray-600 max-w-[10rem] truncate" title="{{ $doc->original_filename }}">{{ $doc->original_filename }}</td>
                    <td class="px-5 py-4 text-gray-500">{{ $doc->file_size ? number_format($doc->file_size / 1024, 0) . ' KB' : '—' }}</td>
                    <td class="px-5 py-4">
                        @if($doc->expires_at)
                            <span class="{{ $doc->isExpired() ? 'text-red-700' : ($doc->isExpiringSoon() ? 'text-amber-700' : 'text-gray-600') }}">{{ $doc->expires_at->format('Y/m/d') }}</span>
                        @else — @endif
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('hr.documents.download', $doc) }}" class="text-xs font-bold mr-2" style="color:{{ $themeColor }}">تحميل</a>
                        <form method="POST" action="{{ route('hr.documents.destroy', $doc) }}" class="inline" onsubmit="return confirm('حذف الملف؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-600">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-16 text-center text-gray-500">لا توجد ملفات محفوظة</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($documents->hasPages())<div class="px-5 py-4 border-t">{{ $documents->links() }}</div>@endif
</div>

<div id="uploadModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('uploadModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border">
            <div class="px-5 py-4 border-b font-bold">رفع ملف لموظف</div>
            <form method="POST" action="{{ route('hr.documents.store') }}" enctype="multipart/form-data" class="p-5 space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-bold mb-1">الموظف</label>
                    <select name="employee_id" required class="w-full border rounded-xl px-3 py-2 text-sm">
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">نوع المستند</label>
                    <select name="document_type" required class="w-full border rounded-xl px-3 py-2 text-sm">
                        @foreach($documentTypes as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">عنوان المستند</label>
                    <input type="text" name="title" required class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="مثال: بطاقة الهوية الوطنية">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">تاريخ انتهاء (اختياري)</label>
                    <input type="date" name="expires_at" class="w-full border rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">الملف</label>
                    <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.webp,.zip" class="w-full text-sm">
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">حفظ الملف</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
