@extends('layouts.app')

@section('page-title', 'تعديل التذكرة')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white mb-6 shadow-lg">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold mb-2">تعديل التذكرة</h1>
            <p class="text-blue-100">تعديل بيانات التذكرة: {{ $ticket->ticket_number }}</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-all duration-200 flex items-center">
            <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات التذكرة</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">الموضوع</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject', $ticket->subject) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('subject') border-red-500 @enderror">
                        @error('subject')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $ticket->description) }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        @include('partials.client-search-select', ['required' => true, 'value' => old('client_id', $ticket->client_id), 'label' => $ticket->client ? \App\Http\Controllers\ClientSearchController::formatLabel($ticket->client) : null, 'inputClass' => 'w-full px-3 py-2 border border-gray-300 rounded-lg', 'crmScope' => false])
                        @error('client_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">المكلف</label>
                        <select name="assigned_to" id="assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">اختر المكلف</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $ticket->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">الأولوية</label>
                        <select name="priority" id="priority" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('priority') border-red-500 @enderror">
                            <option value="low" {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>منخفضة</option>
                            <option value="medium" {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>متوسطة</option>
                            <option value="high" {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>عالية</option>
                            <option value="critical" {{ old('priority', $ticket->priority) == 'critical' ? 'selected' : '' }}>حرجة</option>
                        </select>
                        @error('priority')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                        <select name="category" id="category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror">
                            <option value="technical" {{ old('category', $ticket->category) == 'technical' ? 'selected' : '' }}>تقني</option>
                            <option value="billing" {{ old('category', $ticket->category) == 'billing' ? 'selected' : '' }}>فواتير</option>
                            <option value="general" {{ old('category', $ticket->category) == 'general' ? 'selected' : '' }}>عام</option>
                            <option value="bug_report" {{ old('category', $ticket->category) == 'bug_report' ? 'selected' : '' }}>تقرير خطأ</option>
                            <option value="feature_request" {{ old('category', $ticket->category) == 'feature_request' ? 'selected' : '' }}>طلب ميزة</option>
                        </select>
                        @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            <option value="open" {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>مفتوح</option>
                            <option value="in_progress" {{ old('status', $ticket->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="pending_client" {{ old('status', $ticket->status) == 'pending_client' ? 'selected' : '' }}>في انتظار العميل</option>
                            <option value="resolved" {{ old('status', $ticket->status) == 'resolved' ? 'selected' : '' }}>محلول</option>
                            <option value="closed" {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>مغلق</option>
                        </select>
                        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                <a href="{{ route('tickets.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    إلغاء
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

