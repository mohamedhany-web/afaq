
<?php $__env->startSection('page-title', 'هياكل التعويض'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $input = 'w-full border-2 border-gray-200 rounded-xl px-3 py-2 font-tajawal text-sm';
    $templates = \App\Models\Compensation\CompKpiTemplate::where('is_active', true)->orderBy('name')->get();
    $plans = \App\Models\Compensation\CompCommissionPlan::where('is_active', true)->orderBy('name')->get();
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'ربط التعويض والـ KPI بالموظفين',
    'subtitle' => 'تعيين قالب KPI لموظف واحد أو تعديل الراتب وخطة العمولة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
    'actionUrl' => route('crm.compensation.kpi.index'),
    'actionLabel' => 'قوالب KPI',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-800 text-sm font-tajawal border border-green-200"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b" style="<?php echo e($headerStyle); ?>"><h3 class="font-bold font-tajawal">إضافة موظف لهيكل تعويض</h3></div>
    <form method="POST" action="<?php echo e(route('crm.compensation.profiles.store')); ?>" class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 font-tajawal">
        <?php echo csrf_field(); ?>
        <div class="lg:col-span-2">
            <label class="text-xs font-bold text-gray-500 mb-1 block">الموظف</label>
            <select name="user_id" class="<?php echo e($input); ?>" required>
                <option value="">اختر موظفاً</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 mb-1 block">الراتب الأساسي</label>
            <input type="number" name="base_salary" step="0.01" min="0" class="<?php echo e($input); ?>" required>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 mb-1 block">قالب KPI</label>
            <select name="kpi_template_id" class="<?php echo e($input); ?>">
                <option value="">— بدون —</option>
                <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>"><?php echo e($t->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 mb-1 block">خطة العمولة</label>
            <select name="commission_plan_id" class="<?php echo e($input); ?>">
                <option value="">— بدون —</option>
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="lg:col-span-5">
            <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-bold shadow-sm" style="background:<?php echo e($themeColor); ?>">حفظ الربط</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b" style="<?php echo e($headerStyle); ?>">
        <h3 class="font-bold">الموظفون المرتبطون</h3>
        <p class="text-xs text-gray-500 mt-1">غيّر قالب KPI لموظف دون التأثير على الباقي</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs">
                <tr>
                    <th class="text-right p-3">الموظف</th>
                    <th class="p-3">الراتب</th>
                    <th class="p-3 min-w-[200px]">قالب KPI</th>
                    <th class="p-3">العمولة</th>
                    <th class="p-3 w-28"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50/50">
                    <td class="p-3 font-semibold text-gray-900"><?php echo e($profile->user?->name); ?></td>
                    <td class="p-3 text-center tabular-nums"><?php echo e($money($profile->base_salary)); ?></td>
                    <td class="p-3">
                        <form method="POST" action="<?php echo e(route('crm.compensation.profiles.update', $profile)); ?>" class="flex flex-col gap-2">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="base_salary" value="<?php echo e($profile->base_salary); ?>">
                            <input type="hidden" name="commission_plan_id" value="<?php echo e($profile->commission_plan_id); ?>">
                            <select name="kpi_template_id" class="<?php echo e($input); ?> text-xs">
                                <option value="">— بدون قالب —</option>
                                <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($t->id); ?>" <?php if($profile->kpi_template_id == $t->id): echo 'selected'; endif; ?>><?php echo e($t->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white w-fit" style="background:<?php echo e($themeColor); ?>">تطبيق KPI</button>
                        </form>
                    </td>
                    <td class="p-3 text-center text-xs text-gray-600"><?php echo e($profile->commissionPlan?->name ?? '—'); ?></td>
                    <td class="p-3">
                        <form method="POST" action="<?php echo e(route('crm.compensation.profiles.update', $profile)); ?>" class="space-y-1">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="kpi_template_id" value="<?php echo e($profile->kpi_template_id); ?>">
                            <input type="hidden" name="commission_plan_id" value="<?php echo e($profile->commission_plan_id); ?>">
                            <input type="number" name="base_salary" value="<?php echo e($profile->base_salary); ?>" class="<?php echo e($input); ?> text-xs w-full" step="0.01">
                            <button class="text-xs w-full py-1.5 border rounded-lg hover:bg-gray-50">تحديث الراتب</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t"><?php echo e($profiles->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/compensation/admin/profiles/index.blade.php ENDPATH**/ ?>