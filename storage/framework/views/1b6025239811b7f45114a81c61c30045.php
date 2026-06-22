
<?php $__env->startSection('page-title', 'سجل حذف العملاء'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'سجل عمليات حذف العملاء',
    'subtitle' => 'تقرير بالحذف الفردي والجماعي — مع السبب والمستخدم والتاريخ',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl border p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end font-tajawal text-sm">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">من تاريخ</label>
            <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="border rounded-xl px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">إلى تاريخ</label>
            <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="border rounded-xl px-3 py-2">
        </div>
        <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-semibold" style="background: <?php echo e($themeColor); ?>;">تصفية</button>
        <a href="<?php echo e(route('crm.clients.deletions.index')); ?>" class="px-4 py-2 rounded-xl border text-sm">إعادة ضبط</a>
    </form>
</div>

<div class="bg-white rounded-2xl border overflow-hidden">
    <table class="w-full text-sm font-tajawal">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="p-4 text-right">#</th>
                <th class="p-4 text-right">التاريخ والوقت</th>
                <th class="p-4 text-right">عدد العملاء</th>
                <th class="p-4 text-right">المستخدم</th>
                <th class="p-4 text-right">سبب الحذف</th>
                <th class="p-4 text-right"></th>
            </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="border-t hover:bg-gray-50">
            <td class="p-4"><?php echo e($batch->id); ?></td>
            <td class="p-4"><?php echo e($batch->created_at->format('Y/m/d H:i')); ?></td>
            <td class="p-4">
                <span class="px-2 py-1 rounded-lg text-xs font-bold <?php echo e($batch->isBulk() ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'); ?>">
                    <?php echo e($batch->clients_count); ?> <?php echo e($batch->isBulk() ? '(حذف جماعي)' : '(فردي)'); ?>

                </span>
            </td>
            <td class="p-4"><?php echo e($batch->user?->name ?? '—'); ?></td>
            <td class="p-4 max-w-xs truncate" title="<?php echo e($batch->delete_reason); ?>"><?php echo e(\Illuminate\Support\Str::limit($batch->delete_reason, 80)); ?></td>
            <td class="p-4">
                <a href="<?php echo e(route('crm.clients.deletions.show', $batch)); ?>" class="text-xs font-bold" style="color: <?php echo e($themeColor); ?>;">التفاصيل</a>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6" class="p-10 text-center text-gray-400">لا توجد عمليات حذف مسجّلة.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php if($batches->hasPages()): ?>
    <div class="p-4 border-t"><?php echo e($batches->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\deletions\index.blade.php ENDPATH**/ ?>