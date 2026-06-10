@extends('layouts.app')
@section('page-title', 'تعديل عميل')

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
@endphp

@include('crm.partials.page-header', [
    'title' => ($requiresApproval ?? false) ? 'طلب تعديل عميل' : 'تعديل عميل',
    'subtitle' => $client->name,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
])

@if($requiresApproval ?? false)
<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900 font-tajawal">
    سيتم إرسال التعديلات للإدارة للموافقة قبل تطبيقها على ملف العميل.
</div>
@endif

<form action="{{ route('crm.clients.update', $client) }}" method="POST" class="w-full space-y-6">
    @csrf
    @method('PUT')
    @include('crm.clients.partials.form', ['client' => $client])

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
        <a href="{{ route('crm.clients.show', $client) }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">
            إلغاء والعودة لملف العميل
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all font-tajawal"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            {{ ($requiresApproval ?? false) ? 'إرسال طلب التعديل' : 'حفظ التعديلات' }}
        </button>
    </div>
</form>
@endsection
