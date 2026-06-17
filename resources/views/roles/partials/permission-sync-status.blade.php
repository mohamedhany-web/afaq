@if(!empty($permissionSyncReport))
    @php
        $total = $permissionSyncReport['total_db'] ?? 0;
        $inModules = $permissionSyncReport['total_ui_modules'] ?? 0;
        $uncategorized = $permissionSyncReport['not_in_ui_modules'] ?? [];
        $dbOnly = $permissionSyncReport['in_db_only'] ?? [];
    @endphp
    <div class="mb-6 rounded-xl border px-4 py-3 text-sm font-tajawal {{ ($uncategorized === [] && $dbOnly === []) ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-amber-50 border-amber-200 text-amber-900' }}">
        <p class="font-semibold">
            صلاحيات النظام: {{ $total }} في قاعدة البيانات — {{ $inModules }} معروضة في المصفوفة
        </p>
        @if($uncategorized !== [])
            <p class="mt-2 text-xs">
                غير مصنّفة في الواجهة ({{ count($uncategorized) }}):
                <span dir="ltr" class="font-mono">{{ implode(', ', array_slice($uncategorized, 0, 8)) }}{{ count($uncategorized) > 8 ? '…' : '' }}</span>
                — أضفها في <code>config/crm_roles.php</code>
            </p>
        @endif
        @if($dbOnly !== [])
            <p class="mt-1 text-xs">
                في قاعدة البيانات فقط وغير موجودة في السجل المركزي — أضفها في <code>config/permissions.php</code>
            </p>
        @endif
        @if($uncategorized === [] && $dbOnly === [])
            <p class="mt-1 text-xs text-blue-700">جميع صلاحيات النظام معروضة هنا. لأي ميزة جديدة: أضف المفتاح في <code>config/permissions.php</code> ثم في <code>permission_modules</code>.</p>
        @endif
    </div>
@endif
