@extends('layouts.app')
@section('page-title', 'العملاء')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $typeLabels = ['individual' => 'فرد', 'small_business' => 'شركة'];
@endphp

@include('crm.partials.page-header', [
    'title' => 'العملاء',
    'subtitle' => 'إدارة قاعدة عملاء المبيعات العقارية',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('crm.clients.create'),
    'actionLabel' => 'عميل جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
])

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>
@endif
@php $importResult = session('import_result'); @endphp
@if($importResult && !empty($importResult['errors']))
<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm font-tajawal">
    <p class="font-bold text-amber-900 mb-2">تفاصيل الصفوف الفاشلة:</p>
    <ul class="space-y-1 text-amber-800 max-h-40 overflow-y-auto">
        @foreach(array_slice($importResult['errors'], 0, 10) as $err)
        <li>صف {{ $err['row'] ?? '—' }}: {{ $err['message'] ?? '' }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('crm.clients.create', ['tab' => 'import']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold border-2 font-tajawal hover:bg-gray-50"
       style="border-color: {{ $themeColor }}40; color: {{ $themeColor }};">
        استيراد من Excel / CSV
    </a>
    <a href="{{ route('crm.clients.import.template') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 font-tajawal">
        تنزيل قالب العملاء
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي العملاء', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'])
    @include('crm.partials.stat-card', ['label' => 'عملاء محتملون', 'value' => $stats['prospect'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'])
    @include('crm.partials.stat-card', ['label' => 'عملاء نشطون', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'])
    @include('crm.partials.stat-card', ['label' => 'لديهم صفقات', 'value' => $stats['with_deals'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />'])
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="الاسم، الهاتف، البريد، أو الشركة..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-48">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">كل الحالات</option>
                @foreach(['prospect' => 'محتمل', 'active' => 'نشط', 'inactive' => 'غير نشط', 'suspended' => 'موقوف'] as $val => $txt)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $txt }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal"
                    style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">تطبيق</button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('crm.clients.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <h2 class="font-bold text-gray-900 font-tajawal">قائمة العملاء</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: {{ $themeColor }}15; color: {{ $themeColor }};">{{ $clients->total() }} عميل</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 bg-gray-50/50">
                <tr class="text-gray-600">
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">العميل</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">التواصل</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">النوع</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">الحالة</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">الصفقات</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">المسؤول</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">أضافه</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">إجراءات</th>
                </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                <tr class="border-t border-gray-100 hover:bg-gray-50/80 transition-colors">
                    <td class="p-4">
                        <a href="{{ route('crm.clients.show', $client) }}" class="font-semibold text-gray-900 hover:underline font-tajawal">{{ $client->name }}</a>
                        @if($client->company_name)
                            <div class="text-xs text-gray-500 mt-0.5 font-tajawal">{{ $client->company_name }}</div>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="text-gray-900 font-tajawal" dir="ltr">{{ $client->phone }}</div>
                        @if($client->email)
                            <div class="text-xs text-gray-500 mt-0.5" dir="ltr">{{ $client->email }}</div>
                        @endif
                    </td>
                    <td class="p-4 text-gray-600 font-tajawal whitespace-nowrap">{{ $typeLabels[$client->client_type] ?? 'فرد' }}</td>
                    <td class="p-4">@include('crm.clients.partials.status-badge', ['status' => $client->status])</td>
                    <td class="p-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal"
                              style="background: {{ $themeColor }}10; color: {{ $themeColor }};">
                            {{ $client->sales->count() }} صفقة
                        </span>
                    </td>
                    <td class="p-4 text-gray-600 font-tajawal whitespace-nowrap">
                        @if($client->assignedEmployee)
                            {{ trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name) }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="p-4">
                        @include('crm.clients.partials.created-by', ['client' => $client])
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('crm.clients.show', $client) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal hover:opacity-80"
                               style="background: {{ $themeColor }}15; color: {{ $themeColor }};">عرض</a>
                            <a href="{{ route('crm.clients.edit', $client) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 font-tajawal">تعديل</a>
                            @if($client->sales->isEmpty())
                            <form action="{{ route('crm.clients.destroy', $client) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 font-tajawal">حذف</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-12 text-center">
                        <div class="text-gray-400 font-tajawal mb-4">لا يوجد عملاء مطابقون للبحث</div>
                        <a href="{{ route('crm.clients.create') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                           style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                            إضافة أول عميل
                        </a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
    <div class="p-4 sm:p-5 border-t border-gray-200">{{ $clients->links() }}</div>
    @endif
</div>
@endsection
