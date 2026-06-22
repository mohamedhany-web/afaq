

<?php $__env->startSection('page-title', 'الأصول والممتلكات'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">الأصول والممتلكات</h1>
                <p class="text-sm sm:text-base text-gray-600">إدارة أصول الشركة وممتلكاتها</p>
            </div>
            <button class="bg-blue-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                <svg class="h-4 w-4 sm:h-5 sm:w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="hidden sm:inline">أصل جديد</span>
                <span class="sm:hidden">جديد</span>
            </button>
        </div>
    </div>

    <!-- Coming Soon Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
        <div class="max-w-md mx-auto">
            <div class="h-16 w-16 sm:h-20 sm:w-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="h-8 w-8 sm:h-10 sm:w-10 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">قريباً</h2>
            <p class="text-gray-600 mb-6">وحدة الأصول والممتلكات قيد التطوير وسيتم إطلاقها قريباً</p>
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-sm text-purple-800">
                    <strong>المميزات القادمة:</strong><br>
                    • تسجيل الأصول<br>
                    • تتبع الصيانة<br>
                    • إدارة المخزون<br>
                    • تقارير القيمة
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\assets\index.blade.php ENDPATH**/ ?>