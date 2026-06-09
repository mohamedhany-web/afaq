<?php $__env->startSection('page-title', $client->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $typeLabels = ['individual' => 'فرد', 'small_business' => 'شركة / منشأة'];
    $stageLabels = [
        'lead' => 'عميل محتمل',
        'prospect' => 'مهتم',
        'proposal' => 'عرض سعر',
        'negotiation' => 'تفاوض',
        'closed_won' => 'تم البيع',
        'closed_lost' => 'خسارة',
    ];
    $dealsCount = $client->sales->count();
    $dealsValue = $client->sales->sum('estimated_value');
    $canDelete = $client->sales->isEmpty() && $client->projects()->count() === 0;
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $client->name,
    'subtitle' => 'ملف العميل — ' . ($typeLabels[$client->client_type] ?? 'فرد'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('crm.pipeline.create', ['client_id' => $client->id]),
    'actionLabel' => 'صفقة جديدة',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الصفقات', 'value' => $dealsCount, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قيمة الصفقات', 'value' => $money($dealsValue), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الحالة', 'value' => match($client->status) { 'prospect' => 'محتمل', 'active' => 'نشط', 'inactive' => 'غير نشط', 'suspended' => 'موقوف', default => $client->status }, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تاريخ التسجيل', 'value' => $client->created_at->format('Y/m/d'), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php echo $__env->make('crm.clients.partials.journey-kanban', compact('client', 'stageLabels', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('crm.clients.partials.unified-timeline', compact('client', 'timeline', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 w-full">
    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
             style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            بيانات التواصل
        </div>
        <div class="p-5 sm:p-6 space-y-4">
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">الهاتف</dt>
                <dd class="font-medium text-gray-900 font-tajawal" dir="ltr"><?php echo e($client->phone); ?></dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">البريد الإلكتروني</dt>
                <dd class="text-gray-900 font-tajawal" dir="ltr"><?php echo e($client->email ?? '—'); ?></dd>
            </div>
            <?php if($client->company_name): ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">الشركة</dt>
                <dd class="text-gray-900 font-tajawal"><?php echo e($client->company_name); ?></dd>
            </div>
            <?php endif; ?>
            <?php if($client->address): ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">العنوان</dt>
                <dd class="text-gray-900 font-tajawal"><?php echo e($client->address); ?></dd>
            </div>
            <?php endif; ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">الحالة</dt>
                <dd><?php echo $__env->make('crm.clients.partials.status-badge', ['status' => $client->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></dd>
            </div>
            <?php if($client->assignedEmployee): ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">مسؤول المبيعات</dt>
                <dd class="text-gray-900 font-tajawal"><?php echo e(trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name)); ?></dd>
            </div>
            <?php endif; ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">من أضاف العميل</dt>
                <dd><?php echo $__env->make('crm.clients.partials.created-by', ['client' => $client], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></dd>
            </div>
            <?php if($client->notes): ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">ملاحظات</dt>
                <dd class="text-gray-700 text-sm font-tajawal whitespace-pre-line"><?php echo e($client->notes); ?></dd>
            </div>
            <?php endif; ?>
        </div>
        <div class="px-5 sm:px-6 py-4 border-t border-gray-100 flex flex-wrap gap-2">
            <a href="<?php echo e(route('crm.clients.edit', $client)); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal text-white"
               style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">تعديل البيانات</a>
            <a href="<?php echo e(route('crm.pipeline.client', $client)); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal border-2 font-medium"
               style="border-color: <?php echo e($themeColor); ?>40; color: <?php echo e($themeColor); ?>;">فتح في المسار</a>
            <a href="<?php echo e(route('crm.pipeline.index')); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal border-2 border-gray-200 text-gray-600 hover:bg-gray-50">قائمة المسار</a>
            <a href="<?php echo e(route('crm.clients.index')); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal border-2 border-gray-200 text-gray-600 hover:bg-gray-50">كل العملاء</a>
            <?php if($canDelete): ?>
            <form action="<?php echo e(route('crm.clients.destroy', $client)); ?>" method="POST" class="inline"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal bg-red-50 text-red-600 hover:bg-red-100">حذف العميل</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
             style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            <h3 class="font-bold text-gray-900 font-tajawal">صفقات العميل</h3>
            <a href="<?php echo e(route('crm.pipeline.create', ['client_id' => $client->id])); ?>" class="text-xs font-semibold font-tajawal px-3 py-1.5 rounded-lg"
               style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;">+ صفقة جديدة</a>
        </div>
        <div class="p-5 sm:p-6">
            <?php $__empty_1 = true; $__currentLoopData = $client->sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('crm.pipeline.show', $sale)); ?>" class="block p-4 mb-3 last:mb-0 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-gray-50/80 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900 font-tajawal truncate"><?php echo e($sale->product_service); ?></div>
                            <?php if($sale->project): ?>
                                <div class="text-xs text-gray-500 mt-1 font-tajawal"><?php echo e($sale->project->name); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal bg-gray-100 text-gray-700">
                                <?php echo e($stageLabels[$sale->stage] ?? $sale->stage); ?>

                            </span>
                            <span class="font-bold text-sm font-tajawal" style="color: <?php echo e($themeColor); ?>;"><?php echo e($money($sale->estimated_value)); ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-10">
                    <p class="text-gray-400 font-tajawal mb-4">لا توجد صفقات لهذا العميل بعد</p>
                    <a href="<?php echo e(route('crm.pipeline.create', ['client_id' => $client->id])); ?>" class="inline-flex items-center px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                       style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                        إنشاء أول صفقة
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php echo $__env->make('crm.partials.lost-reason-modal', ['lostReasons' => $lostReasons ?? config('crm_intelligence.lost_reasons')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\show.blade.php ENDPATH**/ ?>