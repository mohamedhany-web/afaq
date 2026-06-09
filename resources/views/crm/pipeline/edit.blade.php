@extends('layouts.app')
@section('page-title', 'تعديل صفقة')

@section('content')
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal'; @endphp
@include('crm.partials.page-header', ['title' => 'تعديل صفقة', 'subtitle' => $sale->product_service, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />'])
<form action="{{ route('crm.pipeline.update', $sale) }}" method="POST" class="max-w-3xl space-y-4">
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 sm:p-8 space-y-4">
    @csrf @method('PUT')
    <div>@include('partials.client-search-select', ['required' => true, 'value' => old('client_id', $sale->client_id), 'label' => $sale->client ? \App\Http\Controllers\ClientSearchController::formatLabel($sale->client) : null, 'inputClass' => $input, 'crmScope' => true])</div>
    <div><label class="block text-sm font-bold text-gray-700 mb-2">المشروع</label><select name="project_id" class="{{ $input }}"><option value="">—</option>@foreach($projects as $p)<option value="{{ $p->id }}" @selected($sale->project_id==$p->id)>{{ $p->name }}</option>@endforeach</select></div>
    <div><label class="block text-sm font-bold text-gray-700 mb-2">الوصف</label><input name="product_service" value="{{ old('product_service', $sale->product_service) }}" required class="{{ $input }}"></div>
    <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-bold text-gray-700 mb-2">القيمة (جنيه مصري)</label><input name="estimated_value" type="number" value="{{ old('estimated_value', $sale->estimated_value) }}" required class="{{ $input }}"></div>
        <div><label class="block text-sm font-bold text-gray-700 mb-2">الاحتمالية</label><input name="probability_percentage" type="number" value="{{ old('probability_percentage', $sale->probability_percentage) }}" required class="{{ $input }}"></div>
    </div>
    <div><label class="block text-sm font-bold text-gray-700 mb-2">المرحلة</label><select name="stage" class="{{ $input }}">@foreach($stages as $s)<option value="{{ $s }}" @selected($sale->stage==$s)>{{ $s }}</option>@endforeach</select></div>
    <button type="submit" class="px-6 py-3 rounded-xl text-white font-semibold" style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">تحديث</button>
</div>
@include('crm.pipeline.partials.commission-fields', ['sale' => $sale, 'agents' => $agents, 'transactionTypes' => $transactionTypes])
</form>
@endsection
