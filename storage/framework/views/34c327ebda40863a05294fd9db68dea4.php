
<?php $__env->startSection('page-title', 'المطورون العقاريون'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $statusLabels = \App\Models\RealEstateDeveloper::STATUSES;
    $canManage = auth()->user()?->can('manage-developers');
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'المطورون العقاريون والتعاقدات',
    'subtitle' => 'إدارة التعاقدات وبوابة المطور — المشاريع تختار المطور المسجّل من هنا فقط',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
    'actionUrl' => $canManage ? route('admin.developers.create') : null,
    'actionLabel' => 'مطور وتعاقد جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي المطورين', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تعاقدات نشطة', 'value' => $stats['contracted'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'بوابات مفعّلة', 'value' => $stats['portal_ready'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'المشاريع المرتبطة', 'value' => $stats['projects'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="الاسم، البريد، الهاتف، أو المدينة..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($val); ?>" <?php if(request('status') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="w-full lg:w-44">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">التعاقد</label>
            <select name="contract" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <option value="active" <?php if(request('contract') === 'active'): echo 'selected'; endif; ?>>تعاقد نشط</option>
                <option value="none" <?php if(request('contract') === 'none'): echo 'selected'; endif; ?>>بدون تعاقد</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal shadow-sm"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
            <?php if(request()->hasAny(['search', 'status', 'contract'])): ?>
            <a href="<?php echo e(route('admin.developers.index')); ?>" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between" style="<?php echo e($headerStyle); ?>">
        <h2 class="font-bold text-gray-900 font-tajawal">قائمة المطورين</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;"><?php echo e($developers->total()); ?> مطور</span>
    </div>

    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-sm min-w-[900px]">
            <thead class="border-b border-gray-200 bg-gray-50/80">
                <tr class="text-gray-600">
                    <th class="text-right p-4 font-tajawal font-bold">المطور</th>
                    <th class="text-right p-4 font-tajawal font-bold">التواصل</th>
                    <th class="text-right p-4 font-tajawal font-bold">التعاقد</th>
                    <th class="text-right p-4 font-tajawal font-bold">المشاريع</th>
                    <th class="text-right p-4 font-tajawal font-bold">البوابة</th>
                    <th class="text-right p-4 font-tajawal font-bold">الحالة</th>
                    <th class="text-right p-4 font-tajawal font-bold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $developers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $initial = mb_substr($d->name, 0, 1);
                    $st = $statusLabels[$d->status] ?? '—';
                    $stClass = $d->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600';
                ?>
                <tr class="hover:bg-gray-50/80 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0 font-tajawal"
                                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);"><?php echo e($initial); ?></div>
                            <div class="min-w-0">
                                <a href="<?php echo e(route('admin.developers.show', $d)); ?>" class="font-semibold text-gray-900 hover:underline font-tajawal block truncate"><?php echo e($d->name); ?></a>
                                <?php if($d->city): ?><span class="text-xs text-gray-400 font-tajawal"><?php echo e($d->city); ?></span><?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="text-xs text-gray-600 font-tajawal" dir="ltr"><?php echo e($d->phone ?: '—'); ?></div>
                        <div class="text-xs text-gray-400 font-tajawal truncate max-w-[180px]" dir="ltr"><?php echo e($d->email ?: '—'); ?></div>
                    </td>
                    <td class="p-4">
                        <?php if($d->activeContract): ?>
                        <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal bg-emerald-100 text-emerald-800">نشط</span>
                        <?php if($d->activeContract->commission_percent): ?>
                        <div class="text-xs text-gray-500 mt-1 font-tajawal tabular-nums"><?php echo e($d->activeContract->commission_percent); ?>% عمولة</div>
                        <?php endif; ?>
                        <?php else: ?>
                        <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal bg-amber-100 text-amber-800">بدون تعاقد</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4 font-semibold text-gray-900 font-tajawal tabular-nums"><?php echo e($d->projects_count); ?></td>
                    <td class="p-4">
                        <?php if($d->isPortalReady()): ?>
                        <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal bg-green-100 text-green-800">مفعّلة</span>
                        <?php elseif($d->portal_enabled): ?>
                        <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal bg-amber-100 text-amber-800">بدون حساب</span>
                        <?php else: ?>
                        <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal bg-gray-100 text-gray-600">موقوفة</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4">
                        <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal <?php echo e($stClass); ?>"><?php echo e($st); ?></span>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-wrap gap-1.5">
                            <a href="<?php echo e(route('admin.developers.show', $d)); ?>"
                               class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-white font-tajawal"
                               style="background:<?php echo e($themeColor); ?>">عرض</a>
                            <?php if($canManage): ?>
                            <a href="<?php echo e(route('admin.developers.edit', $d)); ?>"
                               class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 font-tajawal">تعديل</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="p-12 text-center">
                        <p class="text-gray-400 font-tajawal mb-4">لا يوجد مطورون مطابقون</p>
                        <?php if($canManage): ?>
                        <a href="<?php echo e(route('admin.developers.create')); ?>"
                           class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                           style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">إضافة أول مطور</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($developers->hasPages()): ?>
    <div class="p-4 border-t border-gray-100 hidden md:block"><?php echo e($developers->links()); ?></div>
    <?php endif; ?>
</div>


<div class="md:hidden mt-4 space-y-3">
    <?php $__empty_1 = true; $__currentLoopData = $developers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0 font-tajawal"
                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);"><?php echo e(mb_substr($d->name, 0, 1)); ?></div>
            <div class="flex-1 min-w-0">
                <a href="<?php echo e(route('admin.developers.show', $d)); ?>" class="font-bold text-gray-900 font-tajawal block truncate"><?php echo e($d->name); ?></a>
                <p class="text-xs text-gray-500 mt-1 font-tajawal"><?php echo e($d->city ?: '—'); ?> · <?php echo e($d->projects_count); ?> مشروع</p>
            </div>
        </div>
        <div class="flex gap-2 mt-3">
            <a href="<?php echo e(route('admin.developers.show', $d)); ?>" class="flex-1 text-center py-2 rounded-lg text-xs font-bold text-white font-tajawal" style="background:<?php echo e($themeColor); ?>">عرض</a>
            <?php if($canManage): ?>
            <a href="<?php echo e(route('admin.developers.edit', $d)); ?>" class="px-4 py-2 rounded-lg text-xs font-semibold border border-gray-200 font-tajawal">تعديل</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center text-gray-400 font-tajawal text-sm">لا يوجد مطورون</div>
    <?php endif; ?>
    <?php if($developers->hasPages()): ?>
    <div class="pt-2"><?php echo e($developers->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\admin\developers\index.blade.php ENDPATH**/ ?>