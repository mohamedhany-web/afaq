<?php $__env->startSection('page-title', 'تقارير العملاء'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">تقارير العملاء</h1>
            <p class="text-gray-600 text-sm sm:text-base">رفع تقارير الخدمة من الإدارة؛ تظهر للعميل في بوابة العميل.</p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-clients')): ?>
        <a href="<?php echo e(route('client-service-reports.create')); ?>" class="inline-flex items-center justify-center bg-blue-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-blue-700 transition shadow-sm text-sm sm:text-base font-semibold">
            <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            رفع تقرير لعميل
        </a>
        <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm font-medium">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <form method="get" action="<?php echo e(route('client-service-reports.index')); ?>" class="flex flex-col sm:flex-row gap-4 sm:items-end">
            <div class="flex-1 min-w-0">
                <?php echo $__env->make('partials.client-search-select', ['required' => false, 'value' => request('client_id'), 'inputClass' => 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm', 'crmScope' => false, 'placeholder' => 'ابحث لتصفية حسب عميل...'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <button type="submit" class="inline-flex justify-center px-5 py-2.5 rounded-lg bg-gray-800 text-white text-sm font-semibold hover:bg-gray-900 transition">تطبيق</button>
            <?php if(request()->filled('client_id')): ?>
                <a href="<?php echo e(route('client-service-reports.index')); ?>" class="inline-flex justify-center px-5 py-2.5 rounded-lg border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">إلغاء التصفية</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">العنوان</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">العميل</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">رفع بواسطة</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 w-52">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 align-top">
                                <div class="font-semibold text-gray-900"><?php echo e($report->title); ?></div>
                                <?php if($report->original_filename): ?>
                                    <div class="text-xs text-gray-500 mt-0.5"><?php echo e($report->original_filename); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('clients.show', $report->client)); ?>" class="text-blue-600 hover:text-blue-800 font-medium"><?php echo e($report->client->name); ?></a>
                                <?php if($report->client->company_name): ?>
                                    <div class="text-xs text-gray-500"><?php echo e($report->client->company_name); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap"><?php echo e($report->created_at->format('Y/m/d H:i')); ?></td>
                            <td class="px-4 py-3 text-gray-600"><?php echo e($report->uploader?->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-left">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-clients')): ?>
                                    <a href="<?php echo e(route('clients.service-reports.download', [$report->client, $report])); ?>" class="text-blue-600 hover:text-blue-800 font-medium">تنزيل</a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-clients')): ?>
                                    <form method="POST" action="<?php echo e(route('client-service-reports.destroy', $report)); ?>" class="inline" onsubmit="return confirm('حذف هذا التقرير نهائياً؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">حذف</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">لا توجد تقارير بعد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($reports->hasPages()): ?>
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                <?php echo e($reports->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-service-reports\index.blade.php ENDPATH**/ ?>