@props([
    'name' => 'client_id',
    'id' => null,
    'value' => null,
    'label' => null,
    'required' => false,
    'inputClass' => 'w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal',
    'crmScope' => null,
    'placeholder' => 'ابحث بالاسم، الهاتف، البريد، أو الشركة...',
    'wrapperClass' => '',
])

@php
    $fieldId = $id ?? $name;
    $selectedId = old($name, $value ?? '');
    $selectedLabel = $label;
    if (!$selectedLabel && $selectedId) {
        $prefill = \App\Models\Client::find($selectedId);
        $selectedLabel = $prefill ? \App\Http\Controllers\ClientSearchController::formatLabel($prefill) : '';
    }
    if ($crmScope === null) {
        $user = auth()->user();
        $crmScope = $user && $user->canAccessCrm() && $user->usesCrmWorkspace();
    }
    $clientSearchUrl = ($crmScope ?? false) ? route('crm.clients.search') : route('clients.search');
@endphp

<div
    class="client-search-select relative font-tajawal {{ $wrapperClass }}"
    x-data="clientSearchSelect({
        name: @js($name),
        selectedId: @js((string) $selectedId),
        selectedLabel: @js($selectedLabel),
        searchUrl: @js($clientSearchUrl),
        crmScope: @js((bool) $crmScope),
        required: @js((bool) $required),
    })"
>
    <input type="hidden" name="{{ $name }}" x-model="selectedId" @if($required) required @endif>

    <label class="block text-xs font-bold text-gray-500 mb-1" for="{{ $fieldId }}-query">العميل @if($required)<span class="text-red-500">*</span>@endif</label>

    <div x-show="selectedId" x-cloak class="mb-2 flex items-center gap-2 flex-wrap">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-900 text-sm font-semibold border border-blue-100 max-w-full">
            <span class="truncate" x-text="selectedLabel"></span>
            <button type="button" @click="clear()" class="text-blue-600 hover:text-red-600 shrink-0" title="إزالة الاختيار">&times;</button>
        </span>
    </div>

    <div class="flex gap-2">
        <input
            type="text"
            id="{{ $fieldId }}-query"
            x-ref="queryInput"
            x-model="query"
            @input.debounce.350ms="search()"
            @keydown.enter.prevent="search()"
            @keydown.escape="open = false"
            @focus="if (results.length) { open = true; $nextTick(() => positionDropdown()); }"
            class="{{ $inputClass }} flex-1"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
        >
        <button
            type="button"
            @click="search()"
            class="shrink-0 px-4 py-2 rounded-xl text-white text-sm font-bold shadow-sm hover:opacity-90 transition"
            style="background: {{ \App\Helpers\SettingsHelper::getThemeColor() }};"
        >
            <span x-show="!loading">بحث</span>
            <span x-show="loading" x-cloak>...</span>
        </button>
    </div>

    <p class="text-[11px] text-gray-400 mt-1" x-show="!selectedId">اكتب حرفين على الأقل ثم اضغط «بحث» أو Enter</p>

    <template x-teleport="body">
        <div
            x-ref="dropdownPanel"
            x-show="open && results.length"
            x-cloak
            :style="dropdownStyle"
            class="max-h-60 overflow-y-auto bg-white border-2 border-gray-200 rounded-xl shadow-2xl font-tajawal"
        >
            <template x-for="client in results" :key="client.id">
                <button
                    type="button"
                    @click="select(client)"
                    class="w-full text-right px-4 py-2.5 text-sm hover:bg-gray-50 border-b border-gray-100 last:border-0"
                >
                    <span class="font-semibold text-gray-900" x-text="client.name"></span>
                    <span class="text-gray-500 text-xs block" x-text="client.phone || client.label"></span>
                </button>
            </template>
        </div>
    </template>

    <p x-show="open && searched && !results.length && !loading" x-cloak class="text-sm text-gray-500 mt-2">لا توجد نتائج.</p>
</div>
