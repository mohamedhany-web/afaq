@extends('layouts.developer')
@section('page-title', 'مشروع جديد')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp

@include('crm.partials.page-header', [
    'title' => 'إضافة مشروع عقاري',
    'subtitle' => 'سجّل مشروعاً جديداً ليظهر مباشرة لفريق المبيعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>',
    'secondaryUrl' => route('developer.projects.index'),
    'secondaryLabel' => 'قائمة المشاريع',
])

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
    <ul class="list-disc pr-5 text-sm text-red-700 font-tajawal space-y-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('developer.projects.store') }}" class="space-y-6">
    @csrf
    @include('developer-portal.projects.partials.form', ['project' => new \App\Models\Project(), 'themeColor' => $themeColor])
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-2">
        <a href="{{ route('developer.projects.index') }}"
           class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">
            إلغاء
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            حفظ المشروع
        </button>
    </div>
</form>
@endsection
