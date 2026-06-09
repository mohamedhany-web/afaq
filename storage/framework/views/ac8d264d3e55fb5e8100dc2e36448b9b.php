
<?php $__env->startSection('page-title', ($plan ?? null) ? 'تعديل خطة تسويق' : 'خطة تسويق شهرية'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $plan = $plan ?? null;
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $months = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => ($plan ?? null) ? 'تعديل خطة التسويق' : 'خطة تسويق شهرية جديدة',
    'subtitle' => 'توصيف الخطة، الأهداف، وربطها بحملة إن وُجدت',
    'actionUrl' => route('marketing.plans.index'),
    'actionLabel' => 'كل الخطط',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form method="POST" action="<?php echo e(($plan ?? null) ? route('marketing.plans.update', $plan) : route('marketing.plans.store')); ?>" class="font-tajawal space-y-6">
    <?php echo csrf_field(); ?>
    <?php if($plan ?? null): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold" style="<?php echo e($headerStyle); ?>">بيانات الخطة</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="<?php echo e($label); ?>">عنوان الخطة *</label>
                <input name="title" required class="<?php echo e($input); ?>" value="<?php echo e(old('title', optional($plan)->title)); ?>" placeholder="مثال: خطة تسويق يونيو 2026">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الشهر *</label>
                <select name="month" class="<?php echo e($input); ?>" required>
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m); ?>" <?php if(old('month', optional($plan)->month ?? $defaultMonth) == $m): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">السنة *</label>
                <input type="number" name="year" min="2020" max="2100" required class="<?php echo e($input); ?>" value="<?php echo e(old('year', optional($plan)->year ?? $defaultYear)); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الحملة المرتبطة</label>
                <select name="campaign_id" class="<?php echo e($input); ?>">
                    <option value="">— اختياري —</option>
                    <?php $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php if(old('campaign_id', optional($plan)->campaign_id) == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الحالة</label>
                <select name="status" class="<?php echo e($input); ?>">
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(old('status', optional($plan)->status ?? 'draft') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="<?php echo e($label); ?>">توصيف الخطة (Marketing Plan) *</label>
                <textarea name="description" rows="4" required class="<?php echo e($input); ?>" placeholder="وصف شامل لاستراتيجية التسويق لهذا الشهر..."><?php echo e(old('description', optional($plan)->description)); ?></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="<?php echo e($label); ?>">الأهداف والمؤشرات المستهدفة</label>
                <textarea name="objectives" rows="3" class="<?php echo e($input); ?>" placeholder="عدد Leads، منشورات، فعاليات، ميزانية..."><?php echo e(old('objectives', optional($plan)->objectives)); ?></textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="<?php echo e(route('marketing.plans.index')); ?>" class="px-5 py-2.5 rounded-xl border text-sm font-semibold text-gray-600">إلغاء</a>
        <button type="submit" class="px-8 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?> 0%,<?php echo e($themeColor); ?>dd 100%)">
            <?php echo e(($plan ?? null) ? 'حفظ التعديلات' : 'إنشاء الخطة'); ?>

        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/marketing/plans/form.blade.php ENDPATH**/ ?>