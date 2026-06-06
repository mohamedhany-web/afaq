@extends('layouts.app')
@section('page-title', 'فريق التسويق')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'فريق التسويق',
    'subtitle' => $department->name . ' — ' . $department->description,
    'actionUrl' => $canManage ? route('employees.create', ['marketing_only' => 1]) : null,
    'actionLabel' => 'إضافة موظف تسويق',
])

<div class="grid grid-cols-3 gap-3 mb-6">
    @include('crm.partials.stat-card', ['label' => 'الفريق', 'value' => $stats['total'], 'accent' => 'purple'])
    @include('crm.partials.stat-card', ['label' => 'مديرون', 'value' => $stats['managers'], 'accent' => 'theme'])
    @include('crm.partials.stat-card', ['label' => 'موظفون', 'value' => $stats['reps'], 'accent' => 'blue'])
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($employees as $employee)
    <div class="bg-white rounded-2xl shadow-lg border p-5 font-tajawal">
        <p class="font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $employee->position }} · {{ $employee->email }}</p>
        @php $empRole = \App\Services\EmployeeRoleService::resolve($employee); @endphp
        <p class="text-xs mt-2">
            <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700">{{ $empRole['label'] }}</span>
        </p>
        <span class="inline-block mt-3 text-xs px-2 py-1 rounded {{ $employee->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $employee->status === 'active' ? 'نشط' : $employee->status }}</span>
    </div>
    @empty
    <p class="col-span-full text-center text-gray-500 py-10">لا يوجد فريق تسويق بعد. @if($canManage) أضف موظفاً من زر «إضافة موظف تسويق». @endif</p>
    @endforelse
</div>
<div class="mt-4">{{ $employees->links() }}</div>
@endsection
