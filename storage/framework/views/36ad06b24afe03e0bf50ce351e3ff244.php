<?php $__env->startSection('page-title', 'التقارير والتحليل'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-reports')): ?>
    <div class="mb-6 rounded-xl border p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="background: <?php echo e($themeColor); ?>10; border-color: <?php echo e($themeColor); ?>30;">
        <div>
            <h2 class="text-lg font-bold text-gray-900">تقارير النظام الشاملة (CRM + Excel)</h2>
            <p class="text-sm text-gray-600 mt-1">عملاء، مسار مبيعات، مهام، متابعات، مشاريع عقارية، تعويضات، ومستخدمون — مع تصدير Excel.</p>
        </div>
        <a href="<?php echo e(route('admin.system-reports.index')); ?>" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-white font-medium whitespace-nowrap" style="background-color: <?php echo e($themeColor); ?>">فتح مركز التقارير</a>
    </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">التقارير والتحليل</h1>
                <p class="text-gray-600">تقارير الموارد البشرية والمالية التقليدية</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الموظفين</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['total_employees']); ?></p>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">المشاريع النشطة</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['active_projects']); ?></p>
                    <p class="text-xs text-gray-500 mt-1">من <?php echo e($stats['total_projects']); ?> مشروع</p>
                </div>
                <div class="p-3 bg-green-50 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي العملاء</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['total_clients']); ?></p>
                </div>
                <div class="p-3 bg-purple-50 rounded-xl">
                    <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المبيعات</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_sales'], 0)); ?> ج.م</p>
                </div>
                <div class="p-3 bg-orange-50 rounded-xl">
                    <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Employees Report -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        موارد بشرية
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">تقرير الموظفين</h3>
                <p class="text-sm text-gray-600 mt-2">تقرير شامل عن الموظفين والرواتب والأقسام</p>
            </div>
            <div class="p-6">
                <a href="<?php echo e(route('reports.employees')); ?>" class="flex items-center justify-center w-full px-4 py-3 rounded-lg transition-colors duration-200 font-medium" style="background-color: #2563eb; color: white;" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">
                    عرض التقرير
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Attendance Report -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        حضور
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">تقرير الحضور</h3>
                <p class="text-sm text-gray-600 mt-2">تقرير الحضور والانصراف وساعات العمل</p>
            </div>
            <div class="p-6">
                <a href="<?php echo e(route('reports.attendance')); ?>" class="flex items-center justify-center w-full px-4 py-3 rounded-lg transition-colors duration-200 font-medium" style="background-color: #7c3aed; color: white;" onmouseover="this.style.backgroundColor='#6d28d9'" onmouseout="this.style.backgroundColor='#7c3aed'">
                    عرض التقرير
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Sales Report -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        مبيعات
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">تقرير المبيعات</h3>
                <p class="text-sm text-gray-600 mt-2">تقرير شامل عن المبيعات والإيرادات</p>
            </div>
            <div class="p-6">
                <a href="<?php echo e(route('reports.sales')); ?>" class="flex items-center justify-center w-full px-4 py-3 rounded-lg transition-colors duration-200 font-medium" style="background-color: #ea580c; color: white;" onmouseover="this.style.backgroundColor='#c2410c'" onmouseout="this.style.backgroundColor='#ea580c'">
                    عرض التقرير
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Salaries Report -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-teal-50 to-cyan-50 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <svg class="w-8 h-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                        رواتب
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">تقرير الرواتب</h3>
                <p class="text-sm text-gray-600 mt-2">تقرير تفصيلي عن الرواتب والبدلات</p>
            </div>
            <div class="p-6">
                <a href="<?php echo e(route('reports.salaries')); ?>" class="flex items-center justify-center w-full px-4 py-3 rounded-lg transition-colors duration-200 font-medium" style="background-color: #0d9488; color: white;" onmouseover="this.style.backgroundColor='#0f766e'" onmouseout="this.style.backgroundColor='#0d9488'">
                    عرض التقرير
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Departments Report -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        أقسام
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">تقرير الأقسام</h3>
                <p class="text-sm text-gray-600 mt-2">تقرير شامل عن أداء الأقسام المختلفة</p>
            </div>
            <div class="p-6">
                <a href="<?php echo e(route('reports.departments')); ?>" class="flex items-center justify-center w-full px-4 py-3 rounded-lg transition-colors duration-200 font-medium" style="background-color: #4f46e5; color: white;" onmouseover="this.style.backgroundColor='#4338ca'" onmouseout="this.style.backgroundColor='#4f46e5'">
                    عرض التقرير
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Performance Report -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        أداء
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">تقرير الأداء</h3>
                <p class="text-sm text-gray-600 mt-2">تقرير شامل عن أداء الموظفين والإنتاجية</p>
            </div>
            <div class="p-6">
                <a href="<?php echo e(route('reports.performance')); ?>" class="flex items-center justify-center w-full px-4 py-3 rounded-lg transition-colors duration-200 font-medium" style="background-color: #ea580c; color: white;" onmouseover="this.style.backgroundColor='#c2410c'" onmouseout="this.style.backgroundColor='#ea580c'">
                    عرض التقرير
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Financial Reports -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        مالية
                    </span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">التقارير المالية</h3>
                <p class="text-sm text-gray-600 mt-2">الميزانية وقائمة الدخل والتدفق النقدي</p>
            </div>
            <div class="p-6">
                <a href="<?php echo e(route('accounting.reports.index')); ?>" class="flex items-center justify-center w-full px-4 py-3 rounded-lg transition-colors duration-200 font-medium" style="background-color: #059669; color: white;" onmouseover="this.style.backgroundColor='#047857'" onmouseout="this.style.backgroundColor='#059669'">
                    عرض التقرير
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\reports\index.blade.php ENDPATH**/ ?>