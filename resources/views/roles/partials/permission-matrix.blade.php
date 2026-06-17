{{--
    مصفوفة صلاحيات CRUD
    المتغيرات: $permissionModules, $checkedPermissions (array), $rolePermissions (optional), $customPermissionsMap (optional), $inputName (default permissions[])
--}}
@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $inputName = $inputName ?? 'permissions[]';
    $rolePermissions = $rolePermissions ?? [];
    $customPermissionsMap = $customPermissionsMap ?? [];
    $showSource = $showSource ?? true;
@endphp

<input type="text" id="permissionSearch" placeholder="ابحث عن وحدة أو صلاحية…"
       class="w-full mb-6 border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-tajawal"
       onkeyup="filterPermissionMatrix()">

@foreach($permissionGroups as $groupKey => $group)
    <div class="mb-8 permission-group-block" data-group="{{ $groupKey }}">
        <h4 class="text-sm font-bold text-gray-800 mb-4 font-tajawal border-r-4 pr-3" style="border-color: {{ $themeColor }};">
            {{ $group['label'] }}
        </h4>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm font-tajawal permission-matrix-table min-w-[640px]">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-right px-4 py-3 w-1/3">الوحدة</th>
                        <th class="text-center px-3 py-3">عرض</th>
                        <th class="text-center px-3 py-3">إنشاء</th>
                        <th class="text-center px-3 py-3">تعديل</th>
                        <th class="text-center px-3 py-3">حذف</th>
                        <th class="text-right px-4 py-3">صلاحيات إضافية</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($group['modules'] ?? [] as $module)
                        <tr class="permission-module-row hover:bg-gray-50/50" data-module-label="{{ $module['label'] }}">
                            <td class="px-4 py-3 font-semibold text-gray-900 module-label">{{ $module['label'] }}</td>
                            @foreach(['view', 'create', 'edit', 'delete'] as $action)
                                @php
                                    $permKey = $module[$action] ?? null;
                                @endphp
                                <td class="px-3 py-3 text-center">
                                    @if($permKey)
                                        @php
                                            $isChecked = in_array($permKey, $checkedPermissions);
                                            $isFromRole = in_array($permKey, $rolePermissions);
                                            $hasOverride = isset($customPermissionsMap[$permKey]);
                                            $isDisabled = $hasOverride && !$customPermissionsMap[$permKey];
                                        @endphp
                                        <label class="inline-flex flex-col items-center gap-1 cursor-pointer permission-cell" data-perm="{{ $permKey }}">
                                            <input type="checkbox" name="{{ $inputName }}" value="{{ $permKey }}" class="rounded border-gray-300"
                                                   {{ $isChecked ? 'checked' : '' }}>
                                            @if($showSource && $isFromRole && !$hasOverride)
                                                <span class="text-[9px] text-green-600">دور</span>
                                            @elseif($showSource && $hasOverride && $isChecked)
                                                <span class="text-[9px] text-blue-600">+</span>
                                            @elseif($showSource && $isDisabled)
                                                <span class="text-[9px] text-red-600">−</span>
                                            @endif
                                        </label>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-3">
                                @if(!empty($module['extras']))
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($module['extras'] as $extraKey => $extraLabel)
                                            @php
                                                $permKey = is_int($extraKey) ? $extraLabel : $extraKey;
                                                $label = is_int($extraKey) ? \App\Helpers\RoleHelper::getPermissionName($extraLabel) : $extraLabel;
                                                $isChecked = in_array($permKey, $checkedPermissions);
                                                $isFromRole = in_array($permKey, $rolePermissions);
                                                $hasOverride = isset($customPermissionsMap[$permKey]);
                                            @endphp
                                            <label class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg border text-xs permission-cell {{ $isChecked ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}" data-perm="{{ $permKey }}">
                                                <input type="checkbox" name="{{ $inputName }}" value="{{ $permKey }}" {{ $isChecked ? 'checked' : '' }}>
                                                <span class="permission-name">{{ $label }}</span>
                                                @if($showSource && $isFromRole && !$hasOverride)
                                                    <span class="text-[9px] text-green-600">دور</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

<script>
function filterPermissionMatrix() {
    const q = (document.getElementById('permissionSearch')?.value || '').toLowerCase();
    document.querySelectorAll('.permission-module-row').forEach(row => {
        const label = (row.dataset.moduleLabel || '').toLowerCase();
        const cells = Array.from(row.querySelectorAll('.permission-cell')).map(c => (c.dataset.perm || '').toLowerCase());
        const match = label.includes(q) || cells.some(p => p.includes(q));
        row.style.display = match ? '' : 'none';
    });
}
function selectAllPermissions() {
    document.querySelectorAll('input[name="{{ $inputName }}"]').forEach(c => c.checked = true);
}
function deselectAllPermissions() {
    document.querySelectorAll('input[name="{{ $inputName }}"]').forEach(c => c.checked = false);
}
</script>
