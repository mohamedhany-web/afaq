
<?php $__env->startSection('page-title', 'ملفات الموظفين'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'ملفات الموظفين',
    'subtitle' => 'حفظ وثائق الموظفين داخل النظام — هوية، شهادات، عقود، وتقارير',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الملفات', 'value' => $stats['total'], 'accent' => 'theme', 'href' => route('hr.documents.index') . '#page-data', 'linkLabel' => 'عرض الكل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تنتهي خلال 30 يوم', 'value' => $stats['expiring'], 'accent' => 'amber'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'موظفون لديهم ملفات', 'value' => $stats['employees_with_files'], 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="page-data" class="bg-white rounded-2xl border shadow-lg overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-lg font-bold">أرشيف الملفات</h3>
        <div class="flex flex-wrap gap-2">
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث..." class="border rounded-xl px-3 py-2 text-sm">
                <select name="employee_id" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <option value="">كل الموظفين</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($emp->id); ?>" <?php if(request('employee_id') == $emp->id): echo 'selected'; endif; ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="document_type" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                    <option value="">كل الأنواع</option>
                    <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(request('document_type') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
            <button type="button" onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">رفع ملف</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-right">الموظف</th>
                    <th class="px-5 py-3 text-right">العنوان</th>
                    <th class="px-5 py-3 text-right">النوع</th>
                    <th class="px-5 py-3 text-right">اسم الملف</th>
                    <th class="px-5 py-3 text-right">الحجم</th>
                    <th class="px-5 py-3 text-right">انتهاء</th>
                    <th class="px-5 py-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/80">
                    <td class="px-5 py-4 font-semibold">
                        <a href="<?php echo e(route('employees.dossier', $doc->employee)); ?>" class="hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($doc->employee?->first_name); ?> <?php echo e($doc->employee?->last_name); ?></a>
                    </td>
                    <td class="px-5 py-4"><?php echo e($doc->title); ?></td>
                    <td class="px-5 py-4"><?php echo e($doc->typeLabel()); ?></td>
                    <td class="px-5 py-4 text-gray-600 max-w-[10rem] truncate" title="<?php echo e($doc->original_filename); ?>"><?php echo e($doc->original_filename); ?></td>
                    <td class="px-5 py-4 text-gray-500"><?php echo e($doc->file_size ? number_format($doc->file_size / 1024, 0) . ' KB' : '—'); ?></td>
                    <td class="px-5 py-4">
                        <?php if($doc->expires_at): ?>
                            <span class="<?php echo e($doc->isExpired() ? 'text-red-700' : ($doc->isExpiringSoon() ? 'text-amber-700' : 'text-gray-600')); ?>"><?php echo e($doc->expires_at->format('Y/m/d')); ?></span>
                        <?php else: ?> — <?php endif; ?>
                    </td>
                    <td class="px-5 py-4">
                        <a href="<?php echo e(route('hr.documents.download', $doc)); ?>" class="text-xs font-bold mr-2" style="color:<?php echo e($themeColor); ?>">تحميل</a>
                        <form method="POST" action="<?php echo e(route('hr.documents.destroy', $doc)); ?>" class="inline" onsubmit="return confirm('حذف الملف؟')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-xs font-bold text-red-600">حذف</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="px-5 py-16 text-center text-gray-500">لا توجد ملفات محفوظة</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($documents->hasPages()): ?><div class="px-5 py-4 border-t"><?php echo e($documents->links()); ?></div><?php endif; ?>
</div>

<div id="uploadModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="document.getElementById('uploadModal').classList.add('hidden')"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border">
            <div class="px-5 py-4 border-b font-bold">رفع ملف لموظف</div>
            <form method="POST" action="<?php echo e(route('hr.documents.store')); ?>" enctype="multipart/form-data" class="p-5 space-y-3">
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
                    <label class="block text-sm font-bold mb-1">نوع المستند</label>
                    <select name="document_type" required class="w-full border rounded-xl px-3 py-2 text-sm">
                        <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($v); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">عنوان المستند</label>
                    <input type="text" name="title" required class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="مثال: بطاقة الهوية الوطنية">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">تاريخ انتهاء (اختياري)</label>
                    <input type="date" name="expires_at" class="w-full border rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">الملف</label>
                    <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.webp,.zip" class="w-full text-sm">
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border text-sm font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">حفظ الملف</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\hr\documents\index.blade.php ENDPATH**/ ?>