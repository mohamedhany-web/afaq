
<?php $__env->startSection('page-title', 'جدول هيكل العمولات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'جدول هيكل العمولات',
    'subtitle' => 'Agents Commission Scheme — تقسيم عمولة الشركة المحصّلة بين الوكيل والشركة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />',
    'actionUrl' => route('crm.freelance-agents.index'),
    'actionLabel' => 'عقود الوكلاء',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="mb-4 p-4 rounded-2xl border text-sm font-tajawal text-gray-600" style="border-color:<?php echo e($themeColor); ?>30;background:<?php echo e($themeColor); ?>05;">
    يعتمد النظام على تقسيم <strong>إجمالي عمولة الشركة</strong> المحصّلة من المطور أو البائع — وليس على قيمة الصفقة مباشرة. يُدخل مبلغ عمولة الشركة عند إغلاق الصفقة في مسار المبيعات.
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 sm:px-6 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">Commission Scheme Table</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px]">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600">
                    <th class="text-right p-4 font-tajawal font-bold">نوع العملية</th>
                    <th class="text-right p-4 font-tajawal font-bold">الشروط / الحالة</th>
                    <th class="text-center p-4 font-tajawal font-bold">نسبة الوكيل</th>
                    <th class="text-center p-4 font-tajawal font-bold">نسبة الشركة</th>
                    <th class="text-right p-4 font-tajawal font-bold">الصرف</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 font-tajawal">
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50/80">
                    <td class="p-4 font-semibold text-gray-900">
                        <?php if($row['type'] === 'primary_target'): ?>
                            مبيعات المطورين (Primary) — تارجت
                        <?php else: ?>
                            <?php echo e(config('freelance_agents.transaction_types')[$row['type']] ?? $row['type']); ?>

                        <?php endif; ?>
                    </td>
                    <td class="p-4 text-gray-600 text-xs leading-relaxed max-w-xs"><?php echo e($row['condition']); ?></td>
                    <td class="p-4 text-center font-bold tabular-nums" style="color:<?php echo e($themeColor); ?>"><?php echo e($row['agent_rate']); ?></td>
                    <td class="p-4 text-center font-semibold text-gray-700 tabular-nums"><?php echo e($row['company_rate']); ?></td>
                    <td class="p-4 text-xs text-gray-500"><?php echo e($row['payout']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm font-tajawal">
    <div class="bg-white rounded-2xl border p-5">
        <h3 class="font-bold mb-2">موعد الصرف</h3>
        <p class="text-gray-600">خلال <?php echo e(config('freelance_agents.payout_days_min')); ?> إلى <?php echo e(config('freelance_agents.payout_days_max')); ?> يوم عمل من دخول عمولة الشركة في الحساب البنكي أو تحصيلها نقداً.</p>
    </div>
    <div class="bg-white rounded-2xl border p-5">
        <h3 class="font-bold mb-2">تارجت Primary</h3>
        <p class="text-gray-600">عند تحقيق التارجت الربع سنوي في عقد الوكيل ترتفع نسبة مبيعات المطورين من 40% إلى 50% من عمولة الشركة.</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\freelance-agents\scheme.blade.php ENDPATH**/ ?>