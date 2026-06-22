<?php $__env->startSection('page-title', 'العملاء'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'العملاء',
    'subtitle' => 'إدارة قاعدة عملاء المبيعات العقارية',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('crm.clients.create'),
    'actionLabel' => 'عميل جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($requiresMutationApproval ?? $requiresApproval ?? false): ?>
<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900 font-tajawal">
    تعديل أو حذف العملاء يمرّ بموافقة <strong>مدير العمليات</strong>. تتبع طلباتك من <a href="<?php echo e(route('crm.clients.approvals.index')); ?>" class="font-bold underline">طلباتي — العملاء</a>.
</div>
<?php endif; ?>

<?php if(session('success')): ?>
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php $importResult = session('import_result'); ?>
<?php if($importResult && !empty($importResult['errors'])): ?>
<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm font-tajawal">
    <p class="font-bold text-amber-900 mb-2">تفاصيل الصفوف الفاشلة:</p>
    <ul class="space-y-1 text-amber-800 max-h-40 overflow-y-auto">
        <?php $__currentLoopData = array_slice($importResult['errors'], 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li>صف <?php echo e($err['row'] ?? '—'); ?>: <?php echo e($err['message'] ?? ''); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<div class="mb-4 flex flex-wrap gap-2">
    <a href="<?php echo e(route('crm.clients.create', ['tab' => 'import'])); ?>"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold border-2 font-tajawal hover:bg-gray-50"
       style="border-color: <?php echo e($themeColor); ?>40; color: <?php echo e($themeColor); ?>;">
        استيراد من Excel / CSV
    </a>
    <a href="<?php echo e(route('crm.clients.import.template')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 font-tajawal">
        تنزيل قالب العملاء
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي العملاء', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />', 'href' => route('crm.clients.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عملاء محتملون', 'value' => $stats['prospect'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.clients.index', ['status' => 'prospect']) . '#page-data', 'linkLabel' => 'عرض المحتملين'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عملاء نشطون', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.clients.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'عرض النشطين'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'لديهم صفقات', 'value' => $stats['with_deals'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />', 'href' => route('crm.pipeline.index', ['has_deals' => '1']) . '#page-data', 'linkLabel' => 'عرض الصفقات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php echo $__env->make('crm.partials.filter-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('crm.clients.partials.bulk-actions', ['assignableReps' => $assignableReps ?? collect()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div id="page-data" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <h2 class="font-bold text-gray-900 font-tajawal">قائمة العملاء</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;"><?php echo e($clients->total()); ?> عميل</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 bg-gray-50/50">
                <tr class="text-gray-600">
                    <th class="p-4 w-10">
                        <?php if(auth()->user()->can('bulkDelete', \App\Models\Client::class) || auth()->user()->can('bulkUpdate', \App\Models\Client::class)): ?>
                        <input type="checkbox" id="client-bulk-check-all" class="rounded border-gray-300">
                        <?php endif; ?>
                    </th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">العميل</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">التواصل</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">التصنيف</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">المصدر</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">الحالة</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">الصفقات</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">المسؤول</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">أضافه</th>
                    <th class="text-right p-4 font-tajawal font-bold whitespace-nowrap">إجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-t border-gray-100 hover:bg-gray-50/80 transition-colors">
                    <td class="p-4 align-top">
                        <?php if(auth()->user()->can('bulkDelete', \App\Models\Client::class) || auth()->user()->can('bulkUpdate', \App\Models\Client::class)): ?>
                        <input type="checkbox" class="client-bulk-check rounded border-gray-300" value="<?php echo e($client->id); ?>"
                               data-name="<?php echo e($client->name); ?>" data-phone="<?php echo e($client->phone); ?>">
                        <?php endif; ?>
                    </td>
                    <td class="p-4">
                        <a href="<?php echo e(route('crm.clients.show', $client)); ?>" class="font-semibold text-gray-900 hover:underline font-tajawal"><?php echo e($client->name); ?></a>
                        <?php if($client->company_name): ?>
                            <div class="text-xs text-gray-500 mt-0.5 font-tajawal"><?php echo e($client->company_name); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="p-4">
                        <div class="text-gray-900 font-tajawal" dir="ltr"><?php echo e($client->phone); ?></div>
                        <?php if($client->email): ?>
                            <div class="text-xs text-gray-500 mt-0.5" dir="ltr"><?php echo e($client->email); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="p-4 font-tajawal whitespace-nowrap"><?php echo $__env->make('crm.clients.partials.type-badge', ['type' => $client->client_type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                    <td class="p-4 font-tajawal whitespace-nowrap"><?php echo $__env->make('crm.clients.partials.source-badge', ['source' => $client->lead_source], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                    <td class="p-4"><?php echo $__env->make('crm.clients.partials.status-badge', ['status' => $client->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                    <td class="p-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal"
                              style="background: <?php echo e($themeColor); ?>10; color: <?php echo e($themeColor); ?>;">
                            <?php echo e($client->sales->count()); ?> صفقة
                        </span>
                    </td>
                    <td class="p-4 text-gray-600 font-tajawal whitespace-nowrap">
                        <?php if($client->assignedEmployee): ?>
                            <?php echo e(trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name)); ?>

                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td class="p-4">
                        <?php echo $__env->make('crm.clients.partials.created-by', ['client' => $client], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="<?php echo e(route('crm.clients.show', $client)); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal hover:opacity-80"
                               style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;">عرض الملف</a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $client)): ?>
                            <a href="<?php echo e(route('crm.clients.edit', $client)); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 font-tajawal">تعديل</a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $client)): ?>
                            <?php if($client->sales->isEmpty()): ?>
                                <?php if($requiresApproval ?? false): ?>
                                    <?php echo $__env->make('crm.partials.delete-request-form', [
                                        'action' => route('crm.clients.destroy', $client),
                                        'label' => 'طلب حذف',
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php else: ?>
                                    <?php echo $__env->make('crm.partials.delete-request-form', [
                                        'action' => route('crm.clients.destroy', $client),
                                        'label' => 'حذف',
                                        'confirmMessage' => 'هل أنت متأكد من حذف هذا العميل؟',
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="10" class="p-12 text-center">
                        <div class="text-gray-400 font-tajawal mb-4">لا يوجد عملاء مطابقون للبحث</div>
                        <a href="<?php echo e(route('crm.clients.create')); ?>" class="inline-flex items-center px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                           style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                            إضافة أول عميل
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($clients->hasPages()): ?>
    <div class="p-4 sm:p-5 border-t border-gray-200"><?php echo e($clients->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\index.blade.php ENDPATH**/ ?>