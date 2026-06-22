<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $statusColors = config('project_units.status_colors', []);
    $statusLabels = config('project_units.statuses', []);
    $useColors = config('project_units.use_colors', []);
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden font-tajawal" id="page-data">
    <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, transparent 100%);">
        <div>
            <h2 class="font-bold text-gray-900">وحدات المخزون</h2>
            <p class="text-xs text-gray-500 mt-1">انقر رقم الوحدة أو «عرض التفاصيل» لفتح بيانات الوحدة الكاملة في المشروع</p>
        </div>
        <span class="text-xs text-gray-400"><?php echo e($units->total()); ?> وحدة</span>
    </div>

    <div class="px-5 py-4 border-b bg-gray-50">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = ['' => 'الكل', 'available' => 'متاحة', 'reserved' => 'محجوزة', 'sold' => 'مباعة']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('operations.inventory.index', array_filter(['status' => $key ?: null, 'project_id' => request('project_id'), 'use_type' => request('use_type'), 'search' => request('search')]))); ?>#page-data"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold border <?php echo e(($statusFilter ?? '') === $key || (!$statusFilter && $key === '') ? 'text-white border-transparent' : 'bg-white text-gray-600 border-gray-200'); ?>"
                   <?php if(($statusFilter ?? '') === $key || (!$statusFilter && $key === '')): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>
                    <?php echo e($label); ?>

                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-500 mb-1">المشروع</label>
                <select name="project_id" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="">كل المشاريع</option>
                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($project->id); ?>" <?php if(request('project_id') == $project->id): echo 'selected'; endif; ?>><?php echo e($project->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-500 mb-1">تصنيف الوحدة</label>
                <select name="use_type" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="">كل التصنيفات</option>
                    <?php $__currentLoopData = $useTypeLabels ?? config('project_units.use_types', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php if(($useTypeFilter ?? '') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-[10px] font-bold text-gray-500 mb-1">بحث</label>
                <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="رقم الوحدة أو المشروع..."
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
            </div>
            <?php if($statusFilter): ?>
            <input type="hidden" name="status" value="<?php echo e($statusFilter); ?>">
            <?php endif; ?>
            <?php if(request('use_type')): ?>
            <input type="hidden" name="use_type" value="<?php echo e(request('use_type')); ?>">
            <?php endif; ?>
            <button type="submit" class="px-5 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">بحث</button>
        </form>
    </div>

    <div class="p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            <?php $__empty_1 = true; $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $statusColor = $statusColors[$unit->status] ?? '#6b7280';
                $useColor = $useColors[$unit->use_type] ?? $themeColor;
                $detailUrl = route('crm.projects.show', $unit->project_id) . '?unit=' . $unit->id . '#building-units-root';
                $price = $unit->price_cash > 0 ? $unit->price_cash : ($unit->price_installment > 0 ? $unit->price_installment : null);
            ?>
            <article class="rounded-xl border-2 border-gray-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">
                <div class="px-3 py-2 border-b flex items-center justify-between gap-2" style="background:<?php echo e($useColor); ?>10">
                    <a href="<?php echo e($detailUrl); ?>" class="font-extrabold text-sm hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($unit->code); ?></a>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full text-white" style="background:<?php echo e($statusColor); ?>"><?php echo e($statusLabels[$unit->status] ?? $unit->statusLabel()); ?></span>
                </div>
                <div class="p-3 flex-1 text-sm space-y-1">
                    <p class="font-semibold text-gray-800 truncate"><?php echo e($unit->project?->name ?? '—'); ?></p>
                    <p class="text-xs text-gray-500"><?php echo e($unit->useTypeLabel()); ?> · <?php echo e($unit->area_m2); ?> م²</p>
                    <?php if($unit->floor): ?>
                    <p class="text-xs text-gray-400"><?php echo e($unit->floor->label ?? ('طابق ' . $unit->floor->level)); ?></p>
                    <?php endif; ?>
                    <p class="text-sm font-bold text-gray-900"><?php echo e($price ? $money($price) : 'بدون سعر'); ?></p>
                </div>
                <div class="px-3 py-2 border-t">
                    <a href="<?php echo e($detailUrl); ?>" class="inline-flex items-center gap-1 text-xs font-bold hover:underline" style="color:<?php echo e($themeColor); ?>">
                        عرض التفاصيل
                        <svg class="w-3 h-3 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-12 text-center text-gray-500 text-sm">لا توجد وحدات مطابقة للفلتر</div>
            <?php endif; ?>
        </div>
        <?php if($units->hasPages()): ?>
        <div class="mt-5 pt-4 border-t"><?php echo e($units->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\partials\unit-inventory-cards.blade.php ENDPATH**/ ?>