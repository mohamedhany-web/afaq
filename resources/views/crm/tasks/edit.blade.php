@extends('layouts.app')
@section('page-title', 'تعديل مهمة')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'تعديل المهمة',
    'subtitle' => $task->title,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
    'actionUrl' => route('crm.tasks.show', $task),
    'actionLabel' => 'عرض المهمة',
])

@if($errors->any())
<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 font-tajawal text-sm text-red-800">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('crm.tasks.update', $task) }}" method="POST"
      class="w-full font-tajawal space-y-6"
      x-data="{ priority: @js(old('priority', $task->priority)) }">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8">@include('crm.tasks.partials.form', ['task' => $task])</div>
        <aside class="xl:col-span-4">
            <div class="bg-white rounded-2xl border p-5 text-sm font-tajawal space-y-2">
                <p><span class="text-gray-500">الحالة:</span> <strong>{{ $task->statusLabel() }}</strong></p>
                <p><span class="text-gray-500">أُنشئت:</span> {{ $task->created_at->format('Y/m/d H:i') }}</p>
                @if($task->assigner)<p><span class="text-gray-500">بواسطة:</span> {{ $task->assigner->name }}</p>@endif
            </div>
        </aside>
    </div>
    <div class="flex flex-col sm:flex-row justify-between gap-3 pt-2 border-t border-gray-200">
        <a href="{{ route('crm.tasks.show', $task) }}" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-sm font-semibold text-center">رجوع</a>
        <button type="submit" class="px-8 py-3 rounded-xl text-white text-sm font-bold shadow-md"
                style="background:linear-gradient(135deg,{{ $themeColor }},{{ $themeColor }}dd)">حفظ التعديلات</button>
    </div>
</form>
@endsection
