
<?php $__env->startSection('page-title', 'استلام وتسليم العهد'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'استلام وتسليم العهد',
    'subtitle' => 'تسجيل عهد الموظفين — أجهزة، مفاتيح، معدات — ومتابعة التسليم',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal"><?php echo e(session('error')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عهدة نشطة', 'value' => $stats['active'], 'accent' => 'theme', 'href' => route('hr.custody.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'عرض النشطة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تسليمات (الشهر)', 'value' => $stats['returned_month'], 'accent' => 'green', 'href' => route('hr.custody.index', ['status' => 'returned']) . '#page-data', 'linkLabel' => 'عرض المُسلّمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'موظفون لديهم عهدة', 'value' => $stats['employees_with_custody'], 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="page-data" class="bg-white rounded-2xl border shadow-lg overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-lg font-bold">سجل العهد</h3>
        <div class="flex flex-wrap gap-2">
            <form method="GET" class="flex flex-wrap gap-2">
                <select name="status" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <?php $__currentLoopData = config('custody.status_labels', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(request('status', 'active') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="employee_id" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <option value="">كل الموظفين</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
            <button type="button" onclick="document.getElementById('issueModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">تسجيل استلام</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-right">الموظف</th>
                    <th class="px-5 py-3 text-right">العهدة</th>
                    <th class="px-5 py-3 text-right">التصنيف</th>
                    <th class="px-5 py-3 text-right">الرقم التسلسلي</th>
                    <th class="px-5 py-3 text-right">تاريخ الاستلام</th>
                    <th class="px-5 py-3 text-right">الحالة</th>
                    <th class="px-5 py-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/80 align-top">
                    <td class="px-5 py-4 font-semibold"><?php echo e($assignment->employee?->first_name); ?> <?php echo e($assignment->employee?->last_name); ?></td>
                    <td class="px-5 py-4"><?php echo e($assignment->item_name); ?></td>
                    <td class="px-5 py-4"><?php echo e($assignment->categoryLabel()); ?></td>
                    <td class="px-5 py-4 text-gray-600"><?php echo e($assignment->serial_number ?? '—'); ?></td>
                    <td class="px-5 py-4"><?php echo e($assignment->issued_at->format('Y/m/d')); ?></td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-lg text-xs font-semibold <?php echo e($assignment->status === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'); ?>"><?php echo e($assignment->statusLabel()); ?></span>
                        <?php if($assignment->returned_at): ?><p class="text-xs text-gray-500 mt-1">تسليم: <?php echo e($assignment->returned_at->format('Y/m/d')); ?></p><?php endif; ?>
                    </td>
                    <td class="px-5 py-4">
                        <?php if($assignment->issue_file_path): ?>
                        <a href="<?php echo e(route('hr.custody.issue-file', $assignment)); ?>" class="block text-xs font-bold mb-1" style="color:<?php echo e($themeColor); ?>">إيصال استلام</a>
                        <?php endif; ?>
                        <?php if($assignment->return_file_path): ?>
                        <a href="<?php echo e(route('hr.custody.return-file', $assignment)); ?>" class="block text-xs font-bold mb-1 text-green-700">إيصال تسليم</a>
                        <?php endif; ?>
                        <?php if($assignment->isActive()): ?>
                        <button type="button" onclick="openReturnModal(<?php echo e($assignment->id); ?>, '<?php echo e(addslashes($assignment->item_name)); ?>')" class="text-xs font-bold text-red-600">تسجيل تسليم</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="px-5 py-16 text-center text-gray-500">لا توجد سجلات عهدة</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($assignments->hasPages()): ?><div class="px-5 py-4 border-t"><?php echo e($assignments->links()); ?></div><?php endif; ?>
</div>

<div id="issueModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('issueModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4 overflow-y-auto">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border my-8">
            <div class="px-5 py-4 border-b font-bold">تسجيل استلام عهدة</div>
            <form method="POST" action="<?php echo e(route('hr.custody.store')); ?>" enctype="multipart/form-data" class="p-5 space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-bold mb-1">الموظف</label>
                    <select name="employee_id" required class="w-full border rounded-xl px-3 py-2 text-sm">
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">ربط بأصل مسجّل (اختياري)</label>
                    <select name="asset_id" class="w-full border rounded-xl px-3 py-2 text-sm">
                        <option value="">— بدون —</option>
                        <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($asset->id); ?>"><?php echo e($asset->name); ?> (<?php echo e($asset->asset_tag); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">اسم العهدة</label>
                    <input type="text" name="item_name" required class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="مثال: لابتوب Dell Latitude">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-bold mb-1">التصنيف</label>
                        <select name="category" required class="w-full border rounded-xl px-3 py-2 text-sm">
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">الرقم التسلسلي</label>
                        <input type="text" name="serial_number" class="w-full border rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-bold mb-1">تاريخ الاستلام</label>
                        <input type="date" name="issued_at" required value="<?php echo e(now()->toDateString()); ?>" class="w-full border rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">حالة العهدة</label>
                        <select name="issue_condition" class="w-full border rounded-xl px-3 py-2 text-sm">
                            <option value="">—</option>
                            <?php $__currentLoopData = $conditions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">ملاحظات الاستلام</label>
                    <textarea name="issue_notes" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">إيصال استلام (اختياري)</label>
                    <input type="file" name="issue_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm">
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('issueModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">تسجيل الاستلام</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="returnModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('returnModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border">
            <div class="px-5 py-4 border-b font-bold">تسجيل تسليم عهدة — <span id="returnItemName"></span></div>
            <form id="returnForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-bold mb-1">تاريخ التسليم</label>
                    <input type="date" name="returned_at" required value="<?php echo e(now()->toDateString()); ?>" class="w-full border rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">حالة العهدة عند التسليم</label>
                    <select name="return_condition" class="w-full border rounded-xl px-3 py-2 text-sm">
                        <option value="">—</option>
                        <?php $__currentLoopData = $conditions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">نتيجة التسليم</label>
                    <select name="status" required class="w-full border rounded-xl px-3 py-2 text-sm">
                        <option value="returned">مُسلّم</option>
                        <option value="lost">مفقود</option>
                        <option value="damaged">تالف</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">ملاحظات</label>
                    <textarea name="return_notes" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">إيصال تسليم (اختياري)</label>
                    <input type="file" name="return_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm">
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">تأكيد التسليم</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReturnModal(id, name) {
    document.getElementById('returnItemName').textContent = name;
    document.getElementById('returnForm').action = '<?php echo e(url('hr/custody')); ?>/' + id + '/return';
    document.getElementById('returnModal').classList.remove('hidden');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\hr\custody\index.blade.php ENDPATH**/ ?>