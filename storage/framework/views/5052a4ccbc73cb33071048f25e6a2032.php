<?php $__env->startSection('page-title', 'المشاريع العقارية'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'المشاريع العقارية',
    'subtitle' => 'إدارة كتالوج المشاريع والوحدات المتاحة للبيع',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
    'actionUrl' => auth()->user()->can('create-projects') ? route('projects.create') : null,
    'actionLabel' => 'مشروع جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 w-full">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي المشاريع', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متاح للبيع', 'value' => $stats['active_listings'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قريباً', 'value' => $stats['upcoming'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'وحدات متاحة', 'value' => number_format($stats['available_units']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php if(!empty($stats['ownership'])): ?>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6 w-full">
    <?php $__currentLoopData = $stats['ownership']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between font-tajawal">
        <div>
            <p class="text-xs text-gray-500"><?php echo e($row['label']); ?></p>
            <p class="text-xl font-bold text-gray-900"><?php echo e($row['count']); ?> <span class="text-sm font-normal text-gray-400">مشروع</span></p>
        </div>
        <span class="text-xs text-gray-400"><?php echo e(number_format($row['units'])); ?> وحدة</span>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6 w-full">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 lg:items-end">
        <div class="lg:col-span-2">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم المشروع، المدينة، المطور..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">حالة العرض</label>
            <select name="listing_status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = \App\Models\Project::LISTING_STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php if(request('listing_status') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">نوع العقار</label>
            <select name="property_type" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = \App\Models\Project::PROPERTY_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php if(request('property_type') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">نوع الملكية</label>
            <select name="ownership_type" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = \App\Models\Project::OWNERSHIP_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php if(request('ownership_type') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2 lg:col-span-2">
            <button type="submit" class="flex-1 px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
            <?php if(request()->hasAny(['search', 'listing_status', 'property_type', 'city'])): ?>
            <a href="<?php echo e(route('projects.index')); ?>" class="px-4 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6 w-full">
    <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <a href="<?php echo e(route('projects.show', $project)); ?>"
       class="bg-white rounded-2xl border border-gray-200 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden block group">
        <div class="p-5 sm:p-6">
            <div class="flex items-start justify-between gap-2 mb-2">
                <h3 class="font-bold text-lg text-gray-900 font-tajawal group-hover:opacity-90 line-clamp-2"><?php echo e($project->name); ?></h3>
                <div class="flex flex-col items-end gap-1 shrink-0">
                    <?php echo $__env->make('projects.partials.listing-badge', ['status' => $project->listing_status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('projects.partials.ownership-badge', ['type' => $project->ownership_type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
            <?php if($project->ownership_type === 'developer_third_party' && $project->displayDeveloperName() !== '—'): ?>
            <p class="text-xs text-emerald-700 font-tajawal"><?php echo e($project->displayDeveloperName()); ?></p>
            <?php endif; ?>
            <p class="text-sm text-gray-500 font-tajawal">
                <?php echo e($project->city); ?><?php if($project->location): ?> — <?php echo e($project->location); ?><?php endif; ?>
            </p>
            <p class="text-xs text-gray-400 mt-1 font-tajawal"><?php echo e($project->property_type_name); ?> · <?php echo e($project->development_type_name); ?></p>
            <p class="font-bold mt-3 text-lg font-tajawal" style="color: <?php echo e($themeColor); ?>;">
                <?php echo e(\App\Helpers\SettingsHelper::formatMoney($project->price_from)); ?>

                <?php if($project->price_to): ?> — <?php echo e(\App\Helpers\SettingsHelper::formatMoney($project->price_to)); ?><?php endif; ?>
            </p>
            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 font-tajawal">
                <span><?php echo e($project->available_units ?? 0); ?> متاح من <?php echo e($project->total_units ?? 0); ?></span>
                <span><?php echo e($project->sales_count); ?> صفقة</span>
            </div>
            <?php if($project->total_units > 0): ?>
            <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full" style="width: <?php echo e($project->occupancy_percent); ?>%; background: <?php echo e($themeColor); ?>;"></div>
            </div>
            <?php endif; ?>
        </div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-span-full bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-400 font-tajawal">
        لا توجد مشاريع عقارية. <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-projects')): ?><a href="<?php echo e(route('projects.create')); ?>" class="underline" style="color: <?php echo e($themeColor); ?>;">أضف أول مشروع</a><?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php if($projects->hasPages()): ?>
<div class="mt-6 w-full"><?php echo e($projects->links()); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/projects/index.blade.php ENDPATH**/ ?>