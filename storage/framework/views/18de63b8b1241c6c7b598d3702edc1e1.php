
<?php $__env->startSection('page-title', 'عملاء محتملون'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'العملاء المحتملون — التسويق',
    'subtitle' => 'Leads من الحملات والقنوات',
    'actionUrl' => route('marketing.leads.create'),
    'actionLabel' => 'إضافة Lead',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-3 gap-3 mb-4">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الإجمالي', 'value' => $stats['total'], 'accent' => 'purple'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['today'], 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الشهر', 'value' => $stats['month'], 'accent' => 'green'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث..." class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
    <select name="campaign_id" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
        <option value="">كل الحملات</option>
        <?php $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('campaign_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm" style="background:<?php echo e($themeColor); ?>">تصفية</button>
</form>

<div class="bg-white rounded-2xl shadow-lg border overflow-x-auto">
    <table class="w-full text-sm font-tajawal">
        <thead class="bg-gray-50 text-gray-600"><tr><th class="px-4 py-3 text-right">الاسم</th><th class="px-4 py-3 text-right">الهاتف</th><th class="px-4 py-3 text-right">الحملة</th><th class="px-4 py-3 text-right">المصدر</th><th class="px-4 py-3 text-right">التاريخ</th></tr></thead>
        <tbody class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold"><?php echo e($lead->name); ?></td>
                <td class="px-4 py-3"><?php echo e($lead->phone); ?></td>
                <td class="px-4 py-3"><?php echo e($lead->marketingCampaign?->name ?? '—'); ?></td>
                <td class="px-4 py-3"><?php echo e(config('marketing.lead_sources.'.$lead->lead_source, $lead->lead_source ?? '—')); ?></td>
                <td class="px-4 py-3 text-gray-500"><?php echo e($lead->created_at->format('Y-m-d')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">لا leads.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?php echo e($leads->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/marketing/leads/index.blade.php ENDPATH**/ ?>