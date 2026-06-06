@extends('layouts.app')

@section('page-title', 'طلبات اجتماعات العملاء')

@section('content')
<div class="w-full max-w-full">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">طلبات اجتماعات العملاء</h1>
            <p class="text-gray-600 text-sm sm:text-base">طلبات جدولة اجتماعات وردّ الفريق عليها من بوابة العميل.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <form method="get" action="{{ route('client-meeting-requests.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="عنوان، وصف، رقم مرجعي..."
                       class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="pending" @selected(request('status')==='pending')>قيد المراجعة</option>
                    <option value="confirmed" @selected(request('status')==='confirmed')>تم التأكيد</option>
                    <option value="declined" @selected(request('status')==='declined')>مرفوض</option>
                    <option value="completed" @selected(request('status')==='completed')>مكتمل</option>
                    <option value="cancelled" @selected(request('status')==='cancelled')>ملغى</option>
                </select>
            </div>
            <div>
                @include('partials.client-search-select', ['required' => false, 'value' => request('client_id'), 'inputClass' => 'w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm', 'crmScope' => false, 'placeholder' => 'ابحث لتصفية حسب عميل...'])
            </div>
            <div class="md:col-span-4 flex flex-wrap gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">تصفية</button>
                <a href="{{ route('client-meeting-requests.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">المرجع</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">الموضوع</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">العميل</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">الموعد المفضل</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">الحالة</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($meetingRequests as $mr)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono font-semibold">{{ $mr->reference_code }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($mr->title, 55) }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('clients.show', $mr->client) }}" class="text-blue-600 hover:underline">{{ $mr->client->name }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $mr->preferred_at->format('Y/m/d H:i') }}</td>
                            <td class="px-4 py-3">{{ $mr->status_label }}</td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('client-meeting-requests.show', $mr) }}" class="text-blue-600 font-semibold hover:underline">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($meetingRequests->hasPages())
            <div class="px-4 py-3 border-t bg-gray-50">{{ $meetingRequests->links() }}</div>
        @endif
    </div>
</div>
@endsection
