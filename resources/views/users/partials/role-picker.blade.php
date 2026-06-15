@php
    $selected = old('role', $selected ?? '');
    $inputName = $inputName ?? 'role';
    $workspaceGroups = $workspaceGroups ?? \App\Services\CrmRoleCatalogService::workspaceGroups();
    $roleHints = $roleHints ?? \App\Services\CrmRoleCatalogService::roleAssignmentHints();
@endphp
<div x-data="{
    selected: @js($selected),
    hints: @js($roleHints),
    currentHint() { return this.hints[this.selected] || null; }
}">
    <div class="space-y-6">
        @foreach($workspaceGroups as $groupKey => $group)
        @php
            $groupRoles = $assignableRoles->filter(fn ($role) => in_array($role->name, $group['roles'], true));
        @endphp
        @if($groupRoles->isNotEmpty())
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-3 h-3 rounded-full shrink-0" style="background: {{ $group['color'] }}"></span>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 font-tajawal">{{ $group['label'] }}</h3>
                    <p class="text-xs text-gray-500 font-tajawal">{{ $group['description'] }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($groupRoles as $role)
                @php $meta = \App\Services\CrmRoleCatalogService::roleMeta($role->name); $color = $meta['color'] ?? $group['color']; @endphp
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
        </div>
        @endif
        @endforeach
    </div>

    <div x-show="currentHint()" x-cloak class="mt-5 p-4 rounded-2xl border-2 border-dashed font-tajawal"
         :style="currentHint() ? 'border-color:' + currentHint().color + '55; background:' + currentHint().color + '08' : ''">
        <p class="text-xs font-bold text-gray-500 mb-1">ما الذي يشمله هذا الدور؟</p>
        <p class="text-sm font-bold text-gray-900" x-text="currentHint()?.label"></p>
        <p class="text-sm text-gray-600 mt-1" x-text="currentHint()?.description"></p>
        <p class="text-xs text-gray-500 mt-2">
            القسم: <span class="font-semibold" x-text="currentHint()?.workspace_label"></span>
            <template x-if="currentHint()?.default_department">
                <span> · القسم التوظيفي: <span class="font-semibold" x-text="currentHint()?.default_department"></span></span>
            </template>
            <template x-if="currentHint()?.needs_employee">
                <span> · يُنشأ سجل موظف عند التفعيل</span>
            </template>
        </p>
    </div>
</div>
@error('role')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
