@extends('layouts.app')

@section('page-title', 'مستندات العملاء المشتركة')

@section('content')
<div class="w-full max-w-full">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">مستندات العملاء المشتركة</h1>
            <p class="text-gray-600 text-sm sm:text-base">رفع عقود وعروض وتسليمات تظهر في بوابة العميل.</p>
        </div>
        <a href="{{ route('client-shared-documents.create') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700">رفع مستند</a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm">
            </div>
            <div>
                @include('partials.client-search-select', ['required' => false, 'value' => request('client_id'), 'inputClass' => 'w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm', 'crmScope' => false, 'placeholder' => 'ابحث لتصفية حسب عميل...'])
            </div>
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold">تصفية</button>
                <a href="{{ route('client-shared-documents.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold">العنوان</th>
                    <th class="px-4 py-3 text-right font-semibold">العميل</th>
                    <th class="px-4 py-3 text-right font-semibold">التاريخ</th>
                    <th class="px-4 py-3 text-left font-semibold w-40"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($documents as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $doc->title }}</td>
                        <td class="px-4 py-3"><a href="{{ route('clients.show', $doc->client) }}" class="text-blue-600 hover:underline">{{ $doc->client->name }}</a></td>
                        <td class="px-4 py-3 text-gray-600">{{ $doc->created_at->format('Y/m/d') }}</td>
                        <td class="px-4 py-3 text-left space-x-2 space-x-reverse">
                            <a href="{{ route('client-shared-documents.download', $doc) }}" class="text-blue-600 font-semibold hover:underline">تنزيل</a>
                            <form method="POST" action="{{ route('client-shared-documents.destroy', $doc) }}" class="inline" onsubmit="return confirm('حذف المستند؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-semibold hover:underline mr-2">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-12 text-center text-gray-500">لا توجد مستندات.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t bg-gray-50">{{ $documents->links() }}</div>
    </div>
</div>
@endsection
