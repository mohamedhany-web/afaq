
<?php $__env->startSection('page-title', 'عقود الموظفين'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $statusColors = [
        'draft' => 'bg-gray-100 text-gray-800',
        'active' => 'bg-green-100 text-green-800',
        'expired' => 'bg-amber-100 text-amber-800',
        'terminated' => 'bg-red-100 text-red-800',
    ];
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'عقود الموظفين',
    'subtitle' => 'إدارة عقود العمل — رفع المستندات ومتابعة انتهاء الصلاحية',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عقود سارية', 'value' => $stats['active'], 'accent' => 'green', 'href' => route('hr.contracts.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'عرض السارية'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تنتهي خلال 30 يوم', 'value' => $stats['expiring'], 'accent' => 'amber', 'href' => route('hr.contracts.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'متابعة الانتهاء'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مسودات', 'value' => $stats['draft'], 'accent' => 'theme', 'href' => route('hr.contracts.index', ['status' => 'draft']) . '#page-data', 'linkLabel' => 'عرض المسودات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="page-data" class="bg-white rounded-2xl border shadow-lg overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-lg font-bold">سجل العقود</h3>
        <div class="flex flex-wrap gap-2">
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث..." class="border rounded-xl px-3 py-2 text-sm">
                <select name="status" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <option value="">كل الحالات</option>
                    <?php $__currentLoopData = config('hr_contracts.status_labels', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(request('status') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="employee_id" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <option value="">كل الموظفين</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
            <button type="button" onclick="document.getElementById('newContractModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">عقد جديد</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-right">رقم العقد</th>
                    <th class="px-5 py-3 text-right">الموظف</th>
                    <th class="px-5 py-3 text-right">العنوان</th>
                    <th class="px-5 py-3 text-right">النوع</th>
                    <th class="px-5 py-3 text-right">الفترة</th>
                    <th class="px-5 py-3 text-right">الحالة</th>
                    <th class="px-5 py-3 text-right">ملف</th>
                    <th class="px-5 py-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/80"
                    data-contract-id="<?php echo e($contract->id); ?>"
                    data-title="<?php echo e($contract->title); ?>"
                    data-contract-type="<?php echo e($contract->contract_type); ?>"
                    data-start-date="<?php echo e($contract->start_date->format('Y-m-d')); ?>"
                    data-end-date="<?php echo e(optional($contract->end_date)->format('Y-m-d')); ?>"
                    data-salary="<?php echo e($contract->salary); ?>"
                    data-status="<?php echo e($contract->status); ?>"
                    data-terms="<?php echo e($contract->terms); ?>"
                    data-notes="<?php echo e($contract->notes); ?>"
                    data-update-url="<?php echo e(route('hr.contracts.update', $contract)); ?>">
                    <td class="px-5 py-4 font-mono text-xs"><?php echo e($contract->contract_number); ?></td>
                    <td class="px-5 py-4 font-semibold"><?php echo e($contract->employee?->first_name); ?> <?php echo e($contract->employee?->last_name); ?></td>
                    <td class="px-5 py-4"><?php echo e($contract->title); ?></td>
                    <td class="px-5 py-4"><?php echo e($contract->typeLabel()); ?></td>
                    <td class="px-5 py-4 text-gray-600">
                        <?php echo e($contract->start_date->format('Y/m/d')); ?>

                        <?php if($contract->end_date): ?> — <?php echo e($contract->end_date->format('Y/m/d')); ?><?php endif; ?>
                        <?php if($contract->isExpiringSoon()): ?><span class="block text-xs text-amber-700">ينتهي قريباً</span><?php endif; ?>
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-lg text-xs font-semibold <?php echo e($statusColors[$contract->status] ?? 'bg-gray-100 text-gray-800'); ?>"><?php echo e($contract->statusLabel()); ?></span>
                    </td>
                    <td class="px-5 py-4">
                        <?php if($contract->file_path): ?>
                        <a href="<?php echo e(route('hr.contracts.download', $contract)); ?>" class="text-xs font-bold hover:underline" style="color:<?php echo e($themeColor); ?>">تحميل</a>
                        <?php else: ?> — <?php endif; ?>
                    </td>
                    <td class="px-5 py-4">
                        <button type="button" onclick="openEditContract(this.closest('tr'))" class="text-xs font-bold text-blue-600">تعديل</button>
                        <form method="POST" action="<?php echo e(route('hr.contracts.destroy', $contract)); ?>" class="inline" onsubmit="return confirm('حذف العقد؟')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-xs font-bold text-red-600 mr-2">حذف</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="px-5 py-16 text-center text-gray-500">لا توجد عقود مسجّلة</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($contracts->hasPages()): ?><div class="px-5 py-4 border-t"><?php echo e($contracts->links()); ?></div><?php endif; ?>
</div>

<div id="newContractModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('newContractModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 overflow-y-auto">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border my-8">
            <div class="px-5 py-4 border-b font-bold">عقد موظف جديد</div>
            <form method="POST" action="<?php echo e(route('hr.contracts.store')); ?>" enctype="multipart/form-data" class="p-5 space-y-3">
                <?php echo csrf_field(); ?>
                <?php echo $__env->make('hr.contracts.partials.form-fields', ['employees' => $employees, 'contractTypes' => $contractTypes], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('newContractModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">حفظ العقد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editContractModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('editContractModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 overflow-y-auto">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border my-8">
            <div class="px-5 py-4 border-b font-bold">تعديل العقد</div>
            <form id="editContractForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-3">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div id="editContractFields"></div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('editContractModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditContract(row) {
    const d = row.dataset;
    document.getElementById('editContractForm').action = d.updateUrl;
    document.getElementById('editContractFields').innerHTML = `
        <div><label class="block text-sm font-bold mb-1">العنوان</label><input name="title" value="${d.title || ''}" required class="w-full border rounded-xl px-3 py-2 text-sm"></div>
        <div><label class="block text-sm font-bold mb-1">نوع العقد</label><select name="contract_type" class="w-full border rounded-xl px-3 py-2 text-sm"><?php $__currentLoopData = $contractTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-sm font-bold mb-1">تاريخ البداية</label><input type="date" name="start_date" value="${d.startDate || ''}" required class="w-full border rounded-xl px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-bold mb-1">تاريخ النهاية</label><input type="date" name="end_date" value="${d.endDate || ''}" class="w-full border rounded-xl px-3 py-2 text-sm"></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-sm font-bold mb-1">الراتب</label><input type="number" step="0.01" name="salary" value="${d.salary || ''}" class="w-full border rounded-xl px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-bold mb-1">الحالة</label><select name="status" class="w-full border rounded-xl px-3 py-2 text-sm"><?php $__currentLoopData = config('hr_contracts.status_labels', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        </div>
        <div><label class="block text-sm font-bold mb-1">الشروط</label><textarea name="terms" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm">${d.terms || ''}</textarea></div>
        <div><label class="block text-sm font-bold mb-1">ملاحظات</label><textarea name="notes" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm">${d.notes || ''}</textarea></div>
        <div><label class="block text-sm font-bold mb-1">ملف العقد (اختياري)</label><input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm"></div>
    `;
    document.querySelector('#editContractFields select[name="contract_type"]').value = d.contractType;
    document.querySelector('#editContractFields select[name="status"]').value = d.status;
    document.getElementById('editContractModal').classList.remove('hidden');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\hr\contracts\index.blade.php ENDPATH**/ ?>