@extends('layouts.developer')
@section('page-title', 'بيانات الشركة')
@section('content')
<h1 class="text-2xl font-bold mb-6">بيانات الشركة</h1>
@if($developer->activeContract)<div class="mb-4 p-4 rounded-xl bg-blue-50 text-sm">التعاقد: {{ $developer->activeContract->contract_ref ?? 'نشط' }} — العمولة {{ $developer->activeContract->commission_percent ?? '—' }}% (للقراءة فقط من الإدارة)</div>@endif
<form method="POST" action="{{ route('developer.profile.update') }}">@csrf @method('PUT')
<div class="bg-white rounded-2xl border p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div><label class="text-xs font-bold text-gray-500">الهاتف</label><input name="phone" value="{{ old('phone', $developer->phone) }}" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div><label class="text-xs font-bold text-gray-500">البريد</label><input name="email" value="{{ old('email', $developer->email) }}" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div><label class="text-xs font-bold text-gray-500">الموقع</label><input name="website" value="{{ old('website', $developer->website) }}" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div><label class="text-xs font-bold text-gray-500">المدينة</label><input name="city" value="{{ old('city', $developer->city) }}" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div class="sm:col-span-2"><label class="text-xs font-bold text-gray-500">العنوان</label><input name="address" value="{{ old('address', $developer->address) }}" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div class="sm:col-span-2"><label class="text-xs font-bold text-gray-500">نبذة</label><textarea name="description" rows="4" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1">{{ old('description', $developer->description) }}</textarea></div>
</div>
<button class="mt-4 px-6 py-3 rounded-xl text-white font-bold" style="background:var(--brand)">حفظ</button>
</form>
@endsection
