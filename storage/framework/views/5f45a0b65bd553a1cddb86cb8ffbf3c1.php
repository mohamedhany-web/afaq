<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $repName = $client->assignedSalesRepName();
?>
<?php if($repName): ?>
<div class="mb-6 rounded-2xl border-2 overflow-hidden shadow-sm font-tajawal"
     style="border-color: <?php echo e($themeColor); ?>40; background: linear-gradient(135deg, <?php echo e($themeColor); ?>12 0%, <?php echo e($themeColor); ?>05 100%);">
    <div class="px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shrink-0"
                 style="background: <?php echo e($themeColor); ?>;">
                <?php echo e(mb_substr($repName, 0, 1)); ?>

            </div>
            <div>
                <p class="text-xs font-bold text-gray-500">مسؤول المبيعات (السيلز)</p>
                <?php if($client->assignedEmployee?->user): ?>
                <a href="<?php echo e(route('crm.team-members.show', $client->assignedEmployee->user)); ?>"
                   class="text-lg font-extrabold text-gray-900 hover:underline" style="color: <?php echo e($themeColor); ?>;">
                    <?php echo e($repName); ?>

                </a>
                <?php else: ?>
                <p class="text-lg font-extrabold text-gray-900"><?php echo e($repName); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if(($assignableReps ?? collect())->isNotEmpty() && auth()->user()->can('transfer', $client)): ?>
        <form method="POST" action="<?php echo e(route('crm.clients.transfer', $client)); ?>"
              class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-end min-w-[220px]"
              onsubmit="return confirm('تحويل هذا العميل إلى المندوب المحدد؟')">
            <?php echo csrf_field(); ?>
            <div class="flex-1">
                <label class="block text-[10px] font-bold text-gray-500 mb-1">تحويل / سحب إلى مندوب</label>
                <select name="employee_id" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm bg-white">
                    <?php $__currentLoopData = $assignableReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($rep->employee?->id && (int) $rep->employee->id !== (int) $client->assigned_to): ?>
                    <option value="<?php echo e($rep->employee->id); ?>"><?php echo e($rep->name); ?></option>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold text-white whitespace-nowrap"
                    style="background: <?php echo e($themeColor); ?>;">تحويل</button>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php elseif(($assignableReps ?? collect())->isNotEmpty() && auth()->user()->can('transfer', $client)): ?>
<div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 font-tajawal">
    <p class="text-sm text-amber-900 mb-3 font-semibold">العميل غير مُعيَّن لمندوب مبيعات بعد</p>
    <form method="POST" action="<?php echo e(route('crm.clients.transfer', $client)); ?>" class="flex flex-wrap gap-2 items-end">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">تعيين إلى مندوب</label>
            <select name="employee_id" required class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm min-w-[200px]">
                <?php $__currentLoopData = $assignableReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($rep->employee?->id); ?>"><?php echo e($rep->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold text-white" style="background: <?php echo e($themeColor); ?>;">تعيين</button>
    </form>
</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\partials\sales-rep-card.blade.php ENDPATH**/ ?>