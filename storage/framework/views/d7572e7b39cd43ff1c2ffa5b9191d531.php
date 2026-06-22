

<?php $__env->startSection('page-title', 'تقرير الموظف'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">تقرير الموظف</h1>
                <p class="text-gray-600">تقرير شامل عن أداء الموظف وساعات العمل</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    تصدير PDF
                </button>
                <a href="<?php echo e(route('employees.index')); ?>" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Employee Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6 space-x-reverse">
                <div class="h-20 w-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-2xl font-bold text-white"><?php echo e(substr($employee->first_name, 0, 1)); ?></span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900"><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></h2>
                    <p class="text-gray-600"><?php echo e($employee->position); ?></p>
                    <p class="text-sm text-gray-500"><?php echo e($employee->department->name ?? 'غير محدد'); ?></p>
                </div>
            </div>
            <div class="text-left">
                <div class="text-sm text-gray-500">تاريخ التوظيف</div>
                <div class="text-lg font-semibold text-gray-900"><?php echo e($employee->hire_date->format('Y/m/d')); ?></div>
                <div class="text-sm text-gray-500">الراتب الشهري: $<?php echo e(number_format($employee->salary)); ?></div>
                <div class="text-sm text-gray-500">الساعات اليومية: <?php echo e($employee->daily_hours ?? 8); ?> ساعة</div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Work Days -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">أيام العمل</p>
                    <p class="text-3xl font-bold text-gray-900">22</p>
                    <p class="text-xs text-blue-600 mt-1">هذا الشهر</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Hours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الساعات</p>
                    <p class="text-3xl font-bold text-gray-900">176</p>
                    <p class="text-xs text-green-600 mt-1">هذا الشهر</p>
                </div>
                <div class="p-4 bg-green-50 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">معدل الحضور</p>
                    <p class="text-3xl font-bold text-gray-900">95%</p>
                    <p class="text-xs text-purple-600 mt-1">ممتاز</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-xl">
                    <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Leaves Taken -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">أيام الإجازة</p>
                    <p class="text-3xl font-bold text-gray-900">3</p>
                    <p class="text-xs text-orange-600 mt-1">هذا الشهر</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-xl">
                    <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Hours Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">ساعات العمل الأسبوعية</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">الأسبوع الأول</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-2 ml-3">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">40h</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">الأسبوع الثاني</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-2 ml-3">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 95%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">38h</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">الأسبوع الثالث</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-2 ml-3">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">34h</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">الأسبوع الرابع</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-2 ml-3">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">40h</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Calendar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">تقويم الحضور</h3>
            <div class="grid grid-cols-7 gap-1 text-center">
                <div class="text-xs text-gray-500 py-2">أحد</div>
                <div class="text-xs text-gray-500 py-2">اثن</div>
                <div class="text-xs text-gray-500 py-2">ثلاث</div>
                <div class="text-xs text-gray-500 py-2">أربع</div>
                <div class="text-xs text-gray-500 py-2">خمس</div>
                <div class="text-xs text-gray-500 py-2">جمعة</div>
                <div class="text-xs text-gray-500 py-2">سبت</div>
                
                <!-- Calendar days would go here -->
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">1</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">2</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">3</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">4</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">5</div>
                <div class="h-8 bg-gray-100 rounded flex items-center justify-center text-xs">6</div>
                <div class="h-8 bg-gray-100 rounded flex items-center justify-center text-xs">7</div>
                
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">8</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">9</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">10</div>
                <div class="h-8 bg-orange-100 rounded flex items-center justify-center text-xs">11</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">12</div>
                <div class="h-8 bg-gray-100 rounded flex items-center justify-center text-xs">13</div>
                <div class="h-8 bg-gray-100 rounded flex items-center justify-center text-xs">14</div>
                
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">15</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">16</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">17</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">18</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">19</div>
                <div class="h-8 bg-gray-100 rounded flex items-center justify-center text-xs">20</div>
                <div class="h-8 bg-gray-100 rounded flex items-center justify-center text-xs">21</div>
                
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">22</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">23</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">24</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">25</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">26</div>
                <div class="h-8 bg-gray-100 rounded flex items-center justify-center text-xs">27</div>
                <div class="h-8 bg-gray-100 rounded flex items-center justify-center text-xs">28</div>
                
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">29</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">30</div>
                <div class="h-8 bg-green-100 rounded flex items-center justify-center text-xs">31</div>
                <div class="h-8"></div>
                <div class="h-8"></div>
                <div class="h-8"></div>
                <div class="h-8"></div>
            </div>
            <div class="flex items-center justify-center gap-4 mt-4 text-xs">
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-green-100 rounded"></div>
                    <span>حاضر</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-orange-100 rounded"></div>
                    <span>متأخر</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-gray-100 rounded"></div>
                    <span>إجازة</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">سجل الحضور الأخير</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وقت الحضور</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وقت الانصراف</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ساعات العمل</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">2024/01/20</td>
                        <td class="px-6 py-4 text-sm text-gray-900">09:00</td>
                        <td class="px-6 py-4 text-sm text-gray-900">17:00</td>
                        <td class="px-6 py-4 text-sm text-gray-900">8 ساعات</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                مكتمل
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">2024/01/19</td>
                        <td class="px-6 py-4 text-sm text-gray-900">09:15</td>
                        <td class="px-6 py-4 text-sm text-gray-900">16:45</td>
                        <td class="px-6 py-4 text-sm text-gray-900">7.5 ساعة</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                ناقص
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\employees\report.blade.php ENDPATH**/ ?>