@extends('layouts.app')
@section('page-title', 'تعديل فريق')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
@endphp

@include('crm.partials.page-header', [
    'title' => 'تعديل الفريق',
    'subtitle' => $team->name,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
])

<form action="{{ route('crm.teams.update', $team) }}" method="POST" class="w-full max-w-4xl mx-auto space-y-6">
    @csrf
    @method('PUT')
    @include('crm.teams.partials.form')
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('crm.teams.show', $team) }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">
                إلغاء
            </a>
            @if($canDelete ?? false)
            <button type="button" onclick="document.getElementById('delete-team-form').submit()"
                    class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-red-200 text-red-600 font-semibold text-sm hover:bg-red-50 font-tajawal">
                حذف الفريق
            </button>
            @endif
        </div>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            حفظ التعديلات
        </button>
    </div>
</form>

@if($canDelete ?? false)
<form id="delete-team-form" action="{{ route('crm.teams.destroy', $team) }}" method="POST" class="hidden"
      onsubmit="return confirm('هل أنت متأكد من حذف هذا الفريق؟ سيتم فك ربط الصفقات بالفريق.');">
    @csrf
    @method('DELETE')
</form>
@endif
@endsection
