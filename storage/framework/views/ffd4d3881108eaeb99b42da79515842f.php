
<?php $__env->startSection('page-title', 'قوالب KPI'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $roleLabels = config('compensation.target_role_labels', []);
    $periodLabels = config('compensation.evaluation_period_labels', []);
    $repTemplates = $templates->where('target_role', 'rep');
    $mgrTemplates = $templates->where('target_role', 'manager');
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'قوالب مؤشرات الأداء',
    'subtitle' => 'إنشاء وتعديل KPI — تطبيق على الجميع أو موظف محدد',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
    'actionUrl' => route('crm.compensation.kpi.create'),
    'actionLabel' => 'قالب جديد',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-800 text-sm font-tajawal border border-green-200"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 max-w-lg">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قوالب المندوبين', 'value' => $repTemplates->count(), 'compact' => true, 'accent' => 'blue', 'href' => route('crm.compensation.kpi.index') . '#page-data', 'linkLabel' => 'عرض القوالب'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قوالب المديرين', 'value' => $mgrTemplates->count(), 'compact' => true, 'accent' => 'purple', 'href' => route('crm.compensation.kpi.index') . '#page-data', 'linkLabel' => 'عرض القوالب'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="flex flex-wrap gap-2 mb-6 font-tajawal text-sm">
    <a href="<?php echo e(route('crm.compensation.kpi.create', ['role' => 'rep'])); ?>"
       class="px-4 py-2 rounded-xl border-2 font-semibold hover:shadow-sm transition-all"
       style="border-color:<?php echo e($themeColor); ?>40;color:<?php echo e($themeColor); ?>">+ قالب مندوب</a>
    <a href="<?php echo e(route('crm.compensation.kpi.create', ['role' => 'manager'])); ?>"
       class="px-4 py-2 rounded-xl border-2 border-gray-200 font-semibold hover:bg-gray-50">+ قالب مدير</a>
    <a href="<?php echo e(route('crm.compensation.profiles.index')); ?>" class="px-4 py-2 rounded-xl text-gray-600 border border-gray-200 hover:bg-gray-50 mr-auto">ربط يدوي بالموظفين ←</a>
</div>

<?php $__currentLoopData = [['label' => 'مندوبي المبيعات', 'items' => $repTemplates], ['label' => 'مديرو المبيعات', 'items' => $mgrTemplates]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($section['items']->isNotEmpty()): ?>
    <div class="mb-8">
        <h2 class="font-bold text-gray-800 mb-3 font-tajawal"><?php echo e($section['label']); ?></h2>
        <div class="space-y-4">
            <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $assigned = $tpl->employee_profiles_count; ?>
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap justify-between gap-3 items-start" style="<?php echo e($headerStyle); ?>">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-lg text-gray-900"><?php echo e($tpl->name); ?></h3>
                            <?php if($tpl->is_active): ?>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-bold">نشط</span>
                            <?php else: ?>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-bold">موقوف</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            <?php echo e($roleLabels[$tpl->target_role] ?? $tpl->target_role); ?>

                            · <?php echo e($periodLabels[$tpl->evaluation_period] ?? $tpl->evaluation_period); ?>

                            · <?php echo e($tpl->items_count); ?> مؤشر
                            · <strong><?php echo e($assigned); ?></strong> موظف
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="<?php echo e(route('crm.compensation.kpi.edit', $tpl)); ?>"
                           class="px-4 py-2 rounded-xl text-white text-xs font-bold shadow-sm"
                           style="background:linear-gradient(135deg,<?php echo e($themeColor); ?>,<?php echo e($themeColor); ?>dd)">تعديل</a>
                        <button type="button"
                                onclick="document.getElementById('assign-<?php echo e($tpl->id); ?>').classList.toggle('hidden')"
                                class="px-4 py-2 rounded-xl border-2 text-xs font-bold hover:bg-gray-50"
                                style="border-color:<?php echo e($themeColor); ?>40;color:<?php echo e($themeColor); ?>">
                            تطبيق على الموظفين
                        </button>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2">
                    <?php $__currentLoopData = $tpl->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-3 text-center">
                        <p class="text-xs font-bold text-gray-800 leading-snug"><?php echo e($item->name); ?></p>
                        <p class="text-[10px] text-gray-500 mt-1 tabular-nums"><?php echo e($item->weight); ?>% · هدف <?php echo e(number_format($item->target_value, 0)); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div id="assign-<?php echo e($tpl->id); ?>" class="hidden border-t border-gray-100 p-5 bg-gray-50/50">
                    <form method="POST" action="<?php echo e(route('crm.compensation.kpi.assign', $tpl)); ?>" class="font-tajawal text-sm space-y-3 max-w-xl">
                        <?php echo csrf_field(); ?>
                        <p class="font-bold text-gray-700">تطبيق سريع بدون تعديل المؤشرات</p>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="apply_assignment" value="all_role" checked class="rounded-full">
                            <span>جميع <?php echo e($tpl->target_role === 'manager' ? 'مديري' : 'مندوبي'); ?> المبيعات</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="apply_assignment" value="selected" class="rounded-full" onchange="document.getElementById('emps-<?php echo e($tpl->id); ?>').classList.toggle('hidden', !this.checked)">
                            <span>موظفون محددون</span>
                        </label>
                        <div id="emps-<?php echo e($tpl->id); ?>" class="hidden grid grid-cols-2 gap-1 max-h-32 overflow-y-auto border rounded-lg p-2 bg-white">
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-1 text-xs">
                                <input type="checkbox" name="employee_ids[]" value="<?php echo e($emp->id); ?>" class="rounded">
                                <?php echo e($emp->name); ?>

                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-lg text-white text-xs font-bold" style="background:<?php echo e($themeColor); ?>">تطبيق</button>
                    </form>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($templates->isEmpty()): ?>
<div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center font-tajawal">
    <p class="text-gray-500 mb-4">لا توجد قوالب KPI بعد</p>
    <a href="<?php echo e(route('crm.compensation.kpi.create')); ?>" class="inline-flex px-6 py-3 rounded-xl text-white font-semibold text-sm"
       style="background:<?php echo e($themeColor); ?>">إنشاء أول قالب</a>
</div>
<?php endif; ?>

<a href="<?php echo e(route('crm.compensation.dashboard')); ?>" class="inline-block mt-4 text-sm font-tajawal" style="color:<?php echo e($themeColor); ?>">← لوحة التعويضات</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\compensation\admin\kpi\index.blade.php ENDPATH**/ ?>