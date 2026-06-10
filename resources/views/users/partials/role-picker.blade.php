@php
    $selected = old('role', $selected ?? '');
    $inputName = $inputName ?? 'role';
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3" x-data="{ selected: @js($selected) }">
    @foreach($assignableRoles as $role)
    @php $meta = \App\Services\CrmRoleCatalogService::roleMeta($role->name); $color = $meta['color'] ?? '#4f46e5'; @endphp
    <label class="cursor-pointer block" @click="selected = @js($role->name)">
        <input type="radio" name="{{ $inputName }}" value="{{ $role->name }}" class="sr-only"
               @checked($selected === $role->name) @change="selected = @js($role->name)" required>
        <div class="p-4 rounded-2xl border-2 bg-white transition-all h-full"
             :class="selected === @js($role->name) ? 'shadow-md' : 'border-gray-200'"
             :style="selected === @js($role->name) ? 'border-color: {{ $color }}; background: {{ $color }}08' : ''">
            <div class="flex items-start gap-3">
                <span class="w-3 h-3 rounded-full mt-1 shrink-0" style="background: {{ $color }}"></span>
                <div>
                    <p class="font-bold text-gray-900 text-sm">{{ $meta['label'] ?? $role->name }}</p>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $meta['description'] ?? '' }}</p>
                </div>
            </div>
        </div>
    </label>
    @endforeach
</div>
@error('role')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
