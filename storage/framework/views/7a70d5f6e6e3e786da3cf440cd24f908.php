<?php $__env->startSection('page-title', 'تفاصيل المصروف'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-6 text-white mb-6 shadow-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="h-16 w-16 bg-white/20 rounded-full flex items-center justify-center ml-4">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold mb-2"><?php echo e($expense->expense_number); ?></h1>
                <p class="text-green-100"><?php echo e($expense->description); ?></p>
            </div>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="<?php echo e(route('expenses.edit', $expense)); ?>" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-all duration-200">
                تعديل
            </a>
            <a href="<?php echo e(route('expenses.index')); ?>" class="bg-white text-green-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                العودة
            </a>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">تفاصيل المصروف</h2>
        <div class="grid grid-cols-2 gap-4">
            <div class="flex justify-between py-2 border-b">
                <span class="text-sm font-medium text-gray-500">المبلغ</span>
                <span class="text-sm font-bold text-gray-900"><?php echo e(number_format($expense->amount, 2)); ?> ج.م</span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-sm font-medium text-gray-500">التاريخ</span>
                <span class="text-sm text-gray-900"><?php echo e($expense->expense_date->format('Y/m/d')); ?></span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-sm font-medium text-gray-500">الفئة</span>
                <span class="text-sm text-gray-900"><?php echo e($expense->expense_category); ?></span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <span class="text-sm font-medium text-gray-500">الحالة</span>
                <span class="text-sm text-gray-900"><?php echo e($expense->status); ?></span>
            </div>
            <?php if($expense->vendor): ?>
            <div class="flex justify-between py-2 border-b">
                <span class="text-sm font-medium text-gray-500">المورد</span>
                <span class="text-sm text-gray-900"><?php echo e($expense->vendor->name); ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between py-2 border-b">
                <span class="text-sm font-medium text-gray-500">طريقة الدفع</span>
                <span class="text-sm text-gray-900"><?php echo e($expense->payment_method); ?></span>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\expenses\show.blade.php ENDPATH**/ ?>