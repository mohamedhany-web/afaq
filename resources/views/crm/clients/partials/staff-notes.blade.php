@php
    use App\Models\ClientStaffNote;
    $noteTypes = ClientStaffNote::TYPES;
@endphp
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        ملاحظات الموظفين على بيانات العميل
        <p class="text-xs font-normal text-gray-500 mt-1">نصيحة أو طلب تعديل من فريق المبيعات — مرئية للفريق المعني</p>
    </div>

    <div class="p-5 sm:p-6 space-y-4">
        <form action="{{ route('crm.clients.staff-notes.store', $client) }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">نوع الملاحظة</label>
                    <select name="type" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                        @foreach($noteTypes as $key => $label)
                        <option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">الملاحظة *</label>
                <textarea name="body" rows="3" required placeholder="مثال: رقم الهاتف يحتاج تحديث — أو: العميل يفضّل التواصل مساءً..."
                          class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal resize-none">{{ old('body') }}</textarea>
                @error('body')<p class="text-xs text-red-600 mt-1 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white font-tajawal"
                    style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                إضافة ملاحظة
            </button>
        </form>

        @if($client->staffNotes->isNotEmpty())
        <div class="border-t border-gray-100 pt-4 space-y-3 max-h-80 overflow-y-auto">
            @foreach($client->staffNotes as $note)
            <article class="p-3 rounded-xl border {{ $note->type === ClientStaffNote::TYPE_EDIT_REQUEST ? 'border-amber-200 bg-amber-50/60' : 'border-gray-100 bg-gray-50/50' }}">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold font-tajawal
                        {{ $note->type === ClientStaffNote::TYPE_EDIT_REQUEST ? 'bg-amber-100 text-amber-800' : 'bg-blue-50 text-blue-700' }}">
                        {{ $note->typeLabel() }}
                    </span>
                    <time class="text-[11px] text-gray-400 font-tajawal" datetime="{{ $note->created_at->toIso8601String() }}">
                        {{ $note->created_at->format('Y/m/d') }} · {{ $note->created_at->format('H:i') }}
                    </time>
                </div>
                <p class="text-sm text-gray-800 font-tajawal whitespace-pre-line">{{ $note->body }}</p>
                @if($note->user)
                <p class="text-[11px] text-gray-500 mt-2 font-tajawal">بواسطة: <strong>{{ $note->user->name }}</strong></p>
                @endif
            </article>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400 font-tajawal text-center py-2">لا توجد ملاحظات بعد — أضف نصيحة أو طلب تعديل للفريق.</p>
        @endif
    </div>
</div>
