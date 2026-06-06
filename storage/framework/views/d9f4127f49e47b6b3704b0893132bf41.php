
<?php $__env->startSection('page-title', 'مهمة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $priorityColors = config('crm_tasks.priority_colors', []);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'إنشاء مهمة مبيعات',
    'subtitle' => 'تعيين مهمة قابلة للقياس مع موعد نهائي — تظهر فوراً في لوحة المكلف',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => route('crm.tasks.index'),
    'actionLabel' => 'كل المهام',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 font-tajawal text-sm text-red-800">
    <p class="font-bold mb-2">يرجى تصحيح الحقول التالية:</p>
    <ul class="list-disc list-inside space-y-1">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?php echo e(route('crm.tasks.store')); ?>" method="POST"
      class="w-full font-tajawal space-y-6"
      x-data="{
          priority: <?php echo \Illuminate\Support\Js::from(old('priority', 'medium'))->toHtml() ?>,
          priorityColors: <?php echo \Illuminate\Support\Js::from($priorityColors)->toHtml() ?>,
          priorityLabels: <?php echo \Illuminate\Support\Js::from(config('crm_tasks.priority_labels', []))->toHtml() ?>,
      }">
    <?php echo csrf_field(); ?>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 items-start">
        <div class="xl:col-span-8 space-y-6">
            <?php echo $__env->make('crm.tasks.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <aside class="xl:col-span-4 space-y-4 xl:sticky xl:top-24">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>12 0%, <?php echo e($themeColor); ?>04 100%);">
                    <h3 class="font-bold text-gray-900">معاينة الأولوية</h3>
                </div>
                <div class="p-5">
                    <div class="h-2 rounded-full mb-3 transition-colors"
                         :style="'background:' + (priorityColors[priority] || '<?php echo e($themeColor); ?>')"></div>
                    <p class="text-sm font-bold text-gray-800" x-text="priorityLabels[priority] || priority"></p>
                    <p class="text-xs text-gray-500 mt-2">ستُرتب المهمة في قائمة المكلف حسب الأولوية والموعد.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 text-sm text-gray-600 space-y-3">
                <h3 class="font-bold text-gray-900">إرشادات سريعة</h3>
                <p>• اجعل العنوان فعلياً وقابلاً للقياس (مثال: «5 معاينات اليوم»).</p>
                <p>• اربط المهمة بعميل أو صفقة عند الإمكان.</p>
                <p>• الموعد النهائي إلزامي — يُستخدم للتذكير والتصعيد.</p>
                <p>• «يتطلب القبول» يناسب المهام التي يجب أن يوافق عليها الموظف أولاً.</p>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-900">
                <strong>تنبيه:</strong> الحد الأقصى <?php echo e(config('crm_tasks.max_open_tasks_per_user', 25)); ?> مهمة نشطة لكل موظف. عند التجاوز سيُرفض التعيين.
            </div>
        </aside>
    </div>

    <div class="sticky bottom-0 z-20 -mx-3 sm:-mx-4 lg:-mx-6 xl:-mx-8 px-3 sm:px-4 lg:px-6 xl:px-8 py-4 bg-gray-50/95 backdrop-blur border-t border-gray-200">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
            <a href="<?php echo e(route('crm.tasks.index')); ?>"
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-white font-tajawal">
                إلغاء
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl text-white font-bold text-sm shadow-lg hover:shadow-xl transition-all font-tajawal"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                تعيين المهمة
            </button>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/tasks/create.blade.php ENDPATH**/ ?>