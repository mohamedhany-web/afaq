{{-- فلتر GET — يمرَّر $action وحقول $fields --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6 font-tajawal">
    <form method="GET" action="{{ $action }}" class="flex flex-col lg:flex-row gap-3 lg:items-end flex-wrap">
        @foreach($fields as $field)
        <div class="{{ $field['class'] ?? 'flex-1 min-w-[140px]' }}">
            <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ $field['label'] }}</label>
            @if(($field['type'] ?? 'text') === 'select')
            <select name="{{ $field['name'] }}" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                @foreach($field['options'] as $val => $optLabel)
                <option value="{{ $val }}" @selected(request($field['name']) == $val && $val !== '')>{{ $optLabel }}</option>
                @endforeach
            </select>
            @elseif(($field['type'] ?? '') === 'date')
            <input type="date" name="{{ $field['name'] }}" value="{{ request($field['name']) }}" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
            @else
            <input type="text" name="{{ $field['name'] }}" value="{{ request($field['name']) }}" placeholder="{{ $field['placeholder'] ?? '' }}" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
            @endif
        </div>
        @endforeach
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold" style="background:linear-gradient(135deg,{{ $themeColor }} 0%,{{ $themeColor }}dd 100%);">تطبيق</button>
        @if(collect($fields)->pluck('name')->filter(fn ($n) => request($n))->isNotEmpty())
        <a href="{{ $action }}" class="px-5 py-2.5 rounded-xl border text-sm font-semibold text-gray-600 hover:bg-gray-50">إعادة تعيين</a>
        @endif
    </form>
</div>
