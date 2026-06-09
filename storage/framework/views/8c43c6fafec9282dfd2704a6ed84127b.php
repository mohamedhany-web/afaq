
<?php $__env->startSection('page-title', $campaign->name); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $campaign->name,
    'subtitle' => $campaign->channelLabel() . ' — ' . $campaign->statusLabel(),
    'actionUrl' => route('marketing.leads.create', ['campaign_id' => $campaign->id]),
    'actionLabel' => 'إضافة Lead',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'Leads', 'value' => $campaign->leads_count, 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'المهام', 'value' => $campaign->activities_count, 'accent' => 'purple'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الميزانية', 'value' => number_format($campaign->budget ?? 0), 'accent' => 'amber'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'المصروف', 'value' => number_format($campaign->spent_amount ?? 0), 'accent' => 'theme'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border p-5 font-tajawal space-y-3">
        <p><strong>المدير:</strong> <?php echo e($campaign->manager?->name ?? '—'); ?></p>
        <p><strong>المشروع:</strong> <?php echo e($campaign->project?->name ?? '—'); ?></p>
        <p><strong>الفترة:</strong> <?php echo e($campaign->start_date?->format('Y-m-d') ?? '—'); ?> → <?php echo e($campaign->end_date?->format('Y-m-d') ?? '—'); ?></p>
        <?php if($campaign->description): ?><p class="text-sm text-gray-600"><?php echo e($campaign->description); ?></p><?php endif; ?>
        <div class="flex gap-2 pt-2">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-marketing')): ?><a href="<?php echo e(route('marketing.campaigns.edit', $campaign)); ?>" class="px-4 py-2 rounded-xl border text-sm">تعديل</a><?php endif; ?>
            <a href="<?php echo e(route('marketing.activities.create', ['campaign_id' => $campaign->id])); ?>" class="px-4 py-2 rounded-xl text-white text-sm" style="background:<?php echo e($themeColor); ?>">مهمة جديدة</a>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border p-5 font-tajawal">
        <h3 class="font-bold mb-3">آخر المهام</h3>
        <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <p class="text-sm mb-2"><?php echo e($act->title); ?> <span class="text-gray-400">· <?php echo e($act->statusLabel()); ?></span></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-sm text-gray-500">لا مهام.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\campaigns\show.blade.php ENDPATH**/ ?>