
<?php $__env->startSection('page-title', 'تفاصيل حذف #' . $batch->id); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تفاصيل عملية الحذف #' . $batch->id,
    'subtitle' => $batch->created_at->format('Y/m/d H:i') . ' — ' . ($batch->user?->name ?? '—'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6 font-tajawal">
    <div class="bg-white rounded-2xl border p-5">
        <p class="text-xs text-gray-500">عدد العملاء المحذوفين</p>
        <p class="text-3xl font-bold mt-1" style="color: <?php echo e($themeColor); ?>;"><?php echo e($batch->clients_count); ?></p>
    </div>
    <div class="bg-white rounded-2xl border p-5 lg:col-span-2">
        <p class="text-xs text-gray-500">سبب الحذف</p>
        <p class="text-sm text-gray-800 mt-2 whitespace-pre-line"><?php echo e($batch->delete_reason); ?></p>
    </div>
</div>

<div class="bg-white rounded-2xl border overflow-hidden">
    <div class="px-5 py-4 border-b font-bold">العملاء المحذوفون (<?php echo e(count($batch->clients_snapshot ?? [])); ?>)</div>
    <table class="w-full text-sm font-tajawal">
        <thead class="bg-gray-50 border-b"><tr>
            <th class="p-3 text-right">#</th>
            <th class="p-3 text-right">الاسم</th>
            <th class="p-3 text-right">الهاتف</th>
        </tr></thead>
        <tbody>
        <?php $__currentLoopData = $batch->clients_snapshot ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="border-t">
            <td class="p-3 text-gray-500"><?php echo e($row['id'] ?? '—'); ?></td>
            <td class="p-3 font-semibold"><?php echo e($row['name'] ?? '—'); ?></td>
            <td class="p-3" dir="ltr"><?php echo e($row['phone'] ?? '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <a href="<?php echo e(route('crm.clients.deletions.index')); ?>" class="text-sm font-semibold" style="color: <?php echo e($themeColor); ?>;">← العودة للسجل</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\deletions\show.blade.php ENDPATH**/ ?>