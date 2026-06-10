@extends('layouts.app')
@section('page-title', 'المستخدمون')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'إدارة المستخدمين',
    'subtitle' => 'حسابات الدخول، الأدوار، وربط الموظفين بالنظام العقاري',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
    'actionUrl' => auth()->user()->can('create-users') ? route('users.create') : null,
    'actionLabel' => 'مستخدم جديد',
])

@if(session('success'))<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">{{ session('error') }}</div>@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'إجمالي المستخدمين', 'value' => $stats['total'], 'accent' => 'theme'])
    @include('crm.partials.stat-card', ['label' => 'حسابات مفعّلة', 'value' => $stats['verified'], 'accent' => 'green'])
    @include('crm.partials.stat-card', ['label' => 'مرتبطون بموظف', 'value' => $stats['with_employee'], 'accent' => 'blue'])
    @include('crm.partials.stat-card', ['label' => 'إدارة', 'value' => $stats['admins'], 'accent' => 'purple'])
</div>

<div class="bg-white rounded-2xl border p-4 mb-6 font-tajawal">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">بحث</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="الاسم أو البريد..." class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
        </div>
        <div class="w-full sm:w-48">
            <label class="block text-xs font-bold text-gray-500 mb-1">الدور</label>
            <select name="role" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <option value="">كل الأدوار</option>
                @foreach($assignableRoles as $role)
                <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ \App\Services\CrmRoleCatalogService::roleLabel($role->name) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <option value="">الكل</option>
                <option value="verified" @selected(request('status') === 'verified')>مفعّل</option>
                <option value="pending" @selected(request('status') === 'pending')>بانتظار التفعيل</option>
                <option value="with_employee" @selected(request('status') === 'with_employee')>له سجل موظف</option>
                <option value="without_employee" @selected(request('status') === 'without_employee')>بدون موظف</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:{{ $themeColor }}">تطبيق</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px]">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4 text-right font-bold">المستخدم</th>
                    <th class="p-4 text-right font-bold">الدور</th>
                    <th class="p-4 text-right font-bold">القسم / الموظف</th>
                    <th class="p-4 text-right font-bold">الحالة</th>
                    <th class="p-4 text-right font-bold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($users as $user)
            @php
                $roleKey = \App\Services\CrmRoleCatalogService::resolveUserDisplayRole($user);
                $meta = $roleKey ? \App\Services\CrmRoleCatalogService::roleMeta($roleKey) : null;
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="p-4">
                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500" dir="ltr">{{ $user->email }}</p>
                </td>
                <td class="p-4">
                    @if($meta)
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background: {{ $meta['color'] }}18; color: {{ $meta['color'] }}">{{ $meta['label'] }}</span>
                    @else
                    <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
                <td class="p-4 text-xs text-gray-600">
                    @if($user->employee)
                        {{ $user->employee->department?->name ?? '—' }}
                        <span class="block text-gray-400" dir="ltr">{{ $user->employee->employee_id }}</span>
                    @else
                        <span class="text-amber-600">بدون سجل موظف</span>
                    @endif
                </td>
                <td class="p-4">
                    @if($user->email_verified_at)
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">نشط</span>
                    @else
                    <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">معلق</span>
                    @endif
                </td>
                <td class="p-4">
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('users.show', $user) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-white" style="background:{{ $themeColor }}">عرض</a>
                        @can('edit-users')
                        <a href="{{ route('users.edit', $user) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold border border-gray-200">تعديل</a>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="p-10 text-center text-gray-500">لا يوجد مستخدمون</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())<div class="p-4 border-t">{{ $users->links() }}</div>@endif
</div>
@endsection
