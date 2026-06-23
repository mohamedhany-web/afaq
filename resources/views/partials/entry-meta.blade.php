@php
    use App\Support\EntryAudit;
    $entry = $entry ?? EntryAudit::payload($record ?? null);
    $title = $title ?? 'بيانات الإدخال';
@endphp
<div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 space-y-3 font-tajawal {{ $class ?? '' }}">
    <p class="text-xs font-bold text-gray-500">{{ $title }}</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
        <div>
            <span class="block text-[11px] font-bold text-gray-400 mb-0.5">تاريخ الإدخال</span>
            <span class="font-semibold text-gray-900">{{ $entry['date'] ?? '—' }}</span>
        </div>
        <div>
            <span class="block text-[11px] font-bold text-gray-400 mb-0.5">وقت الإدخال</span>
            <span class="font-semibold text-gray-900" dir="ltr">{{ $entry['time'] ?? '—' }}</span>
        </div>
        <div>
            <span class="block text-[11px] font-bold text-gray-400 mb-0.5">اسم المدخل</span>
            <span class="inline-flex items-center gap-1.5 flex-wrap">
                <strong class="font-semibold {{ !empty($entry['is_admin']) ? 'text-amber-700' : 'text-gray-800' }}">{{ $entry['creator'] ?? '—' }}</strong>
                @if(!empty($entry['is_admin']))
                <span class="px-1.5 py-px rounded text-[10px] font-bold bg-amber-100 text-amber-800">إدارة</span>
                @endif
            </span>
        </div>
    </div>
</div>
